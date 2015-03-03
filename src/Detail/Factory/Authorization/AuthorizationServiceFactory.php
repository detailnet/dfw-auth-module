<?php

namespace Detail\Auth\Factory\Authorization;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Authorization\AuthorizationService;
use Detail\Auth\Exception\ConfigException;

class AuthorizationServiceFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return AuthorizationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
        $authorizationOptions = $moduleOptions->getAuthorization();

        $adapterClass = $authorizationOptions->getAdapter();

        if (!$adapterClass) {
            throw new ConfigException('Missing authorization adapter class');
        }

        /** @var \Detail\Auth\Authorization\Adapter\AdapterInterface $adapter */
        $adapter = $serviceLocator->get($adapterClass);

        return new AuthorizationService($adapter);
    }
}
