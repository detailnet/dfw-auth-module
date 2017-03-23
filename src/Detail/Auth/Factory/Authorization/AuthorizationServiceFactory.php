<?php

namespace Detail\Auth\Factory\Authorization;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Authorization\Adapter\AdapterInterface;
use Detail\Auth\Authorization\AuthorizationService;
use Detail\Auth\Exception\ConfigException;
use Detail\Auth\Options\ModuleOptions;

class AuthorizationServiceFactory implements
    FactoryInterface
{
    /**
     * Create AuthorizationService
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthorizationService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::CLASS);
        $authorizationOptions = $moduleOptions->getAuthorization();

        $adapterClass = $authorizationOptions->getAdapter();

        if (!$adapterClass) {
            throw new ConfigException('Missing authorization adapter class');
        }

        /** @var AdapterInterface $adapter */
        $adapter = $container->get($adapterClass);

        return new AuthorizationService($adapter);
    }
}
