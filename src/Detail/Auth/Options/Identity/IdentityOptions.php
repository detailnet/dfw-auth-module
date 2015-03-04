<?php

namespace Detail\Auth\Options\Identity;

use Detail\Core\Options\AbstractOptions;

use Detail\Auth\Exception;

class IdentityOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $defaultAdapter;

    /**
     * @var string[]
     */
    protected $adapterFactories = array();

    /**
     * @var array
     */
    protected $adapters = array();

    /**
     * @var string[]
     */
    protected $listeners = array();

    /**
     * @return string
     */
    public function getDefaultAdapter()
    {
        return $this->defaultAdapter;
    }

    /**
     * @param string $defaultAdapter
     */
    public function setDefaultAdapter($defaultAdapter)
    {
        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * @return string[]
     */
    public function getAdapterFactories()
    {
        return $this->adapterFactories;
    }

    /**
     * @param string[] $adapterFactories
     */
    public function setAdapterFactories(array $adapterFactories)
    {
        $this->adapterFactories = $adapterFactories;
    }

    /**
     * @param string $type
     * @param string $optionsClass
     * @return array|\Detail\Core\Options\AbstractOptions|null
     * @throws Exception\RuntimeException
     */
    public function getAdapterOptions($type, $optionsClass = null)
    {
        $adapters = $this->getAdapters();
        $adapter = null;

        if (isset($adapters[$type])) {
            $adapter = $adapters[$type];

            if ($optionsClass !== null) {
                if (!class_exists($optionsClass)) {
                    throw new Exception\RuntimeException(
                        sprintf(
                            'Options class "%s" for adapter type "%s" does not exist',
                            $optionsClass,
                            $type
                        )
                    );
                }

                $adapter = new $optionsClass($adapter);
            }
        }

        return $adapter;
    }

    /**
     * @return array
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * @param array $adapters
     */
    public function setAdapters(array $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * @return string[]
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param string[] $listeners
     */
    public function setListeners(array $listeners)
    {
        $this->listeners = $listeners;
    }
}
