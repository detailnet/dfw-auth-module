<?php

namespace Detail\Auth\Authorization\Adapter;

use ZfcRbac\Service as ZfcRbac;

class ZfcRbacAdapter implements
    AdapterInterface
{
    /**
     * @var ZfcRbac\AuthorizationServiceInterface
     */
    protected $rbacService;

    /**
     * @param ZfcRbac\AuthorizationServiceInterface $rbacService
     */
    public function __construct(ZfcRbac\AuthorizationServiceInterface $rbacService)
    {
        $this->rbacService = $rbacService;
    }

    /**
     * @param string $action
     * @param mixed $context
     * @return boolean
     */
    public function isAllowed($action, $context = null)
    {
        return $this->rbacService->isGranted($action, $context);
    }
}
