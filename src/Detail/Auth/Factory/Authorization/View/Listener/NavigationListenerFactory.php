<?php

namespace Detail\Auth\Factory\Authorization\View\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Authorization\View\Listener\NavigationListener;

class NavigationListenerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return NavigationListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Authorization\AuthorizationService $authorizationService */
        $authorizationService = $serviceLocator->get('Detail\Auth\Authorization\AuthorizationService');

        return new NavigationListener($authorizationService);
    }
}
