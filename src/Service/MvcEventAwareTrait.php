<?php

namespace Detail\Auth\Service;

use Zend\Mvc\MvcEvent;

trait MvcEventAwareTrait
{
    /**
     * @var MvcEvent
     */
    protected $mvcEvent;

    /**
     * @return MvcEvent
     */
    public function getMvcEvent()
    {
        return $this->mvcEvent;
    }

    /**
     * @param MvcEvent $mvcEvent
     */
    public function setMvcEvent(MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
    }
}
