<?php

namespace Detail\Auth\Factory\Authorization\ZfcRbac\Guard;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Detail\Auth\Authorization\ZfcRbac\Guard\RestGuard;

class RestGuardFactory implements
    FactoryInterface,
    MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param array $options
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /* @var \ZfcRbac\Options\ModuleOptions $moduleOptions */
        $rbacOptions = $serviceLocator->get('ZfcRbac\Options\ModuleOptions');

        /* @var \ZfcRbac\Service\AuthorizationService $authorizationService */
        $authorizationService = $serviceLocator->get('ZfcRbac\Service\AuthorizationService');

        /** @var \ZF\MvcAuth\Authorization\DefaultResourceResolverListener $resourceResolver */
        $resourceResolver = $serviceLocator->get('ZF\MvcAuth\Authorization\DefaultResourceResolverListener');

        $guard = new RestGuard($authorizationService, $resourceResolver, $this->options);
        $guard->setProtectionPolicy($rbacOptions->getProtectionPolicy());

        return $guard;
    }
}
