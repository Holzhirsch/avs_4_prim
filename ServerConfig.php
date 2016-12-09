<?php

/**
 * Description of Config
 *
 * @author era
 */
class ServerConfig {

    private $this_server_ip = null;
    private $repo_server_ip = "192.168.0.11";
    private $this_server_is_repo_server = true;

    public function __construct() {
        Utils::e("Init class: " . __CLASS__);
        
        $this->this_server_ip = $this->getIp();
    }

    public function getThisServerIp() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);
        
        Utils::e("return: ", $this->this_server_ip);
        return $this->this_server_ip;
    }

    public function getRepoServerUrl() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);
        
        Utils::e("return: ", "http://" . $this->repo_server_ip . "/AVS_3/API.php");
        return "http://" . $this->repo_server_ip . "/AVS_3/API.php";
    }

    public function getIsRepoServer() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);
        
        Utils::e("return: ", $this->this_server_is_repo_server);
        return $this->this_server_is_repo_server;
    }

    private function getIp() {
        Utils::e("Start method: " . __METHOD__ . " in class: " . __CLASS__);
        
        $ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        
        Utils::e("return: ", $$ip);
        return $ip;
    }

}
