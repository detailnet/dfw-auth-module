<?php

namespace Detail\Auth\Factory\Authorization\Adapter;

use Interop\Container\ContainerInterface;

use ZfcRbac\Service\AuthorizationServiceInterface;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Authorization\Adapter\ZfcRbacAdapter;
use Detail\Auth\Options\Authorization\Adapter\ZfcRbacAdapterOptions;
use Detail\Auth\Options\ModuleOptions;

class ZfcRbacAdapterFactory implements
    FactoryInterface
{
    /**
     * Create ZfcRbacAdapter
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ZfcRbacAdapter
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::CLASS);
        $authorizationOptions = $moduleOptions->getAuthorization();

        /** @var ZfcRbacAdapterOptions $adapterOptions */
        $adapterOptions = $authorizationOptions->getAdapterOptions('zfc-rbac', ZfcRbacAdapterOptions::CLASS);
        $rbacServiceClass = $adapterOptions->getService();

        if (!$rbacServiceClass) {
            throw new ServiceNotCreatedException('Missing ZfcRbac service class');
        }

        /** @var AuthorizationServiceInterface $rbacService */
        $rbacService = $container->get($rbacServiceClass);

        return new ZfcRbacAdapter($rbacService);
    }
}
