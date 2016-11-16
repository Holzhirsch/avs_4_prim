<?php

include 'HTTP/Request2.php';

class ServerCommunication {

    private $repo_Server_URL = "http://172.22.0.1/AVS_3/avs_3_simpleServerChat/API.php";

    public function __construct() {
        $this->connect();
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
    }

    public function connect() {
        $URL = $this->repo_Server_URL;

        $request = new HTTP_Request2($URL);
        $request->setMethod(HTTP_Request2::METHOD_POST)
        ->addPostParameter([
            'function' => 'register',
            'ip' => '192.168.2.1'
        ]);

        try {
            $response = $request->send();
            if (200 == $response->getStatus()) {
                echo $response->getBody();
            } else {
                echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase();
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

}
