<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChatService
 *
 * @author student
 */
class ChatService {

    private $file_handler;
    private $chat_room_file;
    private $ip;
    private $message_nr = 0;

    public function __construct($chat_room, $ip) {
        $this->file_handler = new FileHandler();
        $this->chat_room_file = $chat_room;
        $this->ip = $ip;
        if (!file_exists($this->chat_room_file)) {
            $this->createChatRoomFile();
        }
    }

    public function setMessage($message) {
        Utils::e("Add entry: " . $message . " to file: " . $this->chat_room_file);
        $line = $this->getEncodedMessage($message);
        Utils::getLock($this->chat_room_file);
        $this->writeMessageToChatRoomFile($line);
        Utils::releaseLock($this->chat_room_file);
        Utils::e("success, message:" . $message . " was sent.");
    }

    public function getUpdate($lastmsg) {
        Utils::e("Get updates for chatroom: " . $this->chat_room_file);
        Utils::getLock($this->chat_room_file);
        $update_content = $this->getChatFileContent($lastmsg);
        Utils::releaseLock($this->chat_room_file);
        $message_nr = $this->getMessageNr();
        return ["response" => $update_content, "message_nr" => $message_nr];
    }
    
    private function getEncodedMessage($message) {
       return $this->file_handler->getEncodedLineFromEntry([$this->ip, $message]);
    }

    private function writeMessageToChatRoomFile($message) {
        file_put_contents($this->chat_room_file, $message, FILE_APPEND);
    }

    private function createChatRoomFile() {
        $this->file_handler->createFile($this->chat_room_file);
    }

    private function getMessageNr() {
        return $this->message_nr;
    }

    private function getChatFileContent($lastmsg) {
        if ($this->newMsgAvailable($lastmsg)) {
            return $this->getNewMsg($lastmsg);
        }
        return "";
    }

    private function getFormatedMsg($line) {
        $entry = $this->file_handler->getDecodedEntryFromLine($line);
        return "<div id='msg'>" . $entry[0] . ": <p>" . $entry[1] . "</p></div><br>";
    }

    private function newMsgAvailable($lastmsg) {
        $entries = file($this->chat_room_file);
        return count($entries) > $lastmsg ? true : false;
    }

    private function getNewMsg($lastmsg) {
        $response = "";
        $entries = file($this->chat_room_file);
        $i = 0;
        foreach ($entries AS $line) {
            $i += 1;
            if ($i > $lastmsg) {
                $response .= $this->getFormatedMsg($line);
            }
            $this->incrementMessageNr();
        }
        return $response;
    }

    private function incrementMessageNr() {
        $this->message_nr += 1;
    }

}
