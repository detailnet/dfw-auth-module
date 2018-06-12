<?php

return [
    'service_manager' => [
        'abstract_factories' => [
        ],
        'aliases' => [
        ],
        'invokables' => [
        ],
        'factories' => [
            'Detail\Auth\Authorization\Adapter\ZfcRbacAdapter' =>
                'Detail\Auth\Factory\Authorization\Adapter\ZfcRbacAdapterFactory',
            'Detail\Auth\Authorization\AuthorizationService' =>
                'Detail\Auth\Factory\Authorization\AuthorizationServiceFactory',
            'Detail\Auth\Authorization\View\Listener\NavigationListener' =>
                'Detail\Auth\Factory\Authorization\View\Listener\NavigationListenerFactory',
            'Detail\Auth\Identity\AdapterManager' =>'Detail\Auth\Factory\Identity\AdapterManagerFactory',
            'Detail\Auth\Identity\IdentityProvider' => 'Detail\Auth\Factory\Identity\IdentityProviderFactory',
            'Detail\Auth\Identity\Listener\RoutesListener' =>
                'Detail\Auth\Factory\Identity\Listener\RoutesListenerFactory',
            'Detail\Auth\Options\ModuleOptions' => 'Detail\Auth\Factory\Options\ModuleOptionsFactory',
            'ZF\MvcAuth\Authorization\DefaultResourceResolverListener' =>
                'ZF\MvcAuth\Factory\DefaultResourceResolverListenerFactory',
        ],
        'initializers' => [
            'Detail\Auth\Authorization\AuthorizationServiceInitializer',
        ],
        'shared' => [
        ],
    ],
    'controllers' => [
        'initializers' => [
            'Detail\Auth\Authorization\AuthorizationServiceInitializer',
        ],
    ],
    'zfc_rbac' => [
        'guard_manager' => [
            'factories' => [
                'Detail\Auth\Authorization\ZfcRbac\Guard\RestGuard' =>
                    'Detail\Auth\Factory\Authorization\ZfcRbac\Guard\RestGuardFactory',
            ],
        ],
    ],
    'detail_auth' => [
        'authorization' => [
            'adapter' => 'Detail\Auth\Authorization\Adapter\ZfcRbacAdapter',
            'adapters' => [
                'zfc-rbac' => [
                    'service' => 'ZfcRbac\Service\AuthorizationService',
                ],
            ],
        ],
        'identity' => [
            'default_adapter' => 'Detail\Auth\Identity\Adapter\TestAdapter',
            'adapter_factories' => [
                'Detail\Auth\Identity\Adapter\AuthenticationAdapter' =>
                    'Detail\Auth\Factory\Identity\Adapter\AuthenticationAdapterFactory',
                'Detail\Auth\Identity\Adapter\AuthenticationAdapterAdapter' =>
                    'Detail\Auth\Factory\Identity\Adapter\AuthenticationAdapterAdapterFactory',
                'Detail\Auth\Identity\Adapter\ChainedAdapter' =>
                    'Detail\Auth\Factory\Identity\Adapter\ChainedAdapterFactory',
                'Detail\Auth\Identity\Adapter\TestAdapter' =>
                    'Detail\Auth\Factory\Identity\Adapter\TestAdapterFactory',
            ],
            'adapters' => [
                'Detail\Auth\Identity\Adapter\AuthenticationAdapterAdapter' => [
                    'authentication_adapter' => null,
//                    'cache' => null,
                    'app_id_header' => 'DWS-App-ID',
                    'app_key_header' => 'DWS-App-Key',
                ],
                'Detail\Auth\Identity\Adapter\AuthenticationAdapter' => [
                    'service' => 'Zend\Authentication\AuthenticationService',
                ],
                'Detail\Auth\Identity\Adapter\ChainedAdapter' => [
                    'adapters' => [
                    ],
                ],
                'Detail\Auth\Identity\Adapter\TestAdapter' => [
                    'result' => true,
                    'error_message' => null,
                ],
            ],
            'listeners' => [
                'Detail\Auth\Identity\Listener\RoutesListener',
            ],
        ],
    ],
];
