<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\AuthenticationAdapter as Adapter;

class AuthenticationAdapterFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
        $identityOptions = $moduleOptions->getIdentity();

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
