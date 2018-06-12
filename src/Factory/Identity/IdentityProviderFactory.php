<?php

namespace Detail\Auth\Factory\Identity;

use Interop\Container\ContainerInterface;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Identity\AdapterManager;
use Detail\Auth\Identity\IdentityProvider;
use Detail\Auth\Options\ModuleOptions;

class IdentityProviderFactory implements
    FactoryInterface
{
    /**
     * Create IdentityProvider
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return IdentityProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::CLASS);
        $identityOptions = $moduleOptions->getIdentity();

        /** @var AdapterManager $adapters */
        $adapters = $container->get(AdapterManager::CLASS);
        $identityProvider = new IdentityProvider($adapters);
        $defaultAdapter = $identityOptions->getDefaultAdapter();

        if ($defaultAdapter !== null) {
            $identityProvider->setDefaultAdapterType($defaultAdapter);
        }

        foreach ($identityOptions->getListeners() as $listenerClass) {
            /** @var ListenerAggregateInterface $listener */
            $listener = $container->get($listenerClass);
            $listener->attach($identityProvider->getEventManager());
        }

        return $identityProvider;
    }
}
