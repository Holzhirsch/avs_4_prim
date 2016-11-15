<?php

/**
 * @author era
 * 
 * Description of FileHandler:
 * Class to handle file methods.
 * @param String $file_name the name of the file, where machine and ip are stored
 * @param String $machine_name the machine_name
 * @param String $ip the ip adress of the machine
 */
class FileHandler {

    /**
     * Creates a file with given file name.
     */
    public function createFile($file_name) {
        Utils::e("Create file: " . $file_name);
        $handle = fopen($file_name, "w+");
        fclose($handle);
    }

    public function getDecodedEntryFromLine($line) {
        return json_decode(rtrim(utf8_encode($line)));
    }

    public function getEncodedLineFromEntry($entry) {
        return json_encode($entry) . "\r\n";
    }

}
