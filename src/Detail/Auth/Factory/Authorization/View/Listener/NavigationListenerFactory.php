<?php

namespace Detail\Auth\Factory\Authorization\View\Listener;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Authorization\AuthorizationService;
use Detail\Auth\Authorization\View\Listener\NavigationListener;

class NavigationListenerFactory implements
    FactoryInterface
{
    /**
     * Create NavigationListener
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NavigationListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var AuthorizationService $authorizationService */
        $authorizationService = $container->get(AuthorizationService::CLASS);

        return new NavigationListener($authorizationService);
    }
}
