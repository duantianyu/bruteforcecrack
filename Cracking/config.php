<?php

define('FILE_PATH', dirname(dirname(__FILE__)));

const HOSTS_FILE = FILE_PATH . '/hosts.txt';
const HOSTS_PORT_FILE = FILE_PATH . '/host_ports.txt'; //the open port
const USE_RANDOM_PASSWORD = true; //use random password
const USER_PASSWORD_FILE = FILE_PATH . '/user_password.txt'; //password book
const HOST_PASSWORD_FILE = FILE_PATH . '/result_host_password.txt'; //the success password
const  PORT_CLASS = [
    21 => 'Ftp',
    22 => 'Ssh',
    3306 => 'Mysql',
];
