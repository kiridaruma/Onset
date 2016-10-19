<?php

require_once(__DIR__.'/config.php');

class Onset
{

    public static function varidate(&$input)
    {
        $val = $input;
        unset($input);
        if($val == null) return false;
        return $val;
    }

    public static function getRoomlist()
    {
        $dir = RoomSavepath;
        $text = file_get_contents($dir.'roomlist');
        return json_decode(rtrim($text));
    }

    public static function saveRoomlist($roomlist)
    {
        $dir = RoomSavepath;
        $ret = file_put_contents($dir.'roomlist', json_encode($roomlist), LOCK_EX);
        return $ret;
    }

    public static function jsonMessage($message, $data = [], $code = 1)
    {
        $json = [
            "code"  => $code,
            "message" => $message,
            "data" => $data
        ];
        return json_encode($json);
    }

    public static function diceroll($text, $sys)
    {
        $url = self::getBcdiceUrl();

        $encordedText = urlencode($text);
        $encordedSys  = urlencode($sys);

        $ret = file_get_contents("text={$encordedText}&sys={$encordedSys}");
        if(trim($ret) == '1' || trim($ret) == 'error'){
            $ret = "";
        }
        return str_replace('onset: ', '', $ret);
    }

    private static function getBcdiceUrl()
    {
        if(BcdiceURL == "") return BcdiceUrl;
        $fullPath = preg_replace("/src$/", "", __DIR__) . "bcdice/roll.rb";
        $docRoot = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME']);
        $urlPath = str_replace($docRoot, "", $fullPath);
        $procotlName = $_SERVER['HTTPS'] == null ? 'http://' : 'https://';
        return $procotlName . $_SERVER['SERVER_NAME'] . $urlPath;
    }

}
