<?php

namespace Icyboy\HealthCheck;

class HealthCheck
{
    private $description = null;
    private $callable    = null;
    private $status      = null;
    private $exception   = null;

    public function __construct($description, \Closure $callable)
    {
        $this->description = $description;
        $this->callable    = $callable;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStatus()
    {
        if ($this->status === null) {
            try {
                $this->status = $this->call();
            } catch (HealthException $e) {
                $this->exception = $e;
                $this->status    = false;
            }
        }

        return $this->status;
    }

    private function call()
    {
        $c = $this->callable;

        return $c();
    }
}
