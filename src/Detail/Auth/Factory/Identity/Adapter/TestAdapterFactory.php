<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\Adapter\TestAdapter as Adapter;
use Detail\Auth\Options\Identity\Adapter\TestAdapterOptions as AdapterOptions;
use Detail\Auth\Options\Identity\IdentityOptions;

class TestAdapterFactory extends BaseAdapterFactory
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

        $adapter = new Adapter($adapterOptions->getResult(), $adapterOptions->getErrorMessage());

        return $adapter;
    }
}
