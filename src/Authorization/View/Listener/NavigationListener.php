<?php

namespace Detail\Auth\Authorization\View\Listener;

use Zend\EventManager\EventInterface;
use Zend\Navigation\Page\AbstractPage;

use Detail\Auth\Authorization\AuthorizationServiceInterface;

class NavigationListener
{
    /**
     * @var AuthorizationServiceInterface
     */
    protected $authorizationService;

    /**
     * @param AuthorizationServiceInterface $authorizationService
     */
    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * @param EventInterface $event
     * @return bool
     */

    public function accept(EventInterface $event)
    {
        // This is the default behavior (allow access, if not otherwise specified)
        $accept = true;

        $page = $event->getParam('page');

        if ($page instanceof AbstractPage && $page->getPermission() !== null) {
            $accept = $this->authorizationService->isAllowed($page->getPermission(), $page);
        }

        return $accept;
    }
}
