<?php

namespace Detail\Auth\Options\Authorization;

use Zend\Stdlib\AbstractOptions;

use Detail\Auth\Exception;

class AuthorizationOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $adapters = [];

    /**
     * @return string
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param string $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $type
     * @param string $optionsClass
     * @return array|AbstractOptions|null
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
}
