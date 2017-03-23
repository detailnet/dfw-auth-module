<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Interop\Container\ContainerInterface;

use Zend\Authentication\AuthenticationService;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\AuthenticationAdapter as Adapter;
use Detail\Auth\Options\Identity\Adapter\AuthenticationAdapterOptions as AdapterOptions;
use Detail\Auth\Options\Identity\IdentityOptions;

class AuthenticationAdapterFactory extends BaseAdapterFactory
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

        $authenticationServiceClass = $adapterOptions->getService();

        if (!$authenticationServiceClass) {
            throw new ConfigException('Missing authentication service class');
        }

        /** @var AuthenticationService $authenticationService */
        $authenticationService = $container->get($authenticationServiceClass);

        $adapter = new Adapter($authenticationService);

        return $adapter;
    }
}
