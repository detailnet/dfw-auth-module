<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\Cache\Storage\StorageInterface as CacheStorage;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\ThreeScaleAdapter as Adapter;
use Detail\Auth\Options\Identity\IdentityOptions;

class ThreeScaleAdapterFactory extends BaseAdapterFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param IdentityOptions $identityOptions
     * @return Adapter
     */
    protected function createAdapter(
        ServiceLocatorInterface $serviceLocator,
        IdentityOptions $identityOptions
    ) {
        /** @var \Detail\Auth\Options\Identity\Adapter\ThreeScaleAdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            Adapter::CLASS,
            'Detail\Auth\Options\Identity\Adapter\ThreeScaleAdapterOptions'
        );

        /** @var \Detail\Auth\Options\ThreeScaleOptions $threeScaleOptions */
        $threeScaleOptions = $serviceLocator->get('Detail\Auth\Options\ThreeScaleOptions');

        $clientClass = $adapterOptions->getClient();

        if (!$clientClass) {
            throw new ConfigException('Missing 3scale client class');
        }

        /** @var \ThreeScaleClient $client */
        $client = $serviceLocator->get($clientClass);

        $cache = $adapterOptions->getCache();

        if ($cache !== null) {
            $cache = $this->createCache($serviceLocator, $cache);
        }

        $credentialHeaders = array(
            Adapter::CREDENTIAL_APPLICATION_ID  => $adapterOptions->getAppIdHeader(),
            Adapter::CREDENTIAL_APPLICATION_KEY => $adapterOptions->getAppKeyHeader(),
        );

        $adapter = new Adapter(
            $client,
            $threeScaleOptions->getServiceId(),
            $credentialHeaders,
            $cache
        );

        return $adapter;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $cacheName
     * @return CacheStorage
     */
    public function createCache(ServiceLocatorInterface $serviceLocator, $cacheName)
    {
        if (!is_string($cacheName) || strlen($cacheName) == 0) {
            throw new ConfigException(
                sprintf(
                    '%s requires a valid service name for configuration "cache"',
                    Adapter::CLASS
                )
            );
        }

        if (!$serviceLocator->has($cacheName)) {
            throw new ConfigException(
                sprintf(
                    '%s requires service "%s"; service does not exist',
                    Adapter::CLASS,
                    $cacheName
                )
            );
        }

        $cache = $serviceLocator->get($cacheName);

        if (!$cache instanceof CacheStorage) {
            throw new ConfigException(
                sprintf(
                    '%s requires service "%s" to be of type %s; received "%s"',
                    Adapter::CLASS,
                    $cacheName,
                    CacheStorage::CLASS,
                    is_object($cache) ? get_class($cache) : gettype($cache)
                )
            );
        }

        return $cache;
    }
}
