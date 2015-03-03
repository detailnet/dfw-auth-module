<?php

namespace Detail\Auth\Factory\Authorization\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Authorization\Adapter\ZfcRbacAdapter;
use Detail\Auth\Exception\ConfigException;

class ZfcRbacAdapterFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return ZfcRbacAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');
        $authorizationOptions = $moduleOptions->getAuthorization();

        /** @var \Detail\Auth\Options\Authorization\Adapter\ZfcRbacAdapterOptions $adapterOptions */
        $adapterOptions = $authorizationOptions->getAdapterOptions(
            'zfc-rbac',
            'Detail\Auth\Options\Authorization\Adapter\ZfcRbacAdapterOptions'
        );

        $rbacServiceClass = $adapterOptions->getService();

        if (!$rbacServiceClass) {
            throw new ConfigException('Missing ZfcRbac service class');
        }

        /** @var \ZfcRbac\Service\AuthorizationServiceInterface $rbacService */
        $rbacService = $serviceLocator->get($rbacServiceClass);

        return new ZfcRbacAdapter($rbacService);
    }
}
