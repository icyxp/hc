<?php

namespace Icyboy\HealthCheck;

class Formatter
{
    public static function toJson(HealthManager $manager)
    {
        static::contentType('application/json');

        $allPassing     = $manager->getStatus();
        $temp['status'] = self::statusToStr($allPassing);

        foreach ($manager->getHealthChecks() as $hc) {

            $info = $hc->getStatus();
            if (is_array($info) && !empty($info)) {
                $temp[$hc->getDescription()] = array_merge(array("status" => Status::UP), $info);
            } elseif ($info === true) {
                $temp[$hc->getDescription()]["status"] = Status::UP;
            } elseif ($info) {
                $temp[$hc->getDescription()]["status"] = Status::UP;
                $temp[$hc->getDescription()]["message"] = $info;
            } else {
                $temp[$hc->getDescription()]["status"] = Status::DOWN;
                if ($hc->getException() instanceof HealthException) {
                    $temp[$hc->getDescription()]["message"] = $hc->getException()->getMessage();
                }
            }
        }

        static::responseIsPassing($allPassing);

        return json_encode($temp);
    }


    public static function toArr(HealthManager $manager)
    {
        $allPassing     = $manager->getStatus();
        $temp['status'] = self::statusToStr($allPassing);

        foreach ($manager->getHealthChecks() as $hc) {

            $info = $hc->getStatus();
            if (is_array($info) && !empty($info)) {
                $temp[$hc->getDescription()] = array_merge(array("status" => Status::UP), $info);
            } elseif ($info === true) {
                $temp[$hc->getDescription()]["status"] = Status::UP;
            } elseif ($info) {
                $temp[$hc->getDescription()]["status"] = Status::UP;
                $temp[$hc->getDescription()]["message"] = $info;
            } else {
                $temp[$hc->getDescription()]["status"] = Status::DOWN;
                if ($hc->getException() instanceof HealthException) {
                    $temp[$hc->getDescription()]["message"] = $hc->getException()->getMessage();
                }
            }
        }

        static::responseIsPassing($allPassing);

        return $temp;
    }

    public static function statusToStr($status)
    {
        if ($status === true) {
            return Status::UP;
        } elseif ($status === false) {
            return Status::DOWN;
        } else {
            return $status;
        }
    }

    public static function autoexec(HealthManager $manager)
    {
        echo static::toJson($manager);
    }

    public static function autoArr(HealthManager $manager)
    {
        return static::toArr($manager);
    }

    public static function acceptJson()
    {
        // Guard if we're not running in a web server
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false;
    }

    private static function contentType($type)
    {
        if (php_sapi_name() !== 'cli') {
            header('Cache-Control: no-cache');
            header('Content-Type: ' . $type);
        }
    }

    private static function responseIsPassing($isPassing)
    {
        if (php_sapi_name() !== 'cli') {
            if ($isPassing) {
                http_response_code(200);
            } else {
                http_response_code(503);
            }
        }
    }
}
