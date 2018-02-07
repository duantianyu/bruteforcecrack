<?php
/**
 * Port Scan
 *
 */

namespace Cracking;


class PortScan
{
    private $ports = ['21', '22', '3306'];
    private $port;
    private $hosts_file = HOSTS_FILE;
    private $hosts_port_file = HOSTS_PORT_FILE;
    private $hostname = '';
    private $hostnames = [];
    private $line_break = PHP_EOL;
    private $timeout = 1;
    private $delimiter = ':';


    public function __construct($hostname = '', $ports = [])
    {
        if (!empty($hostname)) {
            $this->hostname = $hostname;
        }

        if (!empty($ports)) {
            $this->ports = $ports;
        }

        if (strpos($this->hostname, '-')) {
            $this->get_hosts();
        } elseif ($this->hostname == '' && count($this->hostnames) < 1) {
            $this->hostnames = trim(file_get_contents($this->hosts_file));
            $this->hostnames = explode($this->line_break, $this->hostnames);

        }

    }

    public function execute()
    {

        if (count($this->hostnames) > 0) {
            echo 'hosts num:', count($this->hostnames), $this->line_break;

            foreach ($this->hostnames as $this->hostname) {
                $this->hostname = trim($this->hostname);
                $this->each_hosts();

            }
        } elseif ($this->hostname) {
            echo 'hosts num:', 1, $this->line_break;

            $this->each_hosts();

        }


    }

    //get host from $this->hostname
    protected function get_hosts()
    {
        $arr_host = explode('.', $this->hostname);
        $arr = explode('-', end($arr_host));
        $pre_host = isset($arr_host[0]) && isset($arr_host[1]) && isset($arr_host[2]) ? $arr_host[0] . '.' . $arr_host[1] . '.' . $arr_host[2] : '';

        if (isset($arr[0]) && isset($arr[1])) {
            for ($i = $arr[0]; $i <= $arr[1]; $i++) {
                $this->hostnames[] = $pre_host . '.' . $i;
            }
        }


    }

    //Loop through the data records in the arrays
    protected function each_hosts()
    {
        foreach ($this->ports as $this->port) {
            $this->port_scan();
        }
    }

    //Port Scan
    protected function port_scan()
    {
        $fp = @fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);

        if ($errno == 0) {
            $out = $this->hostname . $this->delimiter . $this->port . $this->line_break;
            file_put_contents($this->hosts_port_file, $out, FILE_APPEND);
            fclose($fp);

        }
    }

    public function __destruct()
    {
        unset($this->ports, $this->hostname, $this->hostnames);
    }
}