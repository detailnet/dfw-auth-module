<?php

namespace Detail\Auth\Factory\Identity\Listener;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Identity\Listener\RoutesListener as Listener;
use Detail\Auth\Options\ModuleOptions;

class RoutesListenerFactory implements
    FactoryInterface
{
    /**
     * Create RoutesListener
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Listener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::CLASS);
        $identityOptions = $moduleOptions->getIdentity();

        $routeRules = array();

        foreach ($identityOptions->getAdapters() as $adapterClass => $adapterConfig) {
            if (!isset($adapterConfig['routes'])) {
                continue;
            }

            foreach ($adapterConfig['routes'] as $route) {
                $routeRules[$adapterClass][] = $route;
            }
        }

        return new Listener($routeRules);
    }
}
