<?php

include 'HTTP/Request2.php';

class ServerCommunication {

    private $I_AM_REPO = false;
    private $repo_Server_URL = "http://192.168.0.11/AVS_3/API.php";
    private $ip;
    private $ip_repo_file = "ipRepoFile";

    public function __construct($ip, $is_repo_server) {
        $this->ip = $ip;
        $this->I_AM_REPO = $is_repo_server;
    }

    public function startRepoExchange() {
        /**
         * get ips from reposerver
         * safe ips
         * ping ips
         * get answer ("online") | no answer
         *      send delete to reposerver for no answer
         * 
         * on getting pinged - send "online" and get ips from repo, but dont ping others again
         */
        if ($this->I_AM_REPO) {
            
        } else {
            $this->sendIPToRepo();
            $this->getIPsFromRepo();
            $this->CheckIPsInRepo();
            $this->NotifyServersAboutNewRepo();
        }
    }

    public function NotifyServersAboutNewRepo() {
        $entries = file($this->ip_repo_file);
        foreach ($entries AS $line) {
            $line = rtrim($line);
            $entry = unserialize($line);
            $url = "http://" . $entry[0] . "/AVS_3/API.php";

            $msg = [
                "ping_msg" => "newRepo",
                "function" => "pingNewRepo"
            ];
            $this->connect($msg, true, $url);
        }
    }

    public function sendIPToRepo() {
        $data = [
            'function' => 'register',
            'ip' => $this->ip
        ];
        $this->connect($data, false, $this->repo_Server_URL);
    }

    public function removeIPFromRepo() {
        $data = [
            'function' => 'unregister',
            'ip_to_del' => $this->ip
        ];
        $this->connect($data, false, $this->repo_Server_URL);
    }

    public function getIPsFromRepo() {
        $data = [
            'function' => 'query'
        ];
        $repo_ips = $this->connect($data, true, $this->repo_Server_URL);
        $this->createOwnRepo($repo_ips);
    }

    public function connect($data, $b_get_response, $url) {
        $URL = $url;

        $request = new HTTP_Request2($URL);
        $request->setMethod(HTTP_Request2::METHOD_POST)
                ->addPostParameter($data);

        try {
            $response = $request->send();
            if (200 == $response->getStatus()) {
                if ($b_get_response) {
                    $temp = $response->getBody();
                    $array = json_decode($temp)->response;
                    return $array;
                }
            } else {
                echo 'Unexpected HTTP status: ' . $response->getStatus();
            }
        } catch (HTTP_Request2_Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function sendMessageToServers($ip, $chat_room, $message) {
        /**
         * Implement here
         * 
         * get ips from ipFile and send message to servers in list with ip of client
         * 
         */
    }

    public function setMessageFromServer($ip, $chat_room, $message) {
        $chat = new ChatService($chat_room, $ip);
        $chat->setMessage($message);
    }

    public function createOwnRepo($repo_ips) {
        $repo = new IPRepositoryService();
        foreach ($repo_ips as $ip) {
            if ($entry !== $this->ip) {
                $repo->register($entry);
            }
        }
    }

    public function CheckIPsInRepo() {

        $entries = file($this->ip_repo_file);
        foreach ($entries AS $line) {
            $line = rtrim($line);
            $entry = unserialize($line);
            $url = "http://" . $entry[0] . "/AVS_3/API.php";

            $msg = [
                "ping_msg" => "online",
                "function" => "pingOnline"
            ];
            $response = $this->connect($msg, true, $url);
            if ($response === "online") {
                Utils::e($entry[0] . " is online");
            } else {
                $repo = new IPRepositoryService();
                $repo->removeEntryFromFile($entry[0]);
                $this->removeIPFromRepo($entry[0]);
            }
        }
    }

    public function setPing($ping_msg) {
        if ($ping_msg === "online") {
            return "online";
        }
        if ($ping_msg === "newRepo") {
            $this->getIPsFromRepo();
        }
    }

}
