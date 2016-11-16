<?php

include "Utils.php";
include "FileHandler.php";
include "ChatService.php";
include "IPRepositoryService.php";
include "ServerCommunication.php";

/**
 * @author era
 * 
 * Description of API:
 * Class to login and send needed data to the functions.
 * 
 */
class API {

    private $function = null;
    private $ip = null;
    private $chat_room = null;
    private $chat_message = null;
    private $last_msg = 0;
    private $ip_to_del = null;

    public function __construct() {
        $this->function = $_POST["function"] ?? null;
        $this->ip = $_POST["ip"] ?? null;
        $this->chat_message = isset($_POST["chat_message"]) ? urldecode($_POST["chat_message"]) : null;
        $this->chat_room = $_POST["chat_room"] ?? null;
        isset($_POST["last_msg"]) AND $this->last_msg = intval($_POST["last_msg"]);

        $this->ip_to_del = $_POST["ip_to_del"] ?? null;
    }

    public function start() {
        $this->startFunction();
        $this->startServerCom()->startRepoExchange();
    }

    /**
     * Sends the needed data to the requested functions.
     */
    private function startFunction() {
        Utils::e("Start function: " . $this->function);
        switch ($this->function) {
            case "setMessage":
                $chat = $this->getChatService();
                $chat->setMessage($this->chat_message);
                break;
            case "getUpdate":
                $chat = $this->getChatService();
                $response = $chat->getUpdate($this->last_msg);
                $this->sendJsonResponse($response);
                break;
            case "register":
                $ipRepo = $this->getIPRepositoryService();
                $ipRepo->register($this->ip);
                break;
            case "unregister":
                $ipRepo = $this->getIPRepositoryService();
                $ipRepo->unregister($this->ip_to_del);
                break;
            case "query":
                //
                $sercom = $this->startServerCom();
                //
                $ipRepo = $this->getIPRepositoryService();
                $reg_ips = $ipRepo->query();
                return $reg_ips;
            case "setMessageServer":
                $sercom = $this->startServerCom();
                $sercom->setMessageFromServer($ip, $chat_room, $message);
                break;
            default :
                throw new Exception("No such function: '" . $this->function . "' exits!");
        }
    }

    private function getChatService() {
        return new ChatService($this->chat_room, $this->ip);
    }

    private function getIPRepositoryService() {
        return new IPRepositoryService();
    }

    private function sendJsonResponse($array) {
        echo json_encode($array);
    }

    public function startServerCom() {
        return new ServerCommunication();
    }

}

/**
 * START OF SCRIPT 
 */
if (!empty($_POST)) {
    $api = new API();
    $api->start();
}