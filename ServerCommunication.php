<?php

class ServerCommunication {

    public function __construct() {
        
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
