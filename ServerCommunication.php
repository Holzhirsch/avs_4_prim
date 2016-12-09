<?php

include 'HTTP/Request2.php';

class ServerCommunication {

    private $config = null;
    private $I_AM_REPO = false;
    private $repo_Server_URL = null;
    private $this_server_ip = null;
    private $ip_repo_file = "ipRepoFile";
    
    private $repo_service = null;

    public function __construct() {
        Utils::e("Init class: " . __CLASS__);
        
        $this->repo_service = new IPRepositoryService();
        
        $this->config = New ServerConfig();
        $this->this_server_ip = $this->config->getThisServerIp();
        $this->I_AM_REPO = $this->config->getIsRepoServer();
        $this->repo_Server_URL = $this->config->getRepoServerUrl();
    }

    public function startRepoExchange() {
        if (!$this->I_AM_REPO) {
            $this->sendIPToRepo();
            $this->getIPsFromRepo();
            $this->CheckIPsInRepo();
            $this->NotifyServersAboutNewRepo();
        }
    }

    public function sendIPToRepo() {
        $data = [
            'function' => 'register',
            'ip' => $this->this_server_ip
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

    public function createOwnRepo($repo_ips) {
        foreach ($repo_ips as $entry) {
            Utils::e($entry . " ::: " . $this->this_server_ip);
            if ($entry !== $this->this_server_ip) {
                $this->repo_service->register($entry);
            }
        }
    }

    public function CheckIPsInRepo() {
        $msg = [
                        "function" => "pingOnline"
                    ];
        
        $entries = file($this->ip_repo_file);
        if (empty($entries)) {
            Utils::e("Entries are empty.");
        } else {
            foreach ($entries AS $line) {
                if (!empty($line)) {
//                    $line = rtrim($line);
//                    $entry = unserialize($line);
                    $entry = $this->repo_service->getDecodedEntry($line);
                    $url = "http://" . $entry[0] . "/AVS_3/API.php";

                    $response = $this->connect($msg, true, $url);
                    if ($response === "online") {
                        Utils::e($entry[0] . " is online");
                    } else {
                        Utils::e($entry[0] . " is not online");
                        $this->repo_service->removeEntryFromFile($entry[0]);
                        $this->removeIPFromRepo($entry[0]);
                    }
                } else {
                    Utils::e("Line is empty: " . $line);
                }
            }
        }
    }

    public function removeIPFromRepo($ip) {
        $data = [
            'function' => 'unregister',
            'ip_to_del' => $ip
        ];
        $this->connect($data, false, $this->repo_Server_URL);
    }

    public function NotifyServersAboutNewRepo() {
        $entries = file($this->ip_repo_file);
        foreach ($entries AS $line) {
//            $line = rtrim($line);
//            $entry = unserialize($line);
            $entry = $this->repo_service->getDecodedEntry($line);
            $url = "http://" . $entry[0] . "/AVS_3/API.php";

            $msg = [
                "function" => "pingNewRepo"
            ];
            $this->connect($msg, false, $url);
        }
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
        $entries = file($this->ip_repo_file);
        foreach ($entries AS $line) {
//            $line = rtrim($line);
//            $entry = unserialize($line);
            $entry = $this->repo_service->getDecodedEntry($line);
            $url = "http://" . $entry[0] . "/AVS_3/API.php";

            $msg = [
                "function" => "setMessageFromServer",
                "chat_message" => $message,
                "ip" => $ip,
                "chat_room" => $chat_room
            ];
            $this->connect($msg, false, $url);
        }
    }

}
