<?php

namespace Detail\Auth\Identity\Event;

use Zend\EventManager\Event;
//use Zend\Stdlib\Parameters;

use Detail\Auth\Service\MvcEventAwareInterface;
use Detail\Auth\Service\MvcEventAwareTrait;

class IdentityEvent extends Event implements
    IdentityEventInterface,
    MvcEventAwareInterface
{
    use MvcEventAwareTrait;

    const PARAM_ADAPTER  = 'adapter';
    const PARAM_IDENTITY = 'identity';
    const PARAM_RESULT   = 'result';
    const PARAM_VALID    = 'valid';
}
