<?php

namespace Detail\Auth\Identity\Event;

use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;

interface IdentityEventInterface extends EventInterface
{
    /**
     * @return MvcEvent
     */
    public function getMvcEvent();
}
