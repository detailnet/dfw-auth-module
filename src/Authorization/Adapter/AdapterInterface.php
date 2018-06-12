<?php

namespace Detail\Auth\Authorization\Adapter;

interface AdapterInterface
{
    /**
     * @param string $action
     * @param mixed $context
     * @return boolean
     */
    public function isAllowed($action, $context = null);
}
