<?php

class IPRepositoryService {

    private $ip_repo_file = "ipRepoFile";
    private $file_handler;

    public function __construct() {
        $this->file_handler = new FileHandler();
        $this->CreateFileIfNotExists();
    }

    private function CreateFileIfNotExists() {
        if (!file_exists($this->ip_repo_file)) {
            echo "filename: " .$this->ip_repo_file;
            $this->file_handler->createFile($this->ip_repo_file);
        }
    }

    public function query() {
        return $this->getRegisteredIPs();
    }

    public function register($ip) {
        if (!$this->ipInFile($ip)) {
            $this->addEntryToFile($ip);
        }
    }

    public function unregister($ip_to_del) {
        if ($this->ipInFile($ip_to_del)) {
            $this->removeEntryFromFile($ip_to_del);
        }
    }

    public function getRegisteredIPs() {
        Utils::e("Get all entries from file: " . $this->ip_repo_file);
        $entries = file($this->ip_repo_file);
        $response = [];
        if (empty($entries)) {
            return $response;
        }
        foreach ($entries AS $line) {
            $line = rtrim($line);
            $entry = unserialize($line);
            array_push($response, $entry[0]);
        }
        
        foreach($response as $item) {
            Utils::e("responseItem: " . $item);
        }
        return $response;
    }

    public function ipInFile($ip) {
        $entries = file($this->ip_repo_file);
        foreach ($entries AS $line) {
            $line = rtrim($line); //remove whitespace at end of string
            $entry = unserialize($line);

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
        $entry = serialize([$ip]) . "\r\n";
        file_put_contents($this->ip_repo_file, $entry, FILE_APPEND);
    }

    public function removeEntryFromFile($ip_to_del) {
        Utils::e("Remove entry: " . $ip_to_del . " from file: " . $this->ip_repo_file);
        $entries = file($this->ip_repo_file);
        $this->file_handler->createFile($this->ip_repo_file);
        foreach ($entries AS $line) {
            $line = rtrim($line);
            $array = unserialize($line);
            if ($array[0] != $ip_to_del) {
                $this->addEntryToFile($array[0]);
            }
        }
    }

}
