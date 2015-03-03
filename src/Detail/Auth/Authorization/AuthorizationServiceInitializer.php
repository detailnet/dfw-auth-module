<?php

namespace Detail\Auth\Authorization;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class AuthorizationServiceInitializer implements
    InitializerInterface
{
    /**
     * Initialize
     *
     * @param mixed $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof AuthorizationServiceAwareInterface) {
            if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
                $serviceLocator = $serviceLocator->getServiceLocator();
            }

            /** @var AuthorizationServiceInterface $authorizationService */
            $authorizationService = $serviceLocator->get(__NAMESPACE__ . '\AuthorizationService');
            $instance->setAuthorizationService($authorizationService);
        }
    }
}
