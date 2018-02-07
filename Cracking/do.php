<?php
include 'cracking.php';

/**
 * usage
 * first parameter (portScan | ssh | crackingPort)
 * second parameter ('host=172.20.13.208' | 'host=172.20.13.208&port=22&user=root&password=root')
 *
 * for example
 * php do.php ssh 'host=172.20.13.208&port=22&user=root&password=root'
 * php do.php portScan 'host=113.10.195.1-255'
 * php do.php crackingPort
 */

$controller = $argv[1];
$parameter = isset($argv[2]) ? $argv[2] : '';
$parameters = [];

if ($parameter) {
    parse_str($parameter, $parameters);
}

$Cracking = new Cracking();
if (method_exists($Cracking, $controller)) {
    Cracking::$controller($parameters);
} else {
    die('This parameter is not supported');
}

