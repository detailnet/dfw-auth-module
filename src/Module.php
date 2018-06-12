<?php

namespace Detail\Auth;

use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Request as HttpRequest;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\MvcEvent;

use Detail\Auth\Authorization\View\Listener\NavigationListener;
use Detail\Auth\Identity\Adapter\TestAdapter;
use Detail\Auth\Identity\Event;
use Detail\Auth\Identity\IdentityProvider;
use Detail\Auth\Service\HttpRequestAwareInterface;
use Detail\Auth\Service\MvcEventAwareInterface;

class Module implements
    ConfigProviderInterface
{
    /**
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $this->bootstrapAuth($event);
        $this->bootstrapNavigation($event);
    }

    /**
     * @param MvcEvent $event
     */
    public function bootstrapAuth(MvcEvent $event)
    {
        /** @var ServiceManager $services */
        $services = $event->getApplication()->getServiceManager();

        /** @var IdentityProvider $identityProvider */
        $identityProvider = $services->get(IdentityProvider::CLASS);

        $request = $event->getRequest();

        // This is somewhat redundant since each event will be injected with the MVC event.
        $injectRequest = function (Event\IdentityAdapterEvent $authEvent) use ($request) {
            $adapter = $authEvent->getParam(Event\IdentityAdapterEvent::PARAM_ADAPTER);

            if ($adapter instanceof HttpRequestAwareInterface
                && $request instanceof HttpRequest
            ) {
                $adapter->setRequest($request);
            }
        };

        $injectMvcEvent = function (Event\IdentityEvent $identityEvent) use ($event) {
            if ($identityEvent instanceof MvcEventAwareInterface) {
                $identityEvent->setMvcEvent($event);
            }
        };

        // Make sure the MvcEvent object gets injected first (high priority)
        $events = $identityProvider->getEventManager();
        $events->attach(Event\IdentityProviderEvent::EVENT_PRE_AUTHENTICATE, $injectMvcEvent, 10000);
        $events->attach(Event\IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE, $injectMvcEvent, 10000);
        $events->attach(Event\IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE, $injectRequest, 9999);
        $events->attach(Event\IdentityAdapterEvent::EVENT_AUTHENTICATE, $injectMvcEvent, 10000);
        $events->attach(Event\IdentityProviderEvent::EVENT_AUTHENTICATE, $injectMvcEvent, 10000);

        if ($request instanceof ConsoleRequest) {
            /** @todo We should probably disable the authentication instead of using a test/dummy adapter... */
            $identityProvider->setDefaultAdapterType(TestAdapter::CLASS);
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function bootstrapNavigation(MvcEvent $event)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $event->getApplication()->getServiceManager();

        /** @var NavigationListener $authorizationListener */
        $authorizationListener = $serviceManager->get(NavigationListener::CLASS);

        // The AbstractHelper attaches it's own AclListener (which is useless for us).
        // But since we can't disable the AclListener (AbstractHelper wouldn't trigger the "isAllowed" event),
        // we need to register our own listener with lower priority (only the last listener's result
        // is used to decide if allowed or not).
        $event->getApplication()->getEventManager()->getSharedManager()->attach(
            'Zend\View\Helper\Navigation\AbstractHelper',
            'isAllowed',
            [$authorizationListener, 'accept'],
            -100 // AclListeners is 1 (so we're lower)
        );
    }

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
