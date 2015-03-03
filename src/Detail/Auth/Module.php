<?php

namespace Detail\Auth;

use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Request as HttpRequest;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;

use Detail\Auth\Identity\Adapter\ThreeScaleAdapter;
use Detail\Auth\Identity\IdentityProviderEvent;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ControllerProviderInterface,
    ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $event)
    {
        $this->bootstrapAuth($event);
        $this->bootstrapNavigation($event);
    }

    public function bootstrapAuth(MvcEvent $event)
    {
        /** @var \Zend\ServiceManager\ServiceManager $serviceManager */
        $serviceManager = $event->getApplication()->getServiceManager();

        /** @var \Detail\Auth\Identity\IdentityProvider $identityProvider */
        $identityProvider = $serviceManager->get(__NAMESPACE__ . '\Identity\IdentityProvider');

        $request = $event->getRequest();

        if ($request instanceof ConsoleRequest) {
            /** @todo We should probably disable the authentication instead of using a test/dummy adapter... */
            $identityProvider->setDefaultAdapterType('test');
            return;
        }

        $injectRequest = function(IdentityProviderEvent $authEvent) use ($request) {
            $adapter = $authEvent->getParam(IdentityProviderEvent::PARAM_ADAPTER);

            /** @todo Use interface in adapters that need HttpRequest */
//            if ($adapter instanceof HttpRequestAwareAdapterInterface
            if ($adapter instanceof ThreeScaleAdapter
                && $request instanceof HttpRequest
            ) {
                $adapter->setRequest($request);
            }
        };

        $identityProvider->getEventManager()->attach(
            IdentityProviderEvent::EVENT_PRE_AUTHENTICATE,
            $injectRequest
        );
    }

    public function bootstrapNavigation(MvcEvent $event)
    {
        /** @var \Zend\ServiceManager\ServiceManager $serviceManager */
        $serviceManager = $event->getApplication()->getServiceManager();

        /** @var \Detail\Auth\Authorization\View\Listener\NavigationListener $authorizationListener */
        $authorizationListener = $serviceManager->get(
            'Detail\Auth\Authorization\View\Listener\NavigationListener'
        );

        // The AbstractHelper attaches it's own AclListener (which is useless for us).
        // But since we can't disable the AclListener (AbstractHelper wouldn't trigger the "isAllowed" event),
        // we need to register our own listener with lower priority (only the last listener's result
        // is used to decide if allowed or not).
        $event->getApplication()->getEventManager()->getSharedManager()->attach(
            'Zend\View\Helper\Navigation\AbstractHelper',
            'isAllowed',
            array($authorizationListener, 'accept'),
            -100 // AclListeners is 1 (so we're lower)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            AutoloaderFactory::STANDARD_AUTOLOADER => array(
                StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    public function getControllerConfig()
    {
        return array();
    }

    public function getServiceConfig()
    {
        return array();
    }
}
