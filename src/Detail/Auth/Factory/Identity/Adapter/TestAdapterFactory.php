<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Interop\Container\ContainerInterface;

use Detail\Auth\Identity\Adapter\TestAdapter as Adapter;
use Detail\Auth\Options\Identity\Adapter\TestAdapterOptions as AdapterOptions;
use Detail\Auth\Options\Identity\IdentityOptions;

class TestAdapterFactory extends BaseAdapterFactory
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

        $adapter = new Adapter($adapterOptions->getResult(), $adapterOptions->getErrorMessage());

        return $adapter;
    }
}
