<?php
require_once(dirname(__FILE__).'/config.php');


class Onset
{
    public static function isValidAccess($randKey)
    {
        if($randKey != $_SESSION['onset_rand']) return false;
        return true;
    }

    public static function getRoomlist()
    {
        global $config;
        $dir = $config['roomSavepath'];
        $text = file_get_contents($dir.'roomlist');
        return unserialize(rtrim($text));
    }

    public static function setRoomlist($roomlist)
    {
        global $config;
        $dir = $config['roomSavepath'];
        $ret = file_put_contents($dir.'roomlist', serialize($roomlist), LOCK_EX);
        return $ret !== FALSE;
    }

    public static function jsonStatus($message, $status = 1)
    {
        header('content-type: application/json; charset=utf-8');
        $json = [
            "status"  => $status,
            "message" => $message
        ];

        return json_encode($json);
    }

    public static function diceRoll($chatContent, $diceSystem)
    {
        global $config;
        $url = $config['bcdiceURL'];

        $encordedText = urlencode($chatContent);
        $encordedSys  = urlencode($diceSystem);

        $s = "";
        if($config["enableSSL"]) $s = 's';
        $ret = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");
        if(trim($ret) == '1' || trim($ret) == 'error'){
            $ret = "";
        }
        return str_replace('onset: ', '', $ret);
    }
}
