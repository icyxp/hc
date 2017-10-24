<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Icyboy\HealthCheck\HealthManager;
use Icyboy\HealthCheck\HealthException;

$hc = new HealthManager();

$hc->addCheck('info', function(){
    return "response extra message";
});

$hc->addCheck('pass', function() {
    return true;
});

$hc->addCheck('fail', function() {
    return false;
});

$config["version"] = 123;
$hc->addCheck('xxx', function() use ($config) {
    if ($config["version"] == "123") {
        return $config;
    } else {
        throw new HealthException("something was wrong");
    }
});

$hc->check();