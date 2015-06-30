<?php

namespace Detail\Auth\Identity\Event;

use Zend\EventManager\Event;

class ThreeScaleEvent extends Event
{
    const EVENT_REPORT_TRANSACTIONS = 'transactions.report';
    const EVENT_DELETE_TRANSACTIONS = 'transactions.delete';
    const EVENT_SKIP_TRANSACTIONS   = 'transactions.skip';

    const PARAM_TRANSACTIONS = 'transactions';
    const PARAM_MESSAGE      = 'message';
    const PARAM_SUCCESS      = 'success';
}
