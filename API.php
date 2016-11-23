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
 * 
 * info:
 * chmod 777 folder
 *
 *  change repo_server_url 
 *  in api.php and chat.js for all servers
 *  update run configs
 * 
 */
class API {

    private $I_AM_REPO = true;
    private $function = null;
    private $ip = null;
    private $chat_room = null;
    private $chat_message = null;
    private $last_msg = 0;
    private $ip_to_del = null;
    private $ping_msg = null;

    public function __construct() {
        $this->function = $_POST["function"] ?? null;
        $this->ip = $_POST["ip"] ?? null;
        $this->chat_message = isset($_POST["chat_message"]) ? urldecode($_POST["chat_message"]) : null;
        $this->chat_room = $_POST["chat_room"] ?? null;
        isset($_POST["last_msg"]) AND $this->last_msg = intval($_POST["last_msg"]);

        $this->ip_to_del = $_POST["ip_to_del"] ?? null;
        $this->ping_msg = $_POST["ping"] ?? null;
    }

    public function start() {
        $this->startFunction();
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
                if ($this->I_AM_REPO) {
                    $ipRepo = $this->getIPRepositoryService();
                    $ipRepo->register($this->ip);
                } else {
                    $sercom = $this->startServerCom();
                    $sercom->sendIPToRepo();
                }

                break;
            case "unregister":
                if ($this->I_AM_REPO) {
                    $ipRepo = $this->getIPRepositoryService();
                    $ipRepo->unregister($this->ip_to_del);
                } else {
                    $sercom = $this->startServerCom();
                    $sercom->removeIPFromRepo();
                }
                break;
            case "query":
                $ipRepo = $this->getIPRepositoryService();
                $reg_ips = $ipRepo->query();
                return $reg_ips;
            case "setMessageServer":
                $sercom = $this->startServerCom();
                $sercom->setMessageFromServer($ip, $chat_room, $message);
                break;
            case "pingOnline":
                $sercom = $this->startServerCom();
                return $sercom->setPingOnline($this->ping_msg);
                break;
            case "pingNewRepo":
                $sercom = $this->startServerCom();
                $sercom->setPingOnline($this->ping_msg);
                break;
            case "startRepoEx":
                $this->startServerCom()->startRepoExchange();
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
        return new ServerCommunication($this->ip, $this->I_AM_REPO);
    }

}

/**
 * START OF SCRIPT 
 */
if (!empty($_POST)) {
    $api = new API();
    $api->start();
}