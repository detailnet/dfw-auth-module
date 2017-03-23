<?php

namespace Detail\Auth\Authorization;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class AuthorizationServiceInitializer implements
    InitializerInterface
{
    /**
     * Initialize the given instance
     *
     * @param ContainerInterface $container
     * @param object $instance
     * @return void
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof AuthorizationServiceAwareInterface) {
            /** @var AuthorizationService $authorizationService */
            $authorizationService = $container->get(AuthorizationService::CLASS);
            $instance->setAuthorizationService($authorizationService);
        }
    }
}
