<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;

use Detail\Auth\Identity\Adapter\ChainedAdapter as Adapter;
use Detail\Auth\Identity\AdapterManager;
use Detail\Auth\Options\Identity\Adapter\ChainedAdapterOptions as AdapterOptions;
use Detail\Auth\Options\Identity\IdentityOptions;

class ChainedAdapterFactory extends BaseAdapterFactory
{
    /**
     * @param ContainerInterface $container
     * @param IdentityOptions $identityOptions
     * @return Adapter
     */
    protected function createAdapter(ContainerInterface $container, IdentityOptions $identityOptions)
    {
        /** @var AdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            Adapter::CLASS,
            AdapterOptions::CLASS
        );

        /** @var AdapterManager $adapters */
        $adapters = $container->get(AdapterManager::CLASS);
        $chainedAdapters = $adapterOptions->getAdapters();

        if (count($chainedAdapters) === 0) {
            throw new ServiceNotCreatedException('Chained adapter requires at least one adapter');
        }

        $adapter = new Adapter($adapters, $chainedAdapters);

        return $adapter;
    }
}
