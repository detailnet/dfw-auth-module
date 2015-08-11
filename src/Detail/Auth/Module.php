<?php

namespace Detail\Auth;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Request as HttpRequest;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;

use Detail\Auth\Identity\Event;
use Detail\Auth\Identity\ThreeScaleResult;
use Detail\Auth\Service\HttpRequestAwareInterface;
use Detail\Auth\Service\MvcEventAwareInterface;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ConsoleUsageProviderInterface,
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
        /** @var \Zend\ServiceManager\ServiceManager $services */
        $services = $event->getApplication()->getServiceManager();

        /** @var \Detail\Auth\Identity\IdentityProvider $identityProvider */
        $identityProvider = $services->get(__NAMESPACE__ . '\Identity\IdentityProvider');

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

        $injectMvcEvent = function(Event\IdentityEvent $identityEvent) use ($event) {
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

        /** @var \Detail\Auth\Options\ThreeScaleOptions $threeScaleOptions */
        $threeScaleOptions = $services->get('Detail\Auth\Options\ThreeScaleOptions');

        // We may need to log requests to 3scale, so the usage can be reported later
        if ($threeScaleOptions->getReporting()->isEnabled()) {
            $attached = false;

            $attachReportingListener = function(Event\IdentityAdapterEvent $identityEvent) use ($event, $services, &$attached) {
                $result = $identityEvent->getParam($identityEvent::PARAM_RESULT);

                // Make sure the listener is only attached once
                if (!$attached
                    && $result instanceof ThreeScaleResult
                    && $result->hasUsage()
                    && $services->has('Detail\Auth\Identity\Listener\ThreeScaleReportingListener')
                ) {
                    /** @var \Detail\Auth\Identity\Listener\ThreeScaleReportingListener $reportingListener */
                    $reportingListener = $services->get('Detail\Auth\Identity\Listener\ThreeScaleReportingListener');
                    $reportingListener->setResult($result);

                    $events = $event->getApplication()->getEventManager();
                    $events->attachAggregate($reportingListener);

                    $attached = true;
                }
            };

            $events->attach(Event\IdentityAdapterEvent::EVENT_AUTHENTICATE, $attachReportingListener, 9999);
        }

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

    /**
     * {@inheritdoc}
     */
    public function getConsoleUsage(Console $console)
    {
        return array(
            'Actions:',
            'threescale report-transactions' => 'Report logged transactions to 3scale',
        );
    }
}
