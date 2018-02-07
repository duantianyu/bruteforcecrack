<?php


include_once 'config.php';
include_once 'autoload.php';

use Cracking\CrackingPort;
use Cracking\PortScan;
use Cracking\Ssh;


class Cracking
{
    public function __construct()
    {
        echo date('Y-m-d H:i:s'), ' start', PHP_EOL;

    }


    /**/
    public static function portScan($parameters)
    {
        if (empty($parameters['host'])) {
            echo 'no host' . PHP_EOL;
        }

        $portScan = new PortScan($parameters['host']);
        $portScan->execute();
    }

    public static function ssh($parameters)
    {
        if (empty($parameters['host']) || empty($parameters['port']) || empty($parameters['user']) || empty($parameters['password'])) {
            echo 'no host or port' . PHP_EOL;
        }

        $ssh = new Ssh($parameters['host'], $parameters['port']);
        $ssh->auth($parameters['user'], $parameters['password']);
    }

    public static function crackingPort($parameters)
    {
        $CrackingPort = new CrackingPort();
        $CrackingPort->execute();
    }


    public function __destruct()
    {
        echo PHP_EOL, date('Y-m-d H:i:s'), ' end', PHP_EOL;
    }
}
/*
if ($argv[1] == 'PortScan') {
    $portScan = new PortScan('172.16.1.200-201');
    $portScan->execute();
}

if ($argv[1] == 'Ssh') {
    $ssh = new Ssh('192.168.1.10', '22');
    $ssh->auth("root", "root");
}

if ($argv[1] == 'CrackingPort') {
    $CrackingPort = new CrackingPort();
    $CrackingPort->execute();
}
*/

