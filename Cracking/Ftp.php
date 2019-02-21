<?php

/**
 * Ftp login check
 *
 */

namespace Cracking;

use ReflectionClass;

class Ftp
{
    private $host;
    private $port;
    private $timeout;
    private $connection;
    private $user;
    private $password;
    private $result = 'init';
    private $reflection_class;


    public function __construct($host = '', $port = '', $timeout = 1)
    {
        $this->reflection_class = new ReflectionClass($this);
        $this->host = !empty($host) ? $host : '';
        $this->port = !empty($port) ? $port : '';
        $this->timeout = $timeout;

        $this->info('connection');
        $this->connection = ftp_connect($this->host, $this->port, $this->timeout);

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
        if ($this->connection) {
            if (!empty($user)) {
                $this->set_user($user);
            }

            if (!empty($password)) {
                $this->set_password($password);
            }

            $this->info('auth');
            if ($this->connection && !empty($this->user) && !empty($this->password)) {
                if (@ftp_login($this->connection, $this->user, $this->password)) {
                    $this->result = 'success';
                }else{
                    $this->result = 'failed';

                }
                $this->output();

            }

        }

    }

    private function info($type)
    {
        $out = [];
        if ($type == 'connection') {
            $out = ['time' => date('Y-m-d H:i:s'), 'msg' => $this->reflection_class->getShortName() . ' connection:', 'host' => $this->host, 'port' => $this->port];
        } elseif ($type == 'auth') {
            $out = ['time' => date('Y-m-d H:i:s'), 'msg' => $this->reflection_class->getShortName() . ' auth:', 'user' => $this->user, 'password' => $this->password];
        }

        echo implode(' ', array_values($out)), PHP_EOL;
        unset($out);
    }

    private function output()
    {
        $out = ['time' => date('Y-m-d H:i:s'), 'msg' => $this->reflection_class->getShortName(), 'host' => $this->host, 'port' => $this->port, 'user' => $this->user, 'password' => $this->password, 'result' => $this->result];
        $this->out($out);
        unset($out);

        ftp_close($this->connection);
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