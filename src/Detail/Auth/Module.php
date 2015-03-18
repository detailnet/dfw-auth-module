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

use Detail\Auth\Identity\Event;
use Detail\Auth\Service\HttpRequestAwareInterface;
use Detail\Auth\Service\MvcEventAwareInterface;

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

        // This is somewhat redundant since each event will be injected with the MVC event.
        $injectRequest = function(Event\IdentityAdapterEvent $authEvent) use ($request) {
            $adapter = $authEvent->getParam(Event\IdentityAdapterEvent::PARAM_ADAPTER);

            if ($adapter instanceof HttpRequestAwareInterface
                && $request instanceof HttpRequest
            ) {
                $adapter->setRequest($request);
            }
        };

        $injectMvcEvent = function(Event\IdentityEventInterface $identityEvent) use ($event) {
            if ($identityEvent instanceof MvcEventAwareInterface) {
                $identityEvent->setMvcEvent($event);
            }
        };

        // Make sure the MvcEvent object get's injected first (high priority)
        $events = $identityProvider->getEventManager();
        $events->attach(Event\IdentityProviderEvent::EVENT_PRE_AUTHENTICATE, $injectMvcEvent, 10000);
        $events->attach(Event\IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE, $injectMvcEvent, 10000);
        $events->attach(Event\IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE, $injectRequest, 9999);
        $events->attach(Event\IdentityAdapterEvent::EVENT_AUTHENTICATE, $injectMvcEvent, 10000);
        $events->attach(Event\IdentityProviderEvent::EVENT_AUTHENTICATE, $injectMvcEvent, 10000);

        if ($request instanceof ConsoleRequest) {
            /** @todo We should probably disable the authentication instead of using a test/dummy adapter... */
            $identityProvider->setDefaultAdapterType(__NAMESPACE__ . '\Identity\Adapter\TestAdapter');
        }
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
