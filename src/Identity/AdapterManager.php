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
     * @param string $name
     * @return boolean
     * @todo Don't look in peering service managers
     */
    public function has($name)
    {
        return parent::has($name);
    }

    /**
     * @param string $type
     * @return boolean
     */
    public function hasAdapter($type)
    {
        return $this->has($type);
    }

    /**
     * @param string $name
     * @param array|null $options
     * @return Adapter\AdapterInterface
     * @todo Don't look in peering service managers
     */
    public function get($name, array $options = null)
    {
        return parent::get($name, $options);
    }

    /**
     * @param string $type
     * @param array|null $options
     * @return Adapter\AdapterInterface
     */
    public function getAdapter($type, array $options = null)
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
