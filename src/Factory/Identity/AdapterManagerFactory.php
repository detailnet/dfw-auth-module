<?php

namespace Detail\Auth\Factory\Identity;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Identity\AdapterManager;
use Detail\Auth\Options\ModuleOptions;

class AdapterManagerFactory implements
    FactoryInterface
{
    /**
     * Create AdapterManager
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AdapterManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::CLASS);
        $identityOptions = $moduleOptions->getIdentity();

        $adapters = new AdapterManager($container);

        foreach ($identityOptions->getAdapterFactories() as $adapterType => $adapterFactory) {
            $adapters->setFactory($adapterType, $adapterFactory);
        }

        return $adapters;
    }
}
