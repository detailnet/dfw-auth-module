<?php

namespace Detail\Auth\Factory\Identity;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\IdentityProvider;

class IdentityProviderFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return IdentityProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
        $identityOptions = $moduleOptions->getIdentity();

        /** @var \Detail\Auth\Identity\AdapterManager $adapters */
        $adapters = $serviceLocator->get('Detail\Auth\Identity\AdapterManager');

        $identityProvider = new IdentityProvider($adapters);

        $defaultAdapter = $identityOptions->getDefaultAdapter();

        if ($defaultAdapter !== null) {
            $identityProvider->setDefaultAdapterType($defaultAdapter);
        }

        return $identityProvider;
    }
}
