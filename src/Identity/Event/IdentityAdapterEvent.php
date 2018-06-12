<?php

namespace Detail\Auth\Identity\Event;

class IdentityAdapterEvent extends IdentityEvent
{
    const EVENT_PRE_AUTHENTICATE = 'adapter.authenticate.pre';
    const EVENT_AUTHENTICATE     = 'adapter.authenticate';
}
