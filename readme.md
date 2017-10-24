# HealthCheck

Allows you to easily write healthchecks for your application and display a simple, aggregated report so you can quickly diagnose whether and why your app is having trouble (or whether you can blame someone else). You can also monitor your healthchecks with [nagios](http://www.nagios.org/), [zabbix](http://www.zabbix.com/), etc.

[![Build Status](https://travis-ci.org/icyxp/healthCheck.svg?branch=master)](https://travis-ci.org/icyxp/healthCheck)

#### Wait, what's a healthcheck?

Healthchecks are a great way to test system health and connectivity to other services. For example, you can verify connectivity to memcache or mysql, that your app can read / write to certain files, or that your API key for a third-party service is still working.

## Installation

You can install this into your project using [composer](http://getcomposer.org/doc/00-intro.md#installation-nix). Create a `composer.json` file in the root of your project and add the following:

```json
{
    "require": {
        "php": ">=5.4.0",
        "icyboy/health-check": "~1.0"
    }
}
```

Run `composer install`, include `vendor/autoload.php`, and you're off to the races!

## Example Usage

#### Checks

```php
use Icyboy\HealthCheck\HealthManager;
use Icyboy\HealthCheck\HealthException;

$hc = new HealthManager();

$hc->addCheck('info', function(){
    return "something";
});

$hc->addCheck('pass', function() {
    return false;
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
```