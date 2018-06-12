<?php

namespace Detail\Auth\Identity\Listener;

use Detail\Auth\Identity\Event;
use Detail\Auth\Identity\Exception;

class RoutesListener extends BaseAuthenticationListener
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->setRules($rules);
    }

    /**
     * Set the rules (it overrides any existing rules)
     *
     * @param  array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param string $adapterClass
     * @return array|null
     */
    public function getRules($adapterClass = null)
    {
        $rules = $this->rules;

        if ($adapterClass !== null) {
            $rules = isset($rules[$adapterClass]) ? $rules[$adapterClass] : null;
        }

        return $rules;
    }

    /**
     * Listener for the "authenticate.pre" event.
     *
     * @param Event\IdentityProviderEvent $event
     */
    public function onPreAuthenticate(Event\IdentityProviderEvent $event)
    {
    }

    /**
     * Listener for the "authenticate" event.
     *
     * @param Event\IdentityProviderEvent $event
     */
    public function onAuthenticate(Event\IdentityProviderEvent $event)
    {
    }

    /**
     * Listener for the "adapter.authenticate.pre" event.
     *
     * @param Event\IdentityAdapterEvent $event
     * @return boolean
     */
    public function onAdapterPreAuthenticate(Event\IdentityAdapterEvent $event)
    {
        $adapter = $event->getParam(Event\IdentityProviderEvent::PARAM_ADAPTER);
        $adapterClass = get_class($adapter);

        $enabledRoutes = $this->getRules($adapterClass);

        // If no rules apply or all adapters are enabled for this route,
        // we don't need to stop the authentication.
        if (!is_array($enabledRoutes)
            || in_array('*', $enabledRoutes)
        ) {
            return true;
        }

        $mvcEvent = $event->getMvcEvent();

        if ($mvcEvent === null) {
            throw new Exception\RuntimeException('Event is missing MvcEvent object');
        }

        $matchedRoute = $mvcEvent->getRouteMatch();

        // If there are rules but we can't determine the route,
        // we need to stop the authentication by this adapter.
        if ($matchedRoute === null) {
            return false;
        }

        $matchedRouteName = $matchedRoute->getMatchedRouteName();
        $matchedRouteRule = null;

        foreach ($enabledRoutes as $routeRule) {
            if (fnmatch($routeRule, $matchedRouteName, FNM_CASEFOLD)) {
                $matchedRouteRule = $routeRule;
                break;
            }
        }

        return $matchedRouteRule !== null;
    }

    /**
     * Listener for the "adapter.authenticate" event.
     *
     * @param Event\IdentityAdapterEvent $event
     */
    public function onAdapterAuthenticate(Event\IdentityAdapterEvent $event)
    {
    }
}
