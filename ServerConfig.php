<?php

/**
 * Description of Config
 *
 * @author era
 */
class ServerConfig {

    private $this_server_ip = "192.168.0.11";
    private $repo_server_ip = "192.168.0.11";
    private $this_server_is_repo_server = true;

    public function __construct() {}

    public function getThisServerIp() {
        return $this->this_server_ip;
    }

    public function getRepoServerUrl() {
        return "http://" . $this->repo_server_ip . "/AVS_3/API.php";
    }

    public function getIsRepoServer() {
        return $this->this_server_is_repo_server;
    }

}