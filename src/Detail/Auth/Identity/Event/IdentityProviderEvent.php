<?php

namespace Detail\Auth\Identity\Event;

class IdentityProviderEvent extends IdentityEvent
{
    const EVENT_PRE_AUTHENTICATE = 'authenticate.pre';
    const EVENT_AUTHENTICATE     = 'authenticate';
}
