<?php

namespace Detail\Auth\Authorization\ZfcRbac\Guard;

use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;

use ZF\MvcAuth\Authorization\DefaultResourceResolverListener;

use ZfcRbac\Guard\AbstractGuard as BaseGuard;
use ZfcRbac\Guard\ProtectionPolicyTrait;
use ZfcRbac\Service\AuthorizationServiceInterface;

class RestGuard extends BaseGuard
{
    use ProtectionPolicyTrait;

    /**
     * Event priority
     */
    const EVENT_PRIORITY = -600;

    /**
     * MVC event to listen
     */
    const EVENT_NAME = MvcEvent::EVENT_ROUTE;

    /**
     * @var AuthorizationServiceInterface
     */
    protected $authorizationService;

    /**
     * @var DefaultResourceResolverListener
     */
    protected $resourceResolver;

    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @param AuthorizationServiceInterface $authorizationService
     * @param DefaultResourceResolverListener $resourceResolver
     * @param array $rules
     */
    public function __construct(
        AuthorizationServiceInterface $authorizationService,
        DefaultResourceResolverListener $resourceResolver,
        array $rules = array()
    ) {
        $this->authorizationService = $authorizationService;
        $this->resourceResolver = $resourceResolver;
        $this->setRules($rules);
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        /** @todo Validate rules */
        $this->rules = $rules;
    }

    /**
     * @param MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        $rules = $this->getRules();
        $routeMatch = $event->getRouteMatch();
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return true;
        }

        $method = $request->getMethod();
        $resource = $this->resourceResolver->buildResourceString($routeMatch, $request);

        // If no resource could be identified, it is considered as granted (this guard does not apply).
        if (!$resource) {
            return true;
        }

        list($controller, $group) = explode('::', $resource);

        // If it's an RPC call and not a REST controller, , it is considered as granted (this guard does not apply).
        if (!in_array($group, array('entity', 'collection'))) {
            return true;
        }

        // If no rules apply, it is considered as granted or not based on the protection policy.
        if (!isset($rules[$controller][$group][$method])) {
            return $this->getProtectionPolicy() === self::POLICY_ALLOW;
        }

        $actions = $rules[$controller][$group][$method];

        if (is_string($actions)) {
            $actions = array($actions);
        }

        if (is_array($actions)) {
            $and = true;

            foreach ($actions as $action) {
                $and = $and && $this->authorizationService->isGranted($action);
            }

            $actions = $and;
        }

        return (bool) $actions;
    }
}
