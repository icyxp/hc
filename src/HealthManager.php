<?php

namespace Icyboy\HealthCheck;

class HealthManager
{
    private $healthChecks = array();

    /**
     * Add  healthcheck. If this healthcheck fails HealthManger will respond with a 503.
     */
    public function addCheck($description, \Closure $healthCheck)
    {
        $this->healthChecks[] = new HealthCheck($description, $healthCheck);
    }

    /**
     * Add an instance of healthcheck, useful if you want to subclass
     * the healthcheck class and add custom behavior.
     *
     * @param HealthCheck $healthCheck
     */
    public function addHealthCheck(HealthCheck $healthCheck)
    {
        $this->healthChecks[] = $healthCheck;
    }

    /**
     * Evaluate all healthchecks and return a boolean based on the aggregate.
     *
     * @return bool true if all tests pass, false otherwise
     */
    public function getStatus()
    {
        $status = true;

        foreach ($this->healthChecks as $healthCheck) {
            // Shortcut the rest if any check fails
            if ($status) {
                $status = $healthCheck->getStatus();
            }
        }

        return $status;
    }

    /**
     * @return Array List of all healthchecks currently registered
     */
    public function getHealthChecks()
    {
        return $this->healthChecks;
    }

    /**
     * Evaluate all healthchecks and output a summary, using Formatter->autoexec()
     */
    public function check()
    {
        Formatter::autoexec($this);
    }
}
