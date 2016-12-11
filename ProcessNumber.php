<?php

/**
 * Description of processNumber
 *
 * @author student
 */
class processNumber {

    private $number = null;
    private $ip_repo_file = "ipRepoFile";

    public function __construct($number) {
        $this->number = $number;
    }

    public function process() {
        $server_ips = $this->getServer();
        $num_server = $this->getServerNumber($server_ips);
        $this->getNumbersPerServer($num_server);
//        $this->SendToServers();
//        $this->processResult();
    }

    public function getServerNumber($server_ips) {
        return count($server_ips);
    }

    public function getNumbersPerServer($num_server) {
        if ($this->number < $num_server) {
            $range = [[1, $this->number]];
            print_r($range);
            return $range;
        }
        $rest = $this->number % $num_server;
        $per_server = intval(($this->number - $rest) / $num_server);
        $range = [];
        for ($i = 1; $i < $this->number;) {
            $start = $i;
            if (($rest != 0) && ($i + 2 * $per_server < $this->number)) {
                $i += $per_server;
                $end = $i - 1;
            } else {
                $i += $per_server + $rest;
                $end = $i;
            }
            $server_range = [$start, $end];
            array_push($range, $server_range);
        }
        print_r($range);
        Utils::e("Server: " . $num_server . ", per server: " . $per_server . ", rest: " . $rest);
        return $range;
    }

    public function SendToServers() {
        
    }

    public function processResult() {
        
    }

    public function getServer() {
        Utils::getLock($this->ip_repo_file);
        $entries = file($this->ip_repo_file);
        Utils::releaseLock($this->ip_repo_file);
        return $entries;
    }

}
