<?php

namespace Detail\Auth\Identity\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Mvc\MvcEvent;

use Detail\Auth\Identity\ThreeScaleResult as Result;

class ThreeScaleReportingListener implements
    ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var Result
     */
    protected $result;

    /**
     * Attach events to the identity provider.
     *
     * This method attaches listeners to the authenticate.pre and authenticate
     * events of Detail\Auth\Identity\IdentityProvider.
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH,
            array($this, 'onFinish'), -10000 // We want to be late/last
        );
    }

    /**
     * Detach events from the identity provider.
     *
     * This method detaches listeners it has previously attached.
     *
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($listener[$index]);
            }
        }
    }

    /**
     * Report request.
     *
     * @param MvcEvent $event
     */
    public function onFinish(MvcEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getRequest();

        if ($request instanceof HttpRequest) {
//            var_dump($request, $response, $this->getResult());
        }
    }

    /**
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param Result $result
     */
    public function setResult(Result $result)
    {
        $this->result = $result;
    }
}
