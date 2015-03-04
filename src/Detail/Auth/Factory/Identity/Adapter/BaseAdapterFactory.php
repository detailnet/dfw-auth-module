<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Detail\Auth\Identity\Adapter\BaseAdapter;
use Detail\Auth\Options\Identity\IdentityOptions;

abstract class BaseAdapterFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return BaseAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
        $identityOptions = $moduleOptions->getIdentity();

        $adapter = $this->createAdapter($serviceLocator, $identityOptions);

        /** @var \Detail\Auth\Identity\IdentityProvider $identityProvider */
        $identityProvider = $serviceLocator->get('Detail\Auth\Identity\IdentityProvider');

        if ($adapter instanceof BaseAdapter) {
            $adapter->setEventManager($identityProvider->getEventManager());
        }

        return $adapter;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param IdentityOptions $identityOptions
     * @return mixed
     */
    abstract protected function createAdapter(
        ServiceLocatorInterface $serviceLocator,
        IdentityOptions $identityOptions
    );
}
