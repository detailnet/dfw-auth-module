<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Detail\Auth\Identity\Adapter\ThreeScaleAdapter as Adapter;

class ThreeScaleAdapterFactory implements FactoryInterface
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

        /** @var \Detail\Auth\Options\Identity\Adapter\ThreeScaleAdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            '3scale',
            'Detail\Auth\Options\Identity\Adapter\ThreeScaleAdapterOptions'
        );

        /** @var \Detail\Auth\Options\ThreeScaleOptions $threeScaleOptions */
        $threeScaleOptions = $serviceLocator->get('Detail\Auth\Options\ThreeScaleOptions');

        /** @var \ThreeScaleClient $client */
        $client = $serviceLocator->get($adapterOptions->getClient());

        $adapter = new Adapter($client, $threeScaleOptions->getServiceId());

        return $adapter;
    }
}
