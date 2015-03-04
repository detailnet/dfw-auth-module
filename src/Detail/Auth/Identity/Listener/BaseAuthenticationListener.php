<?php

namespace Detail\Auth\Identity\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Detail\Auth\Identity\IdentityProviderEvent;
use Detail\Auth\Identity\IdentityAdapterEvent;

abstract class BaseAuthenticationListener implements
    ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Attach events to the identity provider.
     *
     * This method attaches listeners to the authenticate.pre and authenticate
     * events of Detail\Auth\Identity\IdentityProvider.
     *
     * @param EventManagerInterface $eventManager
     */
    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach(
            IdentityProviderEvent::EVENT_PRE_AUTHENTICATE,
            array($this, 'onPreAuthenticate')
        );

        $this->listeners[] = $eventManager->attach(
            IdentityProviderEvent::EVENT_AUTHENTICATE,
            array($this, 'onAuthenticate')
        );

        $this->listeners[] = $eventManager->attach(
            IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE,
            array($this, 'onAdapterPreAuthenticate')
        );

        $this->listeners[] = $eventManager->attach(
            IdentityAdapterEvent::EVENT_AUTHENTICATE,
            array($this, 'onAdapterAuthenticate')
        );
    }

    /**
     * Detach events from the identity provider.
     *
     * This method detaches listeners it has previously attached.
     *
     * @param EventManagerInterface $eventManager
     */
    public function detach(EventManagerInterface $eventManager)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($eventManager->detach($listener)) {
                unset($listener[$index]);
            }
        }
    }

    /**
     * Listener for the "authenticate.pre" event.
     *
     * @param IdentityProviderEvent $event
     */
    abstract public function onPreAuthenticate(IdentityProviderEvent $event);

    /**
     * Listener for the "authenticate" event.
     *
     * @param IdentityProviderEvent $event
     */
    abstract public function onAuthenticate(IdentityProviderEvent $event);

    /**
     * Listener for the "adapter.authenticate.pre" event.
     *
     * @param IdentityAdapterEvent $event
     */
    abstract public function onAdapterPreAuthenticate(IdentityAdapterEvent $event);

    /**
     * Listener for the "adapter.authenticate" event.
     *
     * @param IdentityAdapterEvent $event
     */
    abstract public function onAdapterAuthenticate(IdentityAdapterEvent $event);
}
