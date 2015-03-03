<?php

namespace Detail\Auth\Identity;

use ArrayObject;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

use ZfcRbac\Identity\IdentityProviderInterface as ZfcRbacIdentityProviderInterface;

use Detail\Log\Service\LoggerAwareTrait;

class IdentityProvider implements
    IdentityProviderInterface,
    ZfcRbacIdentityProviderInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var AdapterManager
     */
    protected $adapters;

    /**
     * @var string
     */
    protected $defaultAdapterType = '3scale';

    /**
     * @var Identity
     */
    protected $identity;

    /**
     * @var boolean
     */
    protected $authenticated = false;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var array
     */
    protected $eventParams = array();

    /**
     * @return AdapterManager
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * @param AdapterManager $adapters
     */
    public function setAdapters($adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * @return string
     */
    public function getDefaultAdapterType()
    {
        return $this->defaultAdapterType;
    }

    /**
     * @param string $defaultAdapterType
     */
    public function setDefaultAdapterType($defaultAdapterType)
    {
        $this->defaultAdapterType = $defaultAdapterType;
    }

    /**
     * @return Adapter\AdapterInterface
     */
    public function getDefaultAdapter()
    {
        $adapters = $this->getAdapters();
        $adapterType = $this->getDefaultAdapterType();

        if (!$adapters->hasAdapter($adapterType)) {
            throw new Exception\RuntimeException(
                sprintf('No adapter registered with type "%s"', $adapterType)
            );
        }

        return $adapters->getAdapter($adapterType);
    }

    /**
     * @param AdapterManager $adapters
     */
    public function __construct(AdapterManager $adapters)
    {
        $this->setAdapters($adapters);
    }

    /**
     * Retrieve the event manager instance.
     *
     * Lazy-initializes one if none present.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }

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
        $events->setIdentifiers(
            array(
                __CLASS__,
                get_class($this),
                __NAMESPACE__ . '\IdentityProviderInterface'
            )
        );

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
     * @param Adapter\AdapterInterface $adapter
     * @return ResultInterface
     */
    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
        if ($adapter === null) {
            $adapter = $this->getDefaultAdapter();
        }

        $preEventParams = array(
            IdentityProviderEvent::PARAM_ADAPTER => $adapter,
        );

        $events = $this->getEventManager();

        $preEvent = $this->prepareEvent(IdentityProviderEvent::EVENT_PRE_AUTHENTICATE, $preEventParams);
        $events->triggerUntil($preEvent, function ($result) {
            /** @todo Give listeners the opportunity to provide an identity (in which case we wouldn't continue with authentication) */
            // Don't authenticate when a listener returns false
            return ($result === false);
        });

        try {
            $result = $adapter->authenticate();
            $this->authenticated = true;

            if ($result->isValid()) {
                $this->identity = $result->getIdentity() ?: new Identity('admin');
            }
        } catch (Exception\AuthenticationException $e) {
            $result = new Result(false, array($e->getMessage()));

            /** @todo We should handle logging through an event (authentication.error) */
            $logData = array(
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            );

            $this->log('Authentication failed', LogLevel::ALERT, $logData);
        }

        $postEventParams = array_merge(
            $preEventParams,
            array(
                IdentityProviderEvent::PARAM_IDENTITY => $this->identity,
                IdentityProviderEvent::PARAM_RESULT   => $result,
                IdentityProviderEvent::PARAM_VALID    => $result->isValid(),
            )
        );

        $postEvent = $this->prepareEvent(IdentityProviderEvent::EVENT_AUTHENTICATE, $postEventParams);
        $events->trigger($postEvent);

        return $result;
    }

    /**
     * Get the identity.
     *
     * @return Identity|null
     */
    public function getIdentity()
    {
        if ($this->authenticated === false) {
            $this->authenticate();
        }

        return $this->identity;
    }

    protected function prepareEvent($name, array $params)
    {
        $event = new IdentityProviderEvent($name, $this, $this->prepareEventParams($params));

        return $event;
    }

    /**
     * Prepare event parameters.
     *
     * Ensures event parameters are created as an array object, allowing them to be modified
     * by listeners and retrieved.
     *
     * @param  array $params
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
}
