<?php

namespace Detail\Auth\Identity\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as ConsoleColor;
use Zend\Mvc\Controller\AbstractActionController;
//use Zend\View\Model\ConsoleModel;

use ThreeScaleClient;
use ThreeScaleResponse;
use ThreeScaleServerError;

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
     * @return void
     */
    public function reportTransactionsAction()
    {
        $transactionRepository = $this->getRepository();
        $transactions = $transactionRepository->findAll();

        $batches = array();

        // 3scale allows up to 1000 transactions to be reported in a single request.
        // We're splitting our list of transactions in respective batches.
        foreach ($transactions as $i => $transaction) {
            $batchNumber = (int) ceil(($i + 1) / 1000);
            $batches[$batchNumber][] = $transaction;
        }

        /** @todo Log output to application log */

        $this->writeConsoleLine(
            sprintf(
                'Reporting %d transaction(s) in %d batch(es)',
                count($transactions),
                count($batches)
            )
        );

        foreach ($batches as $batchNumber => $transactions) {
            try {
                $this->reportTransactions($transactions);
                $this->writeConsoleLine(
                    sprintf(
                        'Batch %d: Reported %d transaction(s)',
                        $batchNumber,
                        count($transactions)
                    ),
                    ConsoleColor::LIGHT_GREEN
                );
            } catch (Exception\RuntimeException $e) {
                $this->writeConsoleLine(
                    sprintf(
                        'Batch %d: Failed to report %d transaction(s): ' . $e->getMessage(),
                        $batchNumber,
                        count($transactions)
                    ),
                    ConsoleColor::LIGHT_RED
                );
            }
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
}
