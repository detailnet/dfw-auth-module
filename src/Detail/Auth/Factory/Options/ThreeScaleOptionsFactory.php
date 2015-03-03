<?php

namespace Detail\Auth\Factory\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Options\ThreeScaleOptions;

class ThreeScaleOptionsFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return ThreeScaleOptions
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['3scale'])) {
            throw new ConfigException('Config for 3scale is not set');
        }

        $threeScaleConfig = $config['3scale'];

        if (!isset($threeScaleConfig['provider_key'])) {
            throw new ConfigException('3scale provider key is not set');
        }

        return new ThreeScaleOptions($threeScaleConfig);
    }
}
