<?php

return array(
    'service_manager' => array(
        'abstract_factories' => array(
        ),
        'aliases' => array(
        ),
        'invokables' => array(
        ),
        'factories' => array(
            'Detail\Auth\Authorization\Adapter\ZfcRbacAdapter' => 'Detail\Auth\Factory\Authorization\Adapter\ZfcRbacAdapterFactory',
            'Detail\Auth\Authorization\AuthorizationService' => 'Detail\Auth\Factory\Authorization\AuthorizationServiceFactory',
            'Detail\Auth\Authorization\View\Listener\NavigationListener' => 'Detail\Auth\Factory\Authorization\View\Listener\NavigationListenerFactory',
            'Detail\Auth\Identity\AdapterManager' => 'Detail\Auth\Factory\Identity\AdapterManagerFactory',
            'Detail\Auth\Identity\IdentityProvider' => 'Detail\Auth\Factory\Identity\IdentityProviderFactory',
            'Detail\Auth\Options\ModuleOptions' => 'Detail\Auth\Factory\Options\ModuleOptionsFactory',
            'Detail\Auth\Options\ThreeScaleOptions' => 'Detail\Auth\Factory\Options\ThreeScaleOptionsFactory',
            'ThreeScaleClient' => 'Detail\Auth\Factory\ThreeScale\ThreeScaleClientFactory',
            'ZF\MvcAuth\Authorization\DefaultResourceResolverListener' => 'ZF\MvcAuth\Factory\DefaultResourceResolverListenerFactory',
        ),
        'initializers' => array(
            'Detail\Auth\Authorization\AuthorizationServiceInitializer',
        ),
        'shared' => array(
        ),
    ),
    'controllers' => array(
        'initializers' => array(
            'Detail\Auth\Authorization\AuthorizationServiceInitializer',
        ),
    ),
    'zfc_rbac' => array(
        'guard_manager' => array(
            'factories' => array(
                'Detail\Auth\Authorization\ZfcRbac\Guard\RestGuard' => 'Detail\Auth\Factory\Authorization\ZfcRbac\Guard\RestGuardFactory',
            ),
        ),
    ),
    'detail_auth' => array(
        'authorization' => array(
            'adapter' => 'Detail\Auth\Authorization\Adapter\ZfcRbacAdapter',
            'adapters' => array(
                'zfc-rbac' => array(
                    'service' => 'ZfcRbac\Service\AuthorizationService',
                ),
            ),
        ),
        'identity' => array(
            'default_adapter' => 'test',
            'adapter_factories' => array(
                '3scale' => 'Detail\Auth\Factory\Identity\Adapter\ThreeScaleAdapterFactory',
                'test' => 'Detail\Auth\Factory\Identity\Adapter\TestAdapterFactory',
            ),
            'adapters' => array(
                '3scale' => array(
                    'client' => 'ThreeScaleClient',
                ),
                'test' => array(
                    'result' => true,
                    'error_message' => null,
                ),
            ),
        ),
    ),
);
