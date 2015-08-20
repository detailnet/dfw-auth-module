<?php

namespace DetailTest\Auth\Identity;

use DateTime;

use PHPUnit_Framework_TestCase as TestCase;

use Detail\Auth\Identity\ThreeScaleTransactionTrait;

class ThreeScaleTransactionTraitTest extends TestCase
{
    /**
     * @return array
     */
    public function provideTransactions()
    {
        return array(
            // Original transaction size: 151
            array(
                140,
                'rq34567890',
                'rs34567890',
                140,
            ),
            // Original transaction size: 186
            array(
                165,
                'request body',
                'response päylöad?',
                141,
            ),
            // Original transaction size: 151
            array(
                100,
                'rq34567890',
                'rs34567890',
                false, // It's impossible to limit a transaction to such a low threshold
            ),
        );
    }

    /**
     * @param int $threshold
     * @param string $request
     * @param string $response
     * @param int|bool $expectedSize
     * @dataProvider provideTransactions
     */
    public function testTruncateSizeTo($threshold, $request, $response, $expectedSize)
    {
        $transaction = $this->getMockBuilder('Detail\Auth\Identity\ThreeScaleTransactionTrait')
            ->setMethods(array('getAppId', 'getReceivedOn', 'getUsage', 'getResponseCode', 'getRequest', 'getResponse'))
            ->getMockForTrait();

        $transaction
            ->expects($this->any())
            ->method('getAppId')
            ->will($this->returnValue('app-id'));
        $transaction
            ->expects($this->any())
            ->method('getReceivedOn')
            ->will($this->returnValue(DateTime::createFromFormat('Y-m-d H:i:s', '2015-08-20 12:24:00')));
        $transaction
            ->expects($this->any())
            ->method('getUsage')
            ->will($this->returnValue(array('hits' => 1)));
        $transaction
            ->expects($this->any())
            ->method('getResponseCode')
            ->will($this->returnValue(200));
        $transaction
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $transaction
            ->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        /** @var ThreeScaleTransactionTrait $transaction */

        if ($expectedSize === false) {
            $this->setExpectedException('Detail\Auth\Identity\Exception\RuntimeException');
        }

        $estimatedSize = $transaction->prepareForReporting($threshold);

        if ($expectedSize !== false) {
            $this->assertEquals($expectedSize, $estimatedSize);
        }
    }
}
