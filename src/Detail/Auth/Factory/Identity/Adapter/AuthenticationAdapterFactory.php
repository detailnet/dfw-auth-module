<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\AuthenticationAdapter as Adapter;
use Detail\Auth\Options\Identity\IdentityOptions;

class AuthenticationAdapterFactory extends BaseAdapterFactory
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
        /** @var \Detail\Auth\Options\Identity\Adapter\AuthenticationAdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            'authentication',
            'Detail\Auth\Options\Identity\Adapter\AuthenticationAdapterOptions'
        );

        $authenticationServiceClass = $adapterOptions->getService();

        if (!$authenticationServiceClass) {
            throw new ConfigException('Missing authentication service class');
        }

        /** @var \Zend\Authentication\AuthenticationService $authenticationService */
        $authenticationService = $serviceLocator->get($authenticationServiceClass);

        $adapter = new Adapter($authenticationService);

        return $adapter;
    }
}
