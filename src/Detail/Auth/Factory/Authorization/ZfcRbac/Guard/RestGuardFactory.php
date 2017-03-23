<?php

namespace Detail\Auth\Factory\Authorization\ZfcRbac\Guard;

use Interop\Container\ContainerInterface;

use ZfcRbac\Options\ModuleOptions as RbacOptions;
use ZfcRbac\Service\AuthorizationService;

use ZF\MvcAuth\Authorization\DefaultResourceResolverListener;

use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Authorization\ZfcRbac\Guard\RestGuard;

class RestGuardFactory implements
    FactoryInterface
{
    /**
     * Create RestGuard
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return RestGuard
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var RbacOptions $rbacOptions */
        $rbacOptions = $container->get(RbacOptions::CLASS);

        /* @var AuthorizationService $authorizationService */
        $authorizationService = $container->get(AuthorizationService::CLASS);

        /** @var DefaultResourceResolverListener $resourceResolver */
        $resourceResolver = $container->get(DefaultResourceResolverListener::CLASS);

        $guard = new RestGuard($authorizationService, $resourceResolver, $options ?: array());
        $guard->setProtectionPolicy($rbacOptions->getProtectionPolicy());

        return $guard;
    }
}
