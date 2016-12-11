<?php

class IPRepositoryService {

    private $ip_repo_file = "ipRepoFile";
    private $file_handler;

    public function __construct() {
        $this->file_handler = new FileHandler();
        $this->CreateFileIfNotExists();
        $config = new ServerConfig();
        if (!$config->getIsRepoServer()) {
            $this->register($config->repo_server_ip);
        }
    }

    private function CreateFileIfNotExists() {
        Utils::e("Create File: " . $this->ip_repo_file);
        if (!file_exists($this->ip_repo_file)) {
            $this->file_handler->createFile($this->ip_repo_file);
        }
    }

    public function query() {
        Utils::e("Query registered IPs");
        return $this->getRegisteredIPs();
    }

    public function register($ip) {
        Utils::e("Register IP: " . $ip);
        if (!$this->ipInFile($ip)) {
            $this->addEntryToFile($ip);
        }
    }

    public function unregister($ip_to_del) {
        Utils::e("Unregister IP: " . $ip_to_del);
        if ($this->ipInFile($ip_to_del)) {
            $this->removeEntryFromFile($ip_to_del);
        }
    }

    public function getRegisteredIPs() {
        Utils::e("Get all entries from file: " . $this->ip_repo_file);
        $entries = $this->getFileEntries();
        $response = [];
        if (!empty($entries)) {
            foreach ($entries AS $line) {
                $entry = $this->getDecodedEntry($line);
                array_push($response, $entry[0]);
            }
        }
        return $response;
    }

    public function ipInFile($ip) {
        Utils::e("Check if IP is registered.");
        $entries = $this->getFileEntries();
        foreach ($entries AS $line) {
            $entry = $this->getDecodedEntry($line);
            if ($entry[0] === $ip) {
                Utils::e("Entry: " . $ip . " is in file: " . $this->ip_repo_file);
                return true;
            }
        }
        Utils::e("Entry: " . $ip . " is not in file: " . $this->ip_repo_file);
        return false;
    }

    public function addEntryToFile($ip) {
        Utils::e("Add entry: " . $ip . " to file: " . $this->ip_repo_file);
        $entry = $this->getEncodedEntry([$ip]);
        Utils::getLock($this->ip_repo_file);
        file_put_contents($this->ip_repo_file, $entry, FILE_APPEND);
        Utils::releaseLock($this->ip_repo_file);
    }

    public function removeEntryFromFile($ip_to_del) {
        Utils::e("Remove entry: " . $ip_to_del . " from file: " . $this->ip_repo_file);
        $entries = $this->getFileEntries();
        $this->file_handler->createFile($this->ip_repo_file);
        foreach ($entries AS $line) {
            $array = $this->getDecodedEntry($line);
            if ($array[0] != $ip_to_del) {
                $this->addEntryToFile($array[0]);
            }
        }
    }

    public function getEncodedEntry($entry) {
        return $this->file_handler->getEncodedLineFromEntry($entry);
    }

    public function getDecodedEntry($entry) {
        return $this->file_handler->getDecodedEntryFromLine($entry);
    }

    public function getFileEntries() {
        Utils::getLock($this->ip_repo_file);
        $entries = file($this->ip_repo_file);
        Utils::releaseLock($this->ip_repo_file);
        return $entries;
    }

}
