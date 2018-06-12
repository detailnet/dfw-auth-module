<?php

namespace Detail\Auth\Identity;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;

class AdapterManager extends AbstractPluginManager
{
    /**
     * @param string $type
     * @return boolean
     */
    public function hasAdapter($type)
    {
        return $this->has($type);
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
     * Validate an instance
     *
     * @param object $instance
     * @return void
     */
    public function validate($instance)
    {
        if ($instance instanceof Adapter\AdapterInterface) {
            // We're okay
            return;
        }

        throw new InvalidServiceException(
            sprintf(
                'Adapter of type %s is invalid; must implement %s',
                (is_object($instance) ? get_class($instance) : gettype($instance)),
                Adapter\AdapterInterface::CLASS
            )
        );
    }
}
