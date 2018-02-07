<?php

/**
 * Ssh login check
 *
 */

namespace Cracking;

use ReflectionClass;

class Ssh
{
    private $host;
    private $port;
    private $methods = null;
    private $connection;
    private $user;
    private $password;
    private $result;
    private $reflection_class;


    public function __construct($host = '', $port = '', $methods = [])
    {
        if (!function_exists("ssh2_connect")) {
            exit('no ssh2_connect' . PHP_EOL);

        }

        $this->reflection_class = new ReflectionClass($this);

        $this->host = !empty($host) ? $host : '';
        $this->port = !empty($port) ? $port : '';
        if (!empty($methods)) {
            $this->methods = $methods;
        }

        $this->info('connection');
        $this->connection = @ssh2_connect($this->host, $this->port, $this->methods);

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
                if (@ssh2_auth_password($this->connection, $this->user, $this->password)) {
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
            $out = ['msg' => $this->reflection_class->getShortName() . ' connection:', 'host' => $this->host, 'port' => $this->port];
        } elseif ($type == 'auth') {
            $out = ['msg' => $this->reflection_class->getShortName() . ' auth:', 'user' => $this->user, 'password' => $this->password];
        }

        echo implode(' ', array_values($out)), PHP_EOL;
        unset($out);
    }

    private function output()
    {
        $out = ['msg' => $this->reflection_class->getShortName(), 'host' => $this->host, 'port' => $this->port, 'user' => $this->user, 'password' => $this->password, 'result' => $this->result];
        $this->out($out);
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