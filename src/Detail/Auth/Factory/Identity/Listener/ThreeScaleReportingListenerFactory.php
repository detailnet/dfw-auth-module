<?php

namespace Detail\Auth\Factory\Identity\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\Listener\ThreeScaleReportingListener as Listener;

class ThreeScaleReportingListenerFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return Listener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Detail\Auth\Options\ThreeScaleOptions $threeScaleOptions */
        $threeScaleOptions = $serviceLocator->get('Detail\Auth\Options\ThreeScaleOptions');

        /** @var \Detail\Auth\Identity\ThreeScaleTransactionRepositoryInterface $threeScaleTransactionRepository */
        $threeScaleTransactionRepository = $serviceLocator->get($threeScaleOptions->getReporting()->getRepository());

        return new Listener($threeScaleTransactionRepository);
    }
}
