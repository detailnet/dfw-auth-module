<?php

namespace Detail\Auth\Identity\Adapter;

use ArrayObject;

//use Zend\EventManager\EventManager;
use Detail\Auth\Identity\Result;
use Detail\Auth\Identity\ResultInterface;
use Zend\EventManager\EventManagerInterface;

use Detail\Auth\Identity\IdentityAdapterEvent;

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
    protected $eventParams = array();

    /**
     * Retrieve the event manager instance.
     *
     * Lazy-initializes one if none present.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
//        if (!$this->events) {
//            $this->setEventManager(new EventManager());
//        }

        return $this->events;
    }

    /**
     * Set the event manager instance.
     *
     * @param EventManagerInterface $events
     * @return self
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
        return $this;
    }

    /**
     * @param array $params
     * @return self
     */
    public function setEventParams(array $params)
    {
        $this->eventParams = $params;
        return $this;
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
        $preEventParams = array(
            IdentityAdapterEvent::PARAM_ADAPTER => $this,
        );

        $events = $this->getEventManager();

        $preEvent = $this->prepareEvent(IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE, $preEventParams);
        $eventResults = $events->triggerUntil($preEvent, function ($result) {
            // Stop the execution when a listeners returns false
            return ($result === false);
        });

        // Don't authenticate when a listener stops the execution of the event
        if ($eventResults->stopped()) {
            return new Result(
                false,
                null,
                array(
                    sprintf(
                        'Authentication was stopped by a listener during "%s"',
                        IdentityAdapterEvent::EVENT_PRE_AUTHENTICATE
                    )
                )
            );
        }

        $result = $this->auth();

        $postEventParams = array_merge(
            $preEventParams,
            array(
                IdentityAdapterEvent::PARAM_IDENTITY => $result->getIdentity(),
                IdentityAdapterEvent::PARAM_RESULT   => $result,
                IdentityAdapterEvent::PARAM_VALID    => $result->isValid(),
            )
        );

        $postEvent = $this->prepareEvent(IdentityAdapterEvent::EVENT_AUTHENTICATE, $postEventParams);
        $events->trigger($postEvent);

        return $result;
    }

    /**
     * @param string $name
     * @param array $params
     * @return IdentityAdapterEvent
     */
    protected function prepareEvent($name, array $params)
    {
        $event = new IdentityAdapterEvent($name, $this, $this->prepareEventParams($params));

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

        if (empty($params)) {
            return $params;
        }

        return $this->getEventManager()->prepareArgs($params);
    }

    /**
     * @return ResultInterface
     */
    abstract protected function auth();
}
