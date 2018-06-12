<?php

namespace Detail\Auth\Identity\Adapter;

use ArrayObject;

use Zend\EventManager\EventManagerInterface;

use Detail\Auth\Identity\Event;
use Detail\Auth\Identity\Result;
use Detail\Auth\Identity\ResultInterface;

abstract class BaseAdapter implements
    AdapterInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var array
     */
    protected $eventParams = [];

    /**
     * Retrieve the event manager instance.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * Set the event manager instance.
     *
     * @param EventManagerInterface $events
     */
    public function setEventManager(EventManagerInterface $events)
    {
//        $events->setIdentifiers(
//            array(
//                __CLASS__,
//                get_class($this),
//                __NAMESPACE__ . '\IdentityProviderInterface'
//            )
//        );

        $this->events = $events;
    }

    /**
     * @param array $params
     */
    public function setEventParams(array $params)
    {
        $this->eventParams = $params;
    }

    /**
     * @return array
     */
    public function getEventParams()
    {
        return $this->eventParams;
    }

    /**
     * @return ResultInterface
     */
    public function authenticate()
    {
        $preEventParams = [
            Event\IdentityAdapterEvent::PARAM_ADAPTER => $this,
        ];

        $events = $this->getEventManager();

        $preEvent = $this->prepareEvent(Event\IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE, $preEventParams);
        $eventResults = $events->triggerEventUntil(
            function ($result) {
                // Stop the execution when a listeners returns false
                return ($result === false);
            },
            $preEvent
        );

        // Don't authenticate when a listener stops the execution of the event
        if ($eventResults->stopped()) {
            return new Result(
                false,
                null,
                [
                    sprintf(
                        'Authentication was stopped by a listener during "%s"',
                        Event\IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE
                    )
                ]
            );
        }

        $result = $this->auth();

        $postEventParams = array_merge(
            $preEventParams,
            [
                Event\IdentityAdapterEvent::PARAM_IDENTITY => $result->getIdentity(),
                Event\IdentityAdapterEvent::PARAM_RESULT   => $result,
                Event\IdentityAdapterEvent::PARAM_VALID    => $result->isValid(),
            ]
        );

        $postEvent = $this->prepareEvent(Event\IdentityAdapterEvent::EVENT_AUTHENTICATE, $postEventParams);
        $events->triggerEvent($postEvent);

        return $result;
    }

    /**
     * @param string $name
     * @param array $params
     * @return Event\IdentityAdapterEvent
     */
    protected function prepareEvent($name, array $params)
    {
        $event = new Event\IdentityAdapterEvent($name, $this, $this->prepareEventParams($params));

        return $event;
    }

    /**
     * Prepare event parameters.
     *
     * Ensures event parameters are created as an array object, allowing them to be modified
     * by listeners and retrieved.
     *
     * @param array $params
     * @return ArrayObject
     */
    protected function prepareEventParams(array $params)
    {
        $defaultParams = $this->getEventParams();
        $params = array_merge($defaultParams, $params);

        return new ArrayObject($params);
    }

    /**
     * @return ResultInterface
     */
    abstract protected function auth();
}
