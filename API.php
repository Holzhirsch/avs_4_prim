<?php

include "Utils.php";
include "ServerConfig.php";
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
 *  change repo_server_url and ip in Utils.php and chat.js for all servers
 *  update run configs
 * 
 */
class API {

    private $i_am_repo_server = false;
    private $function = null;
    private $client_ip = null;
    private $this_server_ip = null;
    private $chat_room = null;
    private $chat_message = null;
    private $last_msg = 0;
    private $ip_to_del = null;
    private $config = null;
    private $ip_Repo = null;

    public function __construct() {
        Utils::e("Init class: " . __CLASS__);

        $this->config = New ServerConfig();
        $this->i_am_repo_server = $this->config->getIsRepoServer();
        $this->this_server_ip = $this->config->getThisServerIp();

        $this->client_ip = $_POST["ip"] ?? $this->config->getThisServerIp();
        $this->function = $_POST["function"] ?? null;
        $this->chat_message = isset($_POST["chat_message"]) ? urldecode($_POST["chat_message"]) : null;
        $this->chat_room = $_POST["chat_room"] ?? null;
        isset($_POST["last_msg"]) AND $this->last_msg = intval($_POST["last_msg"]);
        $this->ip_to_del = $_POST["ip_to_del"] ?? null;

        $this->ip_Repo = $this->getIPRepositoryService();
    }

    /**
     * Sends the needed data to the requested functions.
     */
    public function startFunction() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);

        Utils::e("start Post_function:" . $this->function);
        switch ($this->function) {
            case "setMessage":
                $chat = $this->getChatService();
                $chat->setMessage($this->chat_message);
                $sercom = $this->startServerCom();
                $sercom->sendMessageToServers($this->client_ip, $this->chat_room, $this->chat_message);
                break;
            case "setMessageFromServer":
                $chat = $this->getChatService();
                $chat->setMessage($this->chat_message);
                break;
            case "getUpdate":
                $chat = $this->getChatService();
                $response = $chat->getUpdate($this->last_msg);
                $this->sendJsonResponse($response);
                break;
            case "register":
                if ($this->i_am_repo_server) {
                    $this->ip_Repo->register($this->this_server_ip);
                } else {
                    $sercom = $this->startServerCom();
                    $sercom->sendIPToRepo();
                }
                break;
            case "unregister":
                $this->ip_Repo->unregister($this->ip_to_del);
                break;
            case "query":
                $reg_ips = $this->ip_Repo->query();
                $this->sendJsonResponse(["response" => $reg_ips]);
                break;
            case "pingOnline":
                $this->sendJsonResponse(["response" => ["online"]]);
                break;
            case "pingNewRepo":
                $sercom = $this->startServerCom();
                $sercom->getIPsFromRepo();
                break;
            case "startRepoEx":
                $sercom = $this->startServerCom();
                $sercom->startRepoExchange();
                break;
            default :
                Utils::e("No such function: '" . $this->function . "' exists!");
        }
    }

    private function getChatService() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);

        return new ChatService($this->chat_room, $this->client_ip);
    }

    private function getIPRepositoryService() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);

        return new IPRepositoryService();
    }

    private function sendJsonResponse($array) {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);

        echo json_encode($array);
    }

    private function startServerCom() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);

        return new ServerCommunication();
    }

}

/**
 * START OF SCRIPT 
 */
if (!empty($_POST)) {
    $api = new API();
    $api->startFunction();
}