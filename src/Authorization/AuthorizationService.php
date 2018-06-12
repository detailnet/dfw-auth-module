<?php

namespace Detail\Auth\Authorization;

class AuthorizationService implements
    AuthorizationServiceInterface
{
    /**
     * @var Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param Adapter\AdapterInterface $adapter
     */
    public function __construct(Adapter\AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @return Adapter\AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param Adapter\AdapterInterface $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $action
     * @param mixed $context
     * @return boolean
     */
    public function isAllowed($action, $context = null)
    {
        return $this->getAdapter()->isAllowed($action, $context);
    }
}
