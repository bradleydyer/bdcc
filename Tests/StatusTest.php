<?php

namespace Test;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Status as Bdcc_Status;

/**
 * Test class for Bdcc_Http_Client
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @group Bdcc
 */
class StatusTest extends TestCase
{
    public function getInformationalCodes()
    {
        return array(
            array(Bdcc_Status::HTTP_CONTINUE),
            array(Bdcc_Status::HTTP_SWITCHING_PROTOCOLS),
        );
    }

    public function getSuccessCodes()
    {
        return array(
            array(Bdcc_Status::HTTP_OK),
            array(Bdcc_Status::HTTP_CREATED),
            array(Bdcc_Status::HTTP_ACCEPTED),
            array(Bdcc_Status::HTTP_NON_AUTHORITATIVE_INFORMATION),
        );
    }

    public function getRedirectionCodes()
    {
        return array(
            array(Bdcc_Status::HTTP_MULTIPLE_CHOICES),
            array(Bdcc_Status::HTTP_MOVED_PERMANENTLY),
            array(Bdcc_Status::HTTP_FOUND),
            array(Bdcc_Status::HTTP_SEE_OTHER),
        );
    }

    public function getClientErrorCodes()
    {
        return array(
            array(Bdcc_Status::HTTP_BAD_REQUEST),
            array(Bdcc_Status::HTTP_UNAUTHORIZED),
            array(Bdcc_Status::HTTP_PAYMENT_REQUIRED),
            array(Bdcc_Status::HTTP_NOT_FOUND),
        );
    }

    public function getServerErrorCodes()
    {
        return array(
            array(Bdcc_Status::HTTP_INTERNAL_SERVER_ERROR),
            array(Bdcc_Status::HTTP_NOT_IMPLEMENTED),
            array(Bdcc_Status::HTTP_BAD_GATEWAY),
            array(Bdcc_Status::HTTP_INSUFFICIENT_STORAGE),
        );
    }

    public function testOKStatus()
    {    
        $status = Bdcc_Status::HTTP_OK;

        $this->assertSame(200, $status);
    }

    public function testStatusLabels()
    {
        foreach (Bdcc_Status::$statusLabels as $statusCode => $label) {
            $this->assertSame($label, Bdcc_Status::getStatusLabel($statusCode));
        }
    }

    /**
     * @dataProvider getInformationalCodes
     */
    public function testIsInformational($statusCode)
    {
        $this->assertTrue(Bdcc_Status::isInformational($statusCode));
    }

    /**
     * @dataProvider getSuccessCodes
     */
    public function testIsNotInformational($statusCode)
    {
        $this->assertFalse(Bdcc_Status::isInformational($statusCode));
    }

    /**
     * @dataProvider getSuccessCodes
     */
    public function testIsSuccess($statusCode)
    {
        $this->assertTrue(Bdcc_Status::isSuccess($statusCode));
    }

    /**
     * @dataProvider getInformationalCodes
     */
    public function testIsNotSuccess($statusCode)
    {
        $this->assertFalse(Bdcc_Status::isSuccess($statusCode));
    }

    /**
     * @dataProvider getRedirectionCodes
     */
    public function testIsRedirection($statusCode)
    {
        $this->assertTrue(Bdcc_Status::isRedirection($statusCode));
    }

    /**
     * @dataProvider getInformationalCodes
     */
    public function testIsNotRedirection($statusCode)
    {
        $this->assertFalse(Bdcc_Status::isRedirection($statusCode));
    }

    /**
     * @dataProvider getClientErrorCodes
     */
    public function testIsClientError($statusCode)
    {
        $this->assertTrue(Bdcc_Status::isClientError($statusCode));
    }

    /**
     * @dataProvider getInformationalCodes
     */
    public function testIsNotClientError($statusCode)
    {
        $this->assertFalse(Bdcc_Status::isClientError($statusCode));
    }

    /**
     * @dataProvider getServerErrorCodes
     */
    public function testIsServerError($statusCode)
    {
        $this->assertTrue(Bdcc_Status::isServerError($statusCode));
    }

    /**
     * @dataProvider getInformationalCodes
     */
    public function testIsNotServerError($statusCode)
    {
        $this->assertFalse(Bdcc_Status::isServerError($statusCode));
    }
}
