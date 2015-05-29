<?php

namespace Detail\Auth\Identity\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as ConsoleColor;
use Zend\Mvc\Controller\AbstractActionController;
//use Zend\View\Model\ConsoleModel;

use Detail\Auth\Identity\Exception;

class ThreeScaleController extends AbstractActionController
{
    public function __construct()
    {
    }

    /**
     * @return void
     */
    public function reportUsageAction()
    {
//        $request = $this->getConsoleRequest();
//        $value = $request->getParam('value');
//
//        $this->writeConsoleLine($value);
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
