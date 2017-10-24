<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Icyboy\HealthCheck\HealthManager;
use Icyboy\HealthCheck\HealthCheck;

class HealthManagerTest extends PHPUnit_Framework_TestCase
{
    public function testBasicUsage()
    {
        $file = __FILE__;

        $manager = new HealthManager();

        // Dynamically add a new healthcheck
        $manager->addCheck("Test that this file exists", function () use ($file) {
            return file_exists($file);
        });

        // Add a healtcheck we created manually
        $healthcheck = new Healthcheck('description', function () {
            return true;
        });
        $manager->addHealthcheck($healthcheck);

        // Verify healthcheck aggregate is passing up to this point
        $this->assertTrue($manager->getStatus());

        // Add a failing healthcheck
        $manager->addCheck('falsy', function () {
            return false;
        });

        // Verify healthcheck aggregate is failing now
        $this->assertFalse($manager->getStatus());
    }
}