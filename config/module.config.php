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
            'Detail\Auth\Identity\Listener\RoutesListener' => 'Detail\Auth\Factory\Identity\Listener\RoutesListenerFactory',
            'Detail\Auth\Options\ModuleOptions' => 'Detail\Auth\Factory\Options\ModuleOptionsFactory',
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
            'default_adapter' => 'Detail\Auth\Identity\Adapter\TestAdapter',
            'adapter_factories' => array(
                'Detail\Auth\Identity\Adapter\AuthenticationAdapter' => 'Detail\Auth\Factory\Identity\Adapter\AuthenticationAdapterFactory',
                'Detail\Auth\Identity\Adapter\AuthenticationAdapterAdapter' => 'Detail\Auth\Factory\Identity\Adapter\AuthenticationAdapterAdapterFactory',
                'Detail\Auth\Identity\Adapter\ChainedAdapter' => 'Detail\Auth\Factory\Identity\Adapter\ChainedAdapterFactory',
                'Detail\Auth\Identity\Adapter\TestAdapter' => 'Detail\Auth\Factory\Identity\Adapter\TestAdapterFactory',
            ),
            'adapters' => array(
                'Detail\Auth\Identity\Adapter\AuthenticationAdapterAdapter' => array(
                    'authentication_adapter' => null,
//                    'cache' => null,
                    'app_id_header' => 'DWS-App-ID',
                    'app_key_header' => 'DWS-App-Key',
                ),
                'Detail\Auth\Identity\Adapter\AuthenticationAdapter' => array(
                    'service' => 'Zend\Authentication\AuthenticationService',
                ),
                'Detail\Auth\Identity\Adapter\ChainedAdapter' => array(
                    'adapters' => array(
                    ),
                ),
                'Detail\Auth\Identity\Adapter\TestAdapter' => array(
                    'result' => true,
                    'error_message' => null,
                ),
            ),
            'listeners' => array(
                'Detail\Auth\Identity\Listener\RoutesListener',
            ),
        ),
    ),
);
