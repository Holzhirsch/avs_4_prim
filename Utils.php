<?php

/**
 * @author era
 * 
 * Description of Utils:
 * Utility class.
 */
class Utils {

    public static $server_ip = "192.168.0.11";
    public static $repo_server_url = "http://192.168.0.11/AVS_3/API.php";
    
    
    /**
     * Echo debug message.
     * @param type $text
     */
    public static function e($text) {
        STATIC $count = 0;
        $count++;
        $bt = debug_backtrace(1);
        $caller = $bt[1];
        $msg = "[DEBUG]_F[" . $caller['file'] . "]_C[" . $count . "]_L[" . $caller['line'] . "]: " . $text . "<br>";
        if(!file_exists("log")) {
            $handle = fopen("log", "w+");
        }
        $handle = fopen("log", "a");
        fwrite($handle, $msg . "\r\n");
        fclose($handle);
    }

    public static function getLock($file_name) {
        $try = true;
        while ($try) {
            if (!file_exists($file_name . "lock")) {
                Utils::createLockFile($file_name);
                $try = false;
                Utils::e("got lock on: " . $file_name);
            } else {
                usleep(500000);
            }
        }
    }

    private static function createLockFile($file_name) {
        $handle = fopen($file_name . "lock", "w+");
        fclose($handle);
    }

    public static function releaseLock($file_name) {
        unlink($file_name . "lock");
        Utils::e(" released lock on: " . $file_name);
    }

}
