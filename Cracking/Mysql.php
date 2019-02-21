<?php

/**
 * Mysql login check
 *
 */

namespace Cracking;

use ReflectionClass;

class Mysql
{
    private $host;
    private $database = '';
    private $port;
    private $connection;
    private $user;
    private $password;
    private $result;


    public function __construct($host = '', $port = '')
    {
        $this->host = !empty($host) ? $host : '';
        $this->port = !empty($port) ? $port : '';

    }

    public function set_user($user)
    {
        $this->user = $user;
    }

    public function set_password($password)
    {
        $this->password = $password;
    }


    public function auth($user = '', $password = '')
    {
        if (!empty($user)) {
            $this->set_user($user);
        }

        if (!empty($password)) {
            $this->set_password($password);
        }

        $this->info('connection');
        if (!empty($this->host) && !empty($this->user) && !empty($this->password)) {
            $this->connection = @mysqli_connect($this->host, $this->user, $this->password, $this->database, $this->port);
        }

        $this->info('auth');
        if (mysqli_connect_error()) {
            $this->error();
        } else {
            $this->success();

        }

    }

    private function info($type)
    {
        $ReflectionClass = new ReflectionClass($this);
        $out = [];
        if ($type == 'connection') {
            $out = ['time' => date('Y-m-d H:i:s'), 'msg' => $ReflectionClass->getShortName() . ' connection:', 'host' => $this->host, 'port' => $this->port];
        } elseif ($type == 'auth') {
            $out = ['time' => date('Y-m-d H:i:s'), 'msg' => $ReflectionClass->getShortName() . ' auth:', 'user' => $this->user, 'password' => $this->password];
        }

        echo implode(' ', array_values($out)), PHP_EOL;
        unset($out);
    }

    private function success()
    {
        $this->result = 'success';
        $out = ['time' => date('Y-m-d H:i:s'), 'host' => $this->host, 'port' => $this->port, 'user' => $this->user, 'password' => $this->password, 'result' => $this->result];
        $this->out($out);
        unset($out);

        mysqli_close($this->connection);
    }

    private function error()
    {
        $this->result = 'failed';
        $out = [
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'password' => $this->password,
            'errno' => mysqli_connect_errno(),
            'error' => mysqli_connect_error(),
        ];
        echo implode(' ', array_values($out)), PHP_EOL;
        unset($out);
    }

    private function out($out)
    {
        echo implode(' ', array_values($out)), PHP_EOL;
        file_put_contents(HOST_PASSWORD_FILE, json_encode($out) . PHP_EOL, FILE_APPEND);
    }


    public function __destruct()
    {
        unset($this->connection, $this->host, $this->port, $this->user, $this->password);
    }

}