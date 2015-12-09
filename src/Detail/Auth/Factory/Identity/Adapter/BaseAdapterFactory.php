<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Detail\Auth\Options\Identity\Adapter\CacheTrait;
use Zend\Cache\Storage\StorageInterface as CacheStorage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\BaseAdapter;
use Detail\Auth\Options\Identity\IdentityOptions;

abstract class BaseAdapterFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return BaseAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
        $identityOptions = $moduleOptions->getIdentity();

        $adapter = $this->createAdapter($serviceLocator, $identityOptions);

        /** @var \Detail\Auth\Identity\IdentityProvider $identityProvider */
        $identityProvider = $serviceLocator->get('Detail\Auth\Identity\IdentityProvider');

        if ($adapter instanceof BaseAdapter) {
            $adapter->setEventManager($identityProvider->getEventManager());
        }

        return $adapter;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param IdentityOptions $identityOptions
     * @return mixed
     */
    abstract protected function createAdapter(
        ServiceLocatorInterface $serviceLocator,
        IdentityOptions $identityOptions
    );

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string|null $cacheName
     * @return CacheStorage|null
     */
    protected function getCache(ServiceLocatorInterface $serviceLocator, $cacheName)
    {
        if ($cacheName !== null) {
            return $this->createCache($serviceLocator, $cacheName);
        }

        return null;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $cacheName
     * @return CacheStorage
     */
    protected function createCache(ServiceLocatorInterface $serviceLocator, $cacheName)
    {
        if (!is_string($cacheName) || strlen($cacheName) == 0) {
            throw new ConfigException(
                sprintf(
                    '%s requires a valid service name for configuration "cache"',
                    get_class($this)
                )
            );
        }

        if (!$serviceLocator->has($cacheName)) {
            throw new ConfigException(
                sprintf(
                    '%s requires service "%s"; service does not exist',
                    get_class($this),
                    $cacheName
                )
            );
        }

        $cache = $serviceLocator->get($cacheName);

        if (!$cache instanceof CacheStorage) {
            throw new ConfigException(
                sprintf(
                    '%s requires service "%s" to be of type %s; received "%s"',
                    get_class($this),
                    $cacheName,
                    CacheStorage::CLASS,
                    is_object($cache) ? get_class($cache) : gettype($cache)
                )
            );
        }

        return $cache;
    }
}
