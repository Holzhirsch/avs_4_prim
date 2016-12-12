<?php

/**
 * Description of processNumber
 *
 * @author student
 */
class processNumber {

    private $number = null;
    private $ip_repo_file = "ipRepoFile";
    private $repo_service = null;

    public function __construct($number) {
        $this->number = $number;
        $this->repo_service = new IPRepositoryService();
        if($this->number === 1) {
            // really, is one prime or not...
            //TODO
            
        }
    }

    public function process() {
        $server_ips = $this->getServer();
        $num_server = $this->getServerNumber($server_ips);
        $range = $this->getNumbersPerServer($num_server);
        $results = $this->SendToServers($server_ips, $range);
        $this->processResult($results);
    }

    public function getServerNumber($server_ips) {
        return count($server_ips);
    }

    public function getNumbersPerServer($num_server) {
        
        $rest = 0;
        $range = [];

        if ($this->number < $num_server) {
            $range = [[1, $this->number]];
            print_r($range);
            return $range;
        }

        $rest = $this->number % $num_server;
        $num_without_rest = $this->number - $rest;
        $per_server = intval($num_without_rest / $num_server);
        
        for ($i = 0; $i < $num_without_rest;) {
            $start = $i + 1;
            $i += $per_server;
            if ($i == $num_without_rest) {
                $end = $i + $rest;
            } else {
                $end = $i;
            }
            $server_range = [$start, $end];
            array_push($range, $server_range);
        }
        return $range;
    }

    public function SendToServers($server_ips, $range) {
        $sercom = new ServerCommunication();
        $result_array = [];
        for ($i=0; $i < count($server_ips); $i++) {
            $entry = $this->repo_service->getDecodedEntry($server_ips[$i]);
            $url = "http://" . $entry[0] . "/AVS_4/API.php";
            $data = [
                "function" => "processPrime",
                "data" => $range[$i]
            ];
            $response = $sercom->connect($data, true, $url);
            echo "response: " . $response;
            array_push($result_array, $response);
        }
        return $result_array;
    }

    public function processResult($result_array) {
        print_r($result_array);
    }

    public function getServer() {
        $entries = $this->repo_service->getFileEntries();
        return $entries;
    }

}
