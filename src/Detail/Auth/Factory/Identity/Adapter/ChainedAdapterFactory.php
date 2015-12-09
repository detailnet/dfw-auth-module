<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\ChainedAdapter as Adapter;
use Detail\Auth\Options\Identity\Adapter\ChainedAdapterOptions as AdapterOptions;
use Detail\Auth\Options\Identity\IdentityOptions;

class ChainedAdapterFactory extends BaseAdapterFactory
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
        /** @var AdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            Adapter::CLASS,
            AdapterOptions::CLASS
        );

        /** @var \Detail\Auth\Identity\AdapterManager $adapters */
        $adapters = $serviceLocator->get('Detail\Auth\Identity\AdapterManager');
        $chainedAdapters = $adapterOptions->getAdapters();

        if (count($chainedAdapters) === 0) {
            throw new ConfigException('Chained adapter requires at least one adapter');
        }

        $adapter = new Adapter($adapters, $chainedAdapters);

        return $adapter;
    }
}
