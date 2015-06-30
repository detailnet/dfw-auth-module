<?php

namespace Detail\Auth\Identity\Controller;

use ArrayObject;

use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as ConsoleColor;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
//use Zend\View\Model\ConsoleModel;

use ThreeScaleClient;
use ThreeScaleServerError;

use Detail\Auth\Identity\Event;
use Detail\Auth\Identity\Exception;
use Detail\Auth\Identity\ThreeScaleTransactionInterface as Transaction;
use Detail\Auth\Identity\ThreeScaleTransactionRepositoryInterface as TransactionRepository;

class ThreeScaleController extends AbstractActionController
{
    /**
     * @var TransactionRepository
     */
    protected $repository;

    /**
     * @var ThreeScaleClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $serviceId;

    /**
     * @var EventManagerInterface
     */
    protected $threeScaleEvents;

    /**
     * @param TransactionRepository $repository
     * @param ThreeScaleClient $client
     * @param string $serviceId
     */
    public function __construct(
        TransactionRepository $repository,
        ThreeScaleClient $client,
        $serviceId
    ) {
        $this->setRepository($repository);
        $this->setClient($client);
        $this->setServiceId($serviceId);
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

    /**
     * @return ThreeScaleClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ThreeScaleClient $client
     */
    public function setClient(ThreeScaleClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param string $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * Retrieve the event manager instance.
     *
     * Lazy-initializes one if none present.
     *
     * @return EventManagerInterface
     */
    public function getThreeScaleEventManager()
    {
        if (!$this->threeScaleEvents) {
            $this->setThreeScaleEventManager(new EventManager());
        }

        return $this->threeScaleEvents;
    }

    /**
     * Set the event manager instance.
     *
     * @param EventManagerInterface $events
     */
    public function setThreeScaleEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(
            array(
                __CLASS__,
                get_class($this),
            )
        );

        $this->threeScaleEvents = $events;
    }

    /**
     * @return void
     */
    public function reportTransactionsAction()
    {
        $transactionRepository = $this->getRepository();
        $transactions = $transactionRepository->findAll();

        // 3scale allows up to 1000 transactions to be reported in a single request.
        $maxBatchLength = 1000;

        // 3scale seems to limit the request body size to 1 MB (nginx's default).
        // When the body exceeds this limit, a 413 error (Request Entity Too Large) is returned.
        // We have to make sure, we're not reporting too many (big) transactions at once...
        $maxBatchBodySize = (1000 * 1000) - 2000; // 2000 bytes/chars margin to account for other params...

        $batches = array();
        $batchNumber = 0;

        $skippedTransactions = array();
        $unbatchedTransactions = $transactions;

        // We're splitting our list of transactions in respective batches (considering the above limits).
        while (count($unbatchedTransactions) > 0) {
            $batchNumber += 1;
            $batchLength = 0;
            $batchBodySize = 0;

            foreach ($unbatchedTransactions as $i => $transaction) {
                $transactionSize = $transaction->estimateSize();

                // When a single transaction already exceeds the maximum batch size...
                if ($transactionSize > $maxBatchBodySize) {
                    // ...just skip it (will be skipped in all future runs as well, until a human intervenes...)
                    $skippedTransactions[] = $transaction;
                    unset($unbatchedTransactions[$i]);
                    continue;
                }

                // When the maximum batch length or maximum batch size are be reached...
                if ($batchLength >= $maxBatchLength
                    || ($batchBodySize + $transactionSize) > $maxBatchBodySize
                ) {
                    // ...continue with the next batch
                    break;
                }

                $batchLength += 1;
                $batchBodySize += $transactionSize;

                $batches[$batchNumber][] = $transaction;
                unset($unbatchedTransactions[$i]);
            }
        }

        $this->writeConsoleLine(
            sprintf(
                'Reporting %d transaction(s) in %d batch(es)',
                count($transactions),
                count($batches)
            )
        );

        $events = $this->getThreeScaleEventManager();

        foreach ($batches as $batchNumber => $transactions) {
            try {
                $success = true;
                $message = sprintf(
                    'Batch %d: Reported %d transaction(s)',
                    $batchNumber,
                    count($transactions)
                );

                $this->reportTransactions($transactions);
                $this->writeConsoleLine($message, ConsoleColor::LIGHT_GREEN);
            } catch (Exception\RuntimeException $e) {
                $success = false;
                $message = sprintf(
                    'Batch %d: Failed to report %d transaction(s): %s (skipping deletion)',
                    $batchNumber,
                    count($transactions),
                    $e->getMessage()
                );

                $this->writeConsoleLine($message, ConsoleColor::LIGHT_RED);
            }

            $events->trigger(
                $this->prepareEvent(
                    Event\ThreeScaleEvent::EVENT_REPORT_TRANSACTIONS,
                    $transactions,
                    $message,
                    $success
                )
            );

            if (!$success) {
                // Don't delete the transactions of they're not reported...
                continue;
            }

            try {
                $success = true;
                $message = sprintf(
                    'Batch %d: Deleted %d transaction(s)',
                    $batchNumber,
                    count($transactions)
                );

                $transactionRepository->remove($transactions);
                $this->writeConsoleLine($message, ConsoleColor::LIGHT_GREEN);
            } catch (\Exception $e) {
                $success = false;
                $message = sprintf(
                    'Batch %d: Failed to delete %d transaction(s): %s ' .
                    '(Caution: These transactions will be reported again!)',
                    $batchNumber,
                    count($transactions),
                    $e->getMessage()
                );

                $this->writeConsoleLine($message, ConsoleColor::LIGHT_RED);
            }

            $events->trigger(
                $this->prepareEvent(
                    Event\ThreeScaleEvent::EVENT_DELETE_TRANSACTIONS,
                    $transactions,
                    $message,
                    $success
                )
            );
        }

        if (count($skippedTransactions) > 0) {
            $message = sprintf(
                'Skipped reporting of %d transaction(s) because they exceed the maximum allowed size of %s bytes',
                count($transactions),
                $maxBatchBodySize
            );

            $this->writeConsoleLine($message, ConsoleColor::LIGHT_YELLOW);

            $events->trigger(
                $this->prepareEvent(
                    Event\ThreeScaleEvent::EVENT_SKIP_TRANSACTIONS,
                    $skippedTransactions,
                    $message,
                    false
                )
            );
        }
    }

    /**
     * @param Transaction[] $transactions
     */
    protected function reportTransactions(array $transactions)
    {
        $client = $this->getClient();
        $transactionData = array();

        foreach ($transactions as $transaction) {
            $transactionData[] = array(
                'app_id' => $transaction->getAppId(),
                'timestamp' => $transaction->getReceivedOn()->getTimestamp(),
                'usage' => $transaction->getUsage(),
                'log' => array(
                    'request' => $transaction->getRequest(),
                    'response' => $transaction->getResponse(),
                    'code' => $transaction->getResponseCode(),
                ),
            );
        }

        try {
            $response = @$client->report(
                $transactionData,
                $this->getServiceId()
            );
        } catch (ThreeScaleServerError $e) {
            throw new Exception\RuntimeException(
                sprintf(
                    '3scale seems to be unavailable: %s',
                    $e->getMessage()
                ),
                0,
                $e
            );
        } catch (\Exception $e) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Error: %s',
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        if (!$response->isSuccess()) {
            throw new Exception\RuntimeException($response->getErrorMessage());
        }
    }

    /**
     * @return ConsoleRequest
     */
    protected function getConsoleRequest()
    {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest) {
            throw new Exception\RuntimeException('You can only use this action from a console');
        }

        return $request;
    }

