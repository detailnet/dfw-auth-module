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
            'Detail\Auth\Options\ModuleOptions' => 'Detail\Auth\Factory\Options\ModuleOptionsFactory',
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
    ),
);
