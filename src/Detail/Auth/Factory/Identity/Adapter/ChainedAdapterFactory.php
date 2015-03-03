<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\ChainedAdapter as Adapter;

class ChainedAdapterFactory implements FactoryInterface
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

        /** @var \Detail\Auth\Options\Identity\Adapter\ChainedAdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            'chained',
            'Detail\Auth\Options\Identity\Adapter\ChainedAdapterOptions'
        );

        /** @var \Detail\Auth\Identity\AdapterManager $adapters */
        $adapters = $serviceLocator->get('Detail\Auth\Identity\AdapterManager');
        $chainedAdapters = $adapterOptions->getAdapters();

        if (count($chainedAdapters) === 0) {
            throw new ConfigException('Chained adapter required at least one adapter');
        }

        $adapter = new Adapter($adapters, $chainedAdapters);

        return $adapter;
    }
}
