<?php

namespace Detail\Auth\Factory\Options;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

use Detail\Auth\Options\ModuleOptions;

class ModuleOptionsFactory implements
    FactoryInterface
{
    /**
     * Create ModuleOptions
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        if (!isset($config['detail_auth'])) {
            throw new ServiceNotCreatedException('Config for Detail\Auth is not set');
        }

        return new ModuleOptions($config['detail_auth']);
    }
}
