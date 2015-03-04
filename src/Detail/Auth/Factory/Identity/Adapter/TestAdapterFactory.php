<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\Adapter\TestAdapter as Adapter;
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
        /** @var \Detail\Auth\Options\Identity\Adapter\TestAdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            Adapter::CLASS,
            'Detail\Auth\Options\Identity\Adapter\TestAdapterOptions'
        );

        $adapter = new Adapter($adapterOptions->getResult(), $adapterOptions->getErrorMessage());

        return $adapter;
    }
}
