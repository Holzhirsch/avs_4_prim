<?php

include "Utils.php";
include "ServerConfig.php";
include "FileHandler.php";
include "IPRepositoryService.php";
include "ServerCommunication.php";
include "ProcessNumber.php";

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

    private $function = null;
    private $client_ip = null;
    private $ip_to_del = null;
    private $config = null;
    private $ip_Repo = null;
    private $number = null;

    public function __construct() {
        $this->config = New ServerConfig();
        
        $this->client_ip = $_POST["ip"] ?? $this->config->getThisServerIp();
        $this->function = $_GET["function"] ?? ($_POST["function"] ?? null);
        $this->ip_to_del = $_POST["ip_to_del"] ?? null;
        $this->ip_Repo = $this->getIPRepositoryService();
        $this->number = $_POST["number"] ?? null;
        $this->data = $_POST["data"] ?? null;
    }

    /**
     * Sends the needed data to the requested functions.
     */
    public function startFunction() {
        Utils::e("start Post_function:" . $this->function);
        switch ($this->function) {
            case "processNumber":
                Utils::e(" process Number: " . $this->number);
                $process = new processNumber($this->number);
                $process->process();
                break;
            case "register":
                $this->ip_Repo->register($this->client_ip);
                break;
            case "unregister":
                $this->ip_Repo->unregister($this->ip_to_del);
                break;
            case "query":
                $reg_ips = $this->ip_Repo->query();
                $this->sendJsonResponse(["response" => $reg_ips]);
                break;
            case "pingOnline":
                $response = "online";
                $this->sendJsonResponse(["response" => $response]);
                break;
            case "pingNewRepo":
                $sercom = $this->startServerCom();
                $sercom->getIPsFromRepo();
                break;
            case "startRepoEx":
                $sercom = $this->startServerCom();
                $sercom->startRepoExchange();
                break;
            case "processPrime":
                $process = new processNumber($this->number);
                $process->processNumbers($this->data);
                $response = "online" . implode(",", $this->data);
                $this->sendJsonResponse(["response" => $response]);
                break;
            default :
                Utils::e("No such function: '" . $this->function . "' exists!");
        }
    }

    private function getIPRepositoryService() {
        return new IPRepositoryService();
    }

    private function sendJsonResponse($array) {
        echo json_encode($array);
    }

    private function startServerCom() {
        return new ServerCommunication();
    }

}

/**
 * START OF SCRIPT 
 */
if (!empty($_POST) || !empty($_GET)) {
    $api = new API();
    $api->startFunction();
}