    /**
     * @param string $message
     * @param integer $color
     */
    protected function writeConsoleLine($message, $color = ConsoleColor::LIGHT_BLUE)
    {
//        /** @var ConsoleRequest $request */
//        $request = $this->getRequest();
//        $isVerbose = $request->getParam('verbose', false) || $request->getParam('v', false);

        $console = $this->getServiceLocator()->get('console');

        if (!$console instanceof Console) {
            throw new Exception\RuntimeException(
                'Cannot obtain console adapter. Are we running in a console?'
            );
        }

//        if ($isVerbose) {
            $console->writeLine($message, $color);
//        }
    }

    /**
     * @param string $name
     * @param array $transactions
     * @param string|null $message
     * @param bool $success
     * @return Event\ThreeScaleEvent
     */
    protected function prepareEvent($name, array $transactions, $message = null, $success = true)
    {
        $eventParams = array(
            Event\ThreeScaleEvent::PARAM_TRANSACTIONS => $transactions,
            Event\ThreeScaleEvent::PARAM_MESSAGE      => $message,
            Event\ThreeScaleEvent::PARAM_SUCCESS      => $success,
        );

        $event = new Event\ThreeScaleEvent($name, $this, $this->prepareEventParams($eventParams));

        return $event;
    }

    /**
     * Prepare event parameters.
     *
     * Ensures event parameters are created as an array object, allowing them to be modified
     * by listeners and retrieved.
     *
     * @param array $params
     * @return ArrayObject
     */
    protected function prepareEventParams(array $params)
    {
        if (empty($params)) {
            return $params;
        }

        return $this->getEventManager()->prepareArgs($params);
    }
}
