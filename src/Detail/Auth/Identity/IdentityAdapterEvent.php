<?php

namespace Detail\Auth\Identity;

use Zend\EventManager\Event;

//use Zend\Stdlib\Parameters;

class IdentityAdapterEvent extends Event
{
    const EVENT_PRE_AUTHENTICATE = 'adapter.authenticate.pre';
    const EVENT_AUTHENTICATE     = 'adapter.authenticate';

    const PARAM_ADAPTER  = 'adapter';
    const PARAM_IDENTITY = 'identity';
    const PARAM_RESULT   = 'result';
    const PARAM_VALID    = 'valid';
}
