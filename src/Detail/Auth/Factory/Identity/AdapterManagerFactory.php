<?php

namespace Detail\Auth\Factory\Identity;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\AdapterManager;

class AdapterManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return AdapterManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
        $identityOptions = $moduleOptions->getIdentity();

        $adapters = new AdapterManager();
        $adapters->setServiceLocator($serviceLocator);

        foreach ($identityOptions->getAdapterFactories() as $adapterType => $adapterFactory) {
            $adapters->setFactory($adapterType, $adapterFactory);
        }

        return $adapters;
    }
}
