<?php

namespace Detail\Auth\Identity\Adapter;

use Zend\Cache\Storage\StorageInterface as CacheStorage;

use ThreeScaleAuthorizeResponse;
use ThreeScaleClient;
use ThreeScaleServerError;

use Detail\Auth\Identity\Exception;
use Detail\Auth\Identity\Identity;
use Detail\Auth\Identity\ResultInterface;
use Detail\Auth\Identity\Result;
use Detail\Auth\Service\HttpRequestAwareInterface;
use Detail\Auth\Service\HttpRequestAwareTrait;

class ThreeScaleAdapter extends BaseAdapter implements
    HttpRequestAwareInterface
{
    use HttpRequestAwareTrait;

    const CREDENTIAL_APPLICATION_ID = 'app_id';
    const CREDENTIAL_APPLICATION_KEY = 'app_key';

    /**
     * @var ThreeScaleClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $serviceId;

    /**
     * @var string[]
     */
    protected $credentialHeaders;

    /**
     * @var CacheStorage
     */
    protected $cache;

    /**
     * @var boolean
     */
    protected $usePlanAsRole;

    /**
     * @param ThreeScaleClient $client
     * @param string $serviceId
     * @param array $credentialsHeaders
     * @param CacheStorage $cache
     * @param boolean $usePlanAsRole
     */
    public function __construct(
        ThreeScaleClient $client,
        $serviceId,
        array $credentialsHeaders,
        CacheStorage $cache = null,
        $usePlanAsRole = true
    ) {
        $this->setClient($client);
        $this->setServiceId($serviceId);
        $this->setCredentialHeaders($credentialsHeaders);
        $this->setUsePlanAsRole($usePlanAsRole);

        if ($cache !== null) {
            $this->setCache($cache);
        }
    }

    /**
     * @return ThreeScaleClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ThreeScaleClient $client
     */
    public function setClient(ThreeScaleClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param string $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getCredentialHeader($name)
    {
        return $this->credentialHeaders[$name];
    }

    /**
     * @return string[]
     */
    public function getCredentialHeaders()
    {
        return $this->credentialHeaders;
    }

    /**
     * @return CacheStorage
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param CacheStorage $cache
     */
    public function setCache(CacheStorage $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return boolean
     */
    public function usePlanAsRole()
    {
        return $this->usePlanAsRole;
    }

    /**
     * @param boolean $usePlanAsRole
     */
    public function setUsePlanAsRole($usePlanAsRole)
    {
        $this->usePlanAsRole = $usePlanAsRole;
    }

    /**
     * @param string[] $credentialHeaders
     */
    public function setCredentialHeaders(array $credentialHeaders)
    {
        $requiredCredentials = array(
            self::CREDENTIAL_APPLICATION_ID,
            self::CREDENTIAL_APPLICATION_KEY,
        );

        $missingCredentials = array_diff($requiredCredentials, array_keys($credentialHeaders));

        if (count($missingCredentials) > 0) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Invalid credential headers; missing "%s"',
                    implode('", "', $missingCredentials)
                )
            );
        }

        $this->credentialHeaders = $credentialHeaders;
    }

    /**
     * @return Result
     */
    protected function auth()
    {
        $cache = $this->getCache();
        $credentials = $this->getCredentials();

        if ($credentials instanceof ResultInterface) {
            return $credentials;
        }

        $appId = $credentials[self::CREDENTIAL_APPLICATION_ID];

        // The application might already be authenticated
        if ($cache->hasItem($appId)) {
            /** @todo We should silently fail when cache is unavailable */
            $identity = new Identity($cache->getItem($appId));
            return new Result(true, $identity);
        }

        $usage = array('hits' => 1);
        $client = $this->getClient();

        try {
            $response = @$client->authorize(
                $credentials[self::CREDENTIAL_APPLICATION_ID],
                $credentials[self::CREDENTIAL_APPLICATION_KEY],
                $this->getServiceId(),
                $usage
            );
        } catch (ThreeScaleServerError $e) {
            throw new Exception\AuthenticationUnavailableException(
                sprintf(
                    'Failed to authenticate because 3scale seems to be unavailable: %s',
                    $e->getMessage()
                ),
                0,
                $e
            );
        } catch (\Exception $e) {
            throw new Exception\AuthenticationFailedException(
                sprintf(
                    'Failed to authenticate: %s',
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        /** @todo Use MvcEvent listener to log calls in background (using an IronMQ queue) */

        $messages = array();
        $plan = null;
        $role = null;

        if (!$response->isSuccess()) {
            $messages[(string) $response->getErrorCode()] = $response->getErrorMessage();
        }

        if ($response instanceof ThreeScaleAuthorizeResponse) {
            $plan = $response->getPlan();

            if ($this->usePlanAsRole()) {
                $role = '3scale-' . $plan;
            }
        }

        if ($role === null) {
            throw new Exception\AuthenticationFailedException(
                'Failed to authenticate: No role assigned'
            );
        }

        $identity = new Identity($role);

        if ($cache !== null) {
            /** @todo We should silently fail when cache is unavailable */
           $cache->setItem($appId, $role);
        }

        return new Result($response->isSuccess(), $identity, $messages);
    }

    /**
     * @return array|Result
     */
    protected function getCredentials()
    {
        $request = $this->getRequest();

        if ($request === null) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Request object must be set before calling %s::authenticate()',
                    get_class($this)
                )
            );
        }

        $appIdHeader  = $this->getCredentialHeader(self::CREDENTIAL_APPLICATION_ID);
        $appKeyHeader = $this->getCredentialHeader(self::CREDENTIAL_APPLICATION_KEY);

        $appId  = $request->getHeader($appIdHeader);
        $appKey = $request->getHeader($appKeyHeader);

        $messages = array();

        if (!$appId) {
            $messages[] = sprintf(
                'Missing application identifier; provide one using the "%s" header',
                $appIdHeader
            );
        }

        if (!$appKey) {
            $messages[] = sprintf(
                'Missing application key; provide one using the "%s" header',
                $appKeyHeader
            );
        }

        if (count($messages) > 0) {
            return new Result(false, null, $messages);
        }

        return array(
            self::CREDENTIAL_APPLICATION_ID  => $appId->getFieldValue(),
            self::CREDENTIAL_APPLICATION_KEY => $appKey->getFieldValue(),
        );
    }
}
