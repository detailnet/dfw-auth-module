<?php

namespace Detail\Auth\Identity\Listener;

use DateTime;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

use Detail\Auth\Identity\ThreeScaleResult as Result;
use Detail\Auth\Identity\ThreeScaleTransactionRepositoryInterface as TransactionRepository;

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
     * @var TransactionRepository
     */
    protected $repository;

    /**
     * @param TransactionRepository $repository
     */
    public function __construct(TransactionRepository $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * Attach events.
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH,
            array($this, 'onFinish'),
            -10000 // We want to be late/last
        );
    }

    /**
     * Detach events.
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
        $result = $this->getResult();
        $request = $event->getRequest();

        // Only log successful request with usage
        if (!$request instanceof HttpRequest
            || !$result->isValid()
            || !$result->hasUsage()
        ) {
            return;
        }

        /** @var HttpResponse $response */
        $response = $event->getResponse();

        $transactionRepository = $this->getRepository();
        $transaction = $transactionRepository->create(
            array(
                'app_id' => $result->getAppId(),
                'received_on' => new DateTime(),
                'usage' => $result->getUsage(),
                'request' => $request->toString(),
                'response' => $response->toString(),
                'response_code' => $response->getStatusCode(),
            )
        );

        $transactionRepository->add($transaction);
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

    /**
     * @return TransactionRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param TransactionRepository $repository
     */
    public function setRepository(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }
}
