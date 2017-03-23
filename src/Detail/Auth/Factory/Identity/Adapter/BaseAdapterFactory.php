<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Interop\Container\ContainerInterface;

use Zend\Cache\Storage\StorageInterface as CacheStorage;
use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\BaseAdapter;
use Detail\Auth\Identity\IdentityProvider;
use Detail\Auth\Options\Identity\IdentityOptions;
use Detail\Auth\Options\ModuleOptions;

abstract class BaseAdapterFactory implements
    FactoryInterface
{
    /**
     * Create adapter
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::CLASS);
        $identityOptions = $moduleOptions->getIdentity();

        $adapter = $this->createAdapter($container, $identityOptions);

        /** @var IdentityProvider $identityProvider */
        $identityProvider = $container->get(IdentityProvider::CLASS);

        if ($adapter instanceof BaseAdapter) {
            $adapter->setEventManager($identityProvider->getEventManager());
        }

        return $adapter;
    }

    /**
     * @param ContainerInterface $container
     * @param IdentityOptions $identityOptions
     * @return mixed
     */
    abstract protected function createAdapter(ContainerInterface $container, IdentityOptions $identityOptions);

    /**
     * @param ContainerInterface $container
     * @param string|null $cacheName
     * @return CacheStorage|null
     */
    protected function getCache(ContainerInterface $container, $cacheName)
    {
        if ($cacheName !== null) {
            return $this->createCache($container, $cacheName);
        }

        return null;
    }

    /**
     * @param ContainerInterface $container
     * @param string $cacheName
     * @return CacheStorage
     */
    protected function createCache(ContainerInterface $container, $cacheName)
    {
        if (!is_string($cacheName) || strlen($cacheName) == 0) {
            throw new ConfigException(
                sprintf(
                    '%s requires a valid service name for configuration "cache"',
                    get_class($this)
                )
            );
        }

        if (!$container->has($cacheName)) {
            throw new ConfigException(
                sprintf(
                    '%s requires service "%s"; service does not exist',
                    get_class($this),
                    $cacheName
                )
            );
        }

        $cache = $container->get($cacheName);

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
