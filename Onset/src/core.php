<?php
require_once(dirname(__FILE__).'/config.php');


class Onset{
    
    
    /*
     * isIllegalAccess
     */
    static function isValidAccess($randKey) {
        session_start();
        if($randKey != $_SESSION['onset_rand']) {
            return false;
        }
        return true;
    }
    
    /*
     * getRoomlist
     */
    static function getRoomlist() {
        global $config;
        $dir = $config['roomSavepath'];
        return unserialize(file_get_contents($dir.'roomlist'));
    }
    
    
    static function okJson($data){
        $json = [
            "status" => 1,
            "data" => $data
        ];
        
        return json_encode($json);
        
    }
    
    
    static function errorJson($message){
        $json = [
            "status" => -1,
            "message" => $message
        ];
        return json_encode($json);
    }
    
    static function diceroll($text){
        global $config;
        $url = $config['bcdiceURL'];
        
        $encordedText = urlencode($text);
        $encordedSys  = urlencode($sys);
        
        $s = "";
        if($config["enableSSL"]){$s = 's';}
        $ret = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");
        if(trim($ret) == '1' || trim($ret) == 'error'){
            $ret = "";
        }
        return str_replace('onset: ', '', $ret);
    }
    
    static function getSystemList(){
        global $config;
        $url = $config['bcdiceURL'];
        $s = '';
        if($config['enableSSL']){$s = 's';}
        return split("\n", file_get_contents("http{$s}://{$url}?list=1"));
    }
    
}
