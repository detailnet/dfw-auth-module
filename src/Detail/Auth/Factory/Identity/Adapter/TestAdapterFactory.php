<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Detail\Auth\Identity\Adapter\TestAdapter as Adapter;

class TestAdapterFactory implements FactoryInterface
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

        /** @var \Detail\Auth\Options\Identity\Adapter\TestAdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            'test',
            'Detail\Auth\Options\Identity\Adapter\TestAdapterOptions'
        );

        $adapter = new Adapter($adapterOptions->getResult(), $adapterOptions->getErrorMessage());

        return $adapter;
    }
}
