<?php

namespace Detail\Auth\Factory\ThreeScale;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use ThreeScaleClient;

class ThreeScaleClientFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return ThreeScaleClient
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Options\ThreeScaleOptions $threeScaleOptions */
        $threeScaleOptions = $serviceLocator->get('Detail\Auth\Options\ThreeScaleOptions');

        $client = new ThreeScaleClient($threeScaleOptions->getProviderKey());

        return $client;
    }
}
