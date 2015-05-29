<?php

namespace Detail\Auth\Factory\Identity\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\Listener\RoutesListener as Listener;

class RoutesListenerFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return Listener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
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
