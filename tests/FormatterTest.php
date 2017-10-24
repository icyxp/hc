<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Icyboy\HealthCheck\HealthManager;
use Icyboy\HealthCheck\Formatter;
use Icyboy\HealthCheck\HealthException;

class FormatterTest extends PHPUnit_Framework_TestCase
{
    private $fail;
    private $success;

    public function setUp()
    {
        $this->fail        = new HealthManager();
        $config["version"] = 123;

        $this->fail->addCheck('info', function () {
            return 'this is some data';
        });
        $this->fail->addCheck('pass', function () {
            return true;
        });
        $this->fail->addCheck('fail', function () {
            return false;
        });
        $this->fail->addCheck("mysql", function () use ($config) {
            if ($config["version"] == "456") {
                return $config;
            } else {
                throw new HealthException("something was wrong");
            }
        });

        $this->success = new HealthManager();
        $this->success->addCheck('info', function () {
            return 'this is some data';
        });
        $this->success->addCheck('pass', function () {
            return true;
        });
        $this->success->addCheck("mysql", function() use ($config) {
            if ($config["version"] == "123") {
                return $config;
            } else {
                throw new HealthException("something was wrong");
            }
        });
    }

    public function testToJsonFailure()
    {
        $expected = '{"status":"DOWN","info":{"status":"UP","message":"this is some data"},"pass":{"status":"UP"},"fail":{"status":"DOWN"},"mysql":{"status":"DOWN","message":"something was wrong"}}';

        $this->assertEquals($expected, Formatter::toJson($this->fail));
    }

    public function testToJsonSuccess()
    {
        $expected = '{"status":"UP","info":{"status":"UP","message":"this is some data"},"pass":{"status":"UP"},"mysql":{"status":"UP","version":123}}';

        $this->assertEquals($expected, Formatter::toJson($this->success));
    }

    public function testAcceptsJson()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $this->assertFalse(Formatter::acceptJson());

        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->assertTrue(Formatter::acceptJson());

        // Technically this is not compliant, but we'll accept it anyway.
        $_SERVER['HTTP_ACCEPT'] = 'text/html; Application/JSON';
        $this->assertTrue(Formatter::acceptJson());

        unset($_SERVER['HTTP_ACCEPT']);
        $this->assertFalse(Formatter::acceptJson());
    }
}
