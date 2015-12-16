<?php

namespace Detail\Auth\Identity\Adapter;

use Zend\Authentication\Adapter as AuthenticationAdapter;
use Zend\Authentication\Result as AuthenticationResult;
//use Zend\Cache\Storage\StorageInterface as CacheStorage;
//use Zend\EventManager\EventsCapableInterface;

use Detail\Auth\Identity\Exception;
use Detail\Auth\Identity\Identity;
use Detail\Auth\Identity\AuthenticationResult as Result;
use Detail\Auth\Service\HttpRequestAwareInterface;
use Detail\Auth\Service\HttpRequestAwareTrait;

class AuthenticationAdapterAdapter extends BaseAdapter implements
    HttpRequestAwareInterface
{
    use HttpRequestAwareTrait;

    const CREDENTIAL_APPLICATION_ID = 'app_id';
    const CREDENTIAL_APPLICATION_KEY = 'app_key';

    /**
     * @var AuthenticationAdapter\ValidatableAdapterInterface
     */
    protected $authenticationAdapter;

    /**
     * @var string[]
     */
    protected $credentialHeaders;

//    /**
//     * @var CacheStorage
//     */
//    protected $cache;

    /**
     * @param AuthenticationAdapter\ValidatableAdapterInterface $authenticationAdapter
     * @param array $credentialsHeaders
     */
    public function __construct(
        AuthenticationAdapter\ValidatableAdapterInterface $authenticationAdapter,
        array $credentialsHeaders
//        CacheStorage $cache = null
    ) {
        $this->setAuthenticationAdapter($authenticationAdapter);
        $this->setCredentialHeaders($credentialsHeaders);

//        if ($cache !== null) {
//            $this->setCache($cache);
//        }
    }

    /**
     * @return AuthenticationAdapter\ValidatableAdapterInterface
     */
    public function getAuthenticationAdapter()
    {
        return $this->authenticationAdapter;
    }

    /**
     * @param AuthenticationAdapter\ValidatableAdapterInterface $authenticationAdapter
     */
    public function setAuthenticationAdapter(
        AuthenticationAdapter\ValidatableAdapterInterface $authenticationAdapter
    ) {
        $this->authenticationAdapter = $authenticationAdapter;
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

//    /**
//     * @return CacheStorage
//     */
//    public function getCache()
//    {
//        return $this->cache;
//    }
//
//    /**
//     * @param CacheStorage $cache
//     */
//    public function setCache(CacheStorage $cache)
//    {
////        if ($cache instanceof EventsCapableInterface) {
////            $events = $cache->getEventManager();
////
////            // We are interested in the "hasItem.exception" and "getItem.exception" events...
////        }
//
//        $this->cache = $cache;
//    }

    /**
     * @return Result
     */
    protected function auth()
    {
//        $cache = $this->getCache();
//        $cacheKey = $this->getCacheKey();
//
//        // The application might already be authenticated
//        if ($cache !== null && $cacheKey !== null && $cache->hasItem($cacheKey)) {
//            $identity = new Identity($cache->getItem($cacheKey));
//            return $this->createResult(true, $identity);
//        }

        // When credentials are missing, we're just returning an unsuccessful response
        try {
            $result = $this->authorize();
        } catch (Exception\CredentialMissingException $e) {
            return $this->createResult(false, null, array($e->getMessage()));
        }

        $identity = $result->isValid() ? $result->getIdentity() : null;

//        if ($cache !== null && $cacheKey !== null) {
//            $cache->setItem($cacheKey, $role);
//        }

        return $this->createResult($result->isValid(), $identity, $result->getMessages());
    }

    /**
     * Actually authorize .
     *
     * @return AuthenticationResult
     */
    protected function authorize()
    {
        $appId = $this->getCredential(self::CREDENTIAL_APPLICATION_ID);
        $appKey = $this->getCredential(self::CREDENTIAL_APPLICATION_KEY);

        $authenticationAdapter = $this->getAuthenticationAdapter();
        $authenticationAdapter->setIdentity($appId);
        $authenticationAdapter->setCredential($appKey);

        try {
            $result = $this->getAuthenticationAdapter()->authenticate();
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

        return $result;
    }

    /**
     * @param string $name
     * @param $failOnNull
     * @return string
     */
    protected function getCredential($name, $failOnNull = true)
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
            if ($failOnNull === false) {
                return null;
            }

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

//    /**
//     * @return string|null
//     */
//    protected function getCacheKey()
//    {
//        // We are basing the key on the credentials.
//        // So only when both application identifier and key are provided and are correct,
//        // the cache applies.
//        try {
//            $cacheString = sprintf(
//                'auth.%s-%s',
//                $this->getCredential(self::CREDENTIAL_APPLICATION_ID),
//                $this->getCredential(self::CREDENTIAL_APPLICATION_KEY)
//            );
//
//            // We're balancing speed and security (should only take a few milli seconds)...
//            $cacheKey = hash('sha256', $cacheString);
//
//        } catch (Exception\CredentialMissingException $e) {
//            $cacheKey = null;
//        }
//
//        return $cacheKey;
//    }

    /**
     * @param boolean $success
     * @param Identity|null $identity
     * @param array $messages
     * @return Result
     */
    private function createResult(
        $success,
        $identity = null,
        array $messages = array()
    ) {
        $appId  = $this->getCredential(self::CREDENTIAL_APPLICATION_ID, false);
        $appKey = $this->getCredential(self::CREDENTIAL_APPLICATION_KEY, false);

        return new Result($success, $identity, $messages, $appId, $appKey);
    }
}
