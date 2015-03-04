<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\ThreeScaleAdapter as Adapter;
use Detail\Auth\Options\Identity\IdentityOptions;

class ThreeScaleAdapterFactory extends BaseAdapterFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param IdentityOptions $identityOptions
     * @return Adapter
     */
    protected function createAdapter(
        ServiceLocatorInterface $serviceLocator,
        IdentityOptions $identityOptions
    ) {
        /** @var \Detail\Auth\Options\Identity\Adapter\ThreeScaleAdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            '3scale',
            'Detail\Auth\Options\Identity\Adapter\ThreeScaleAdapterOptions'
        );

        /** @var \Detail\Auth\Options\ThreeScaleOptions $threeScaleOptions */
        $threeScaleOptions = $serviceLocator->get('Detail\Auth\Options\ThreeScaleOptions');

        $clientClass = $adapterOptions->getClient();

        if (!$clientClass) {
            throw new ConfigException('Missing 3scale client class');
        }

        /** @var \ThreeScaleClient $client */
        $client = $serviceLocator->get($clientClass);

        $adapter = new Adapter($client, $threeScaleOptions->getServiceId());

        return $adapter;
    }
}
