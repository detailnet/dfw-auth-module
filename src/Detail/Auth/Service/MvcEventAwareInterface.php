<?php

namespace Detail\Auth\Service;

use Zend\Mvc\MvcEvent;

interface MvcEventAwareInterface
{
    /**
     * @param MvcEvent $mvcEvent
     */
    public function setMvcEvent(MvcEvent $mvcEvent);
}
