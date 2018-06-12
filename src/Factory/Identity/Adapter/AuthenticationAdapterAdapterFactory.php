<?php

namespace Detail\Auth\Factory\Identity\Adapter;

use Interop\Container\ContainerInterface;

use Zend\Authentication\Adapter\ValidatableAdapterInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

use Detail\Auth\Identity\Adapter\AuthenticationAdapterAdapter as Adapter;
use Detail\Auth\Options\Identity\Adapter\AuthenticationAdapterAdapterOptions as AdapterOptions;
use Detail\Auth\Options\Identity\IdentityOptions;

class AuthenticationAdapterAdapterFactory extends BaseAdapterFactory
{
    /**
     * @param ContainerInterface $container
     * @param IdentityOptions $identityOptions
     * @return Adapter
     */
    protected function createAdapter(ContainerInterface $container, IdentityOptions $identityOptions)
    {
        /** @var AdapterOptions $adapterOptions */
        $adapterOptions = $identityOptions->getAdapterOptions(
            Adapter::CLASS,
            AdapterOptions::CLASS
        );

        $authenticationAdapterClass = $adapterOptions->getAuthenticationAdapter();

        if (!$authenticationAdapterClass) {
            throw new ServiceNotCreatedException('Missing authentication adapter class');
        }

        /** @var ValidatableAdapterInterface $authenticationAdapter */
        $authenticationAdapter = $container->get($authenticationAdapterClass);

        $credentialHeaders = [
            Adapter::CREDENTIAL_APPLICATION_ID  => $adapterOptions->getAppIdHeader(),
            Adapter::CREDENTIAL_APPLICATION_KEY => $adapterOptions->getAppKeyHeader(),
        ];

//        $adapter = new Adapter(
//            $authenticationAdapter,
//            $credentialHeaders,
//            $this->getCache($container, $adapterOptions->getCache())
//        );

        $adapter = new Adapter($authenticationAdapter, $credentialHeaders);

        return $adapter;
    }
}
