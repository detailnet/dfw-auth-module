<?php

namespace Detail\Auth\Identity;

use Zend\ServiceManager\AbstractPluginManager;

use Detail\Auth\Exception;

/**
 * Plugin manager implementation for identity adapters.
 *
 * Enforces that senders retrieved are instances of AdapterInterface.
 */
class AdapterManager extends AbstractPluginManager
{
    /**
     * Whether or not to share by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * {@inheritDoc}
     */
    public function has($name, $checkAbstractFactories = true, $usePeeringServiceManagers = true)
    {
        return parent::has($name, $checkAbstractFactories, false); // Don't look in peering service managers
    }

    /**
     * {@inheritDoc}
     */
    public function hasAdapter($type)
    {
        return $this->has($type);
    }

    /**
     * {@inheritDoc}
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        return parent::get($name, $options, false); // Don't look in peering service managers
    }

    /**
     * {@inheritDoc}
     */
    public function getAdapter($type, $options = array())
    {
        return $this->get($type, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Adapter\AdapterInterface) {
            // We're okay
            return;
        }

        throw new Exception\RuntimeException(
            sprintf(
                'Adapter of type %s is invalid; must implement %s',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
                Adapter\AdapterInterface::CLASS
            )
        );
    }
}
