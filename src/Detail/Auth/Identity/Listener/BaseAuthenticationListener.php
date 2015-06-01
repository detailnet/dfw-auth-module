<?php

namespace Detail\Auth\Identity\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Detail\Auth\Identity\Event;

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
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            Event\IdentityProviderEvent::EVENT_PRE_AUTHENTICATE,
            array($this, 'onPreAuthenticate')
        );

        $this->listeners[] = $events->attach(
            Event\IdentityProviderEvent::EVENT_AUTHENTICATE,
            array($this, 'onAuthenticate')
        );

        $this->listeners[] = $events->attach(
            Event\IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE,
            array($this, 'onAdapterPreAuthenticate')
        );

        $this->listeners[] = $events->attach(
            Event\IdentityAdapterEvent::EVENT_AUTHENTICATE,
            array($this, 'onAdapterAuthenticate')
        );
    }

    /**
     * Detach events from the identity provider.
     *
     * This method detaches listeners it has previously attached.
     *
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($listener[$index]);
            }
        }
    }

    /**
     * Listener for the "authenticate.pre" event.
     *
     * @param Event\IdentityProviderEvent $event
     */
    abstract public function onPreAuthenticate(Event\IdentityProviderEvent $event);

    /**
     * Listener for the "authenticate" event.
     *
     * @param Event\IdentityProviderEvent $event
     */
    abstract public function onAuthenticate(Event\IdentityProviderEvent $event);

    /**
     * Listener for the "adapter.authenticate.pre" event.
     *
     * @param Event\IdentityAdapterEvent $event
     */
    abstract public function onAdapterPreAuthenticate(Event\IdentityAdapterEvent $event);

    /**
     * Listener for the "adapter.authenticate" event.
     *
     * @param Event\IdentityAdapterEvent $event
     */
    abstract public function onAdapterAuthenticate(Event\IdentityAdapterEvent $event);
}
