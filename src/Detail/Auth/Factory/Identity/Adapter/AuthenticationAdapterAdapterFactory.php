<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Identity\Adapter\AuthenticationAdapterAdapter as Adapter;
use Detail\Auth\Options\Identity\Adapter\AuthenticationAdapterAdapterOptions as AdapterOptions;
use Detail\Auth\Options\Identity\IdentityOptions;

class AuthenticationAdapterAdapterFactory extends BaseAdapterFactory
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
        /** @var AdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            Adapter::CLASS,
            AdapterOptions::CLASS
        );

        $authenticationAdapterClass = $adapterOptions->getAuthenticationAdapter();

        if (!$authenticationAdapterClass) {
            throw new ConfigException('Missing authentication adapter class');
        }

        /** @var \Zend\Authentication\Adapter\ValidatableAdapterInterface $authenticationAdapter */
        $authenticationAdapter = $serviceLocator->get($authenticationAdapterClass);

        $credentialHeaders = array(
            Adapter::CREDENTIAL_APPLICATION_ID  => $adapterOptions->getAppIdHeader(),
            Adapter::CREDENTIAL_APPLICATION_KEY => $adapterOptions->getAppKeyHeader(),
        );

        $adapter = new Adapter(
            $authenticationAdapter,
            $credentialHeaders,
            $this->getCache($serviceLocator, $adapterOptions->getCache())
        );

        return $adapter;
    }
}
