<?php

namespace Detail\Auth\Identity\Adapter;

use ThreeScaleResponse;
use Zend\Cache\Storage\StorageInterface as CacheStorage;

use ThreeScaleAuthorizeResponse;
use ThreeScaleClient;
use ThreeScaleServerError;

use Detail\Auth\Identity\Exception;
use Detail\Auth\Identity\Identity;
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
     * @var string
     */
    protected $defaultRole;

    /**
     * @param ThreeScaleClient $client
     * @param string $serviceId
     * @param array $credentialsHeaders
     * @param CacheStorage $cache
     * @param boolean $usePlanAsRole
     * @param string $defaultRole
     */
    public function __construct(
        ThreeScaleClient $client,
        $serviceId,
        array $credentialsHeaders,
        CacheStorage $cache = null,
        $usePlanAsRole = true,
        $defaultRole = null
    ) {
        $this->setClient($client);
        $this->setServiceId($serviceId);
        $this->setCredentialHeaders($credentialsHeaders);
        $this->setUsePlanAsRole($usePlanAsRole);
        $this->setDefaultRole($defaultRole);

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
     * @param string $defaultRole
     */
    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;
    }

    /**
     * @return string
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * @return Result
     */
    protected function auth()
    {
        $cache = $this->getCache();
        $cacheKey = $this->getCacheKey();

        // The application might already be authenticated
        if ($cache !== null && $cacheKey !== null && $cache->hasItem($cacheKey)) {
            /** @todo We should silently fail when cache is unavailable */
            $identity = new Identity($cache->getItem($cacheKey));
            return new Result(true, $identity);
        }

        $usage = array('hits' => 1);

        // When credentials are missing, we're just returning an unsuccessful response
        try {
            $response = $this->authorize($usage);
        } catch (Exception\CredentialMissingException $e) {
            return new Result(false, null, array($e->getMessage()));
        }

        /** @todo Use MvcEvent listener to log calls in background (using an IronMQ queue) */

        if (!$response->isSuccess()) {
            return new Result(false, null, array($response->getErrorMessage()));
        }

        $role = $this->getAssignedRole($response);

        if ($role === null) {
            return new Result(false, null, array('No role assigned'));
        }

        $identity = new Identity($role);

        if ($cache !== null && $cacheKey !== null) {
            /** @todo We should silently fail when cache is unavailable */
            $cache->setItem($cacheKey, $role);
        }

        return new Result($response->isSuccess(), $identity);
    }

    /**
     * Actually authorize by querying 3scale.
     *
     * @param array $usage
     * @return \ThreeScaleResponse
     */
    protected function authorize(array $usage = null)
    {
        $appId = $this->getCredential(self::CREDENTIAL_APPLICATION_ID);
        $appKey = $this->getCredential(self::CREDENTIAL_APPLICATION_KEY);

        $client = $this->getClient();

        try {
            $response = @$client->authorize(
                $appId,
                $appKey,
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

        return $response;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getCredential($name)
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

        $header  = $this->getCredentialHeader($name);
        $credential  = $request->getHeader($header);

        if (!$credential) {
            throw new Exception\CredentialMissingException(
                sprintf(
                    'Missing authentication credential "%s"; provide it using the "%s" header',
                    $name,
                    $header
                )
            );
        }

        return $credential->getFieldValue();
    }

    /**
     * @return string|null
     */
    protected function getCacheKey()
    {
        // Of course, we are using the application identifier as cache key.
        try {
            $appId = $this->getCredential(self::CREDENTIAL_APPLICATION_ID);
        } catch (Exception\CredentialMissingException $e) {
            $appId = null;
        }

        return $appId;
    }

    /**
     * @param ThreeScaleResponse $response
     * @return string
     */
    protected function getAssignedRole(ThreeScaleResponse $response)
    {
        $role = $this->getDefaultRole();

        if ($response instanceof ThreeScaleAuthorizeResponse && $this->usePlanAsRole()) {
            $plan = $response->getPlan();

            if ($plan) {
                $role = '3scale-' . $plan;
            }
        }

        return $role;
    }
}
