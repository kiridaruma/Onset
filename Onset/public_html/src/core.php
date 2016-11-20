<?php

require_once(__DIR__.'/config.php');

class Onset
{

    public static function varidate(&$input)
    {
        $val = $input;
        unset($input);
        if($val == null) return false;
        $val = trim($val);
        if($val == "") return false;
        return $val;
    }

    public static function getRoomlist()
    {
        $name = RoomSavepath . 'roomlist';
        if (!file_exists($name)) {
            file_put_contents($name, '{}');
        }
        return json_decode(file_get_contents($name));
    }

    public static function saveRoomlist(stdClass $roomlist)
    {
        $dir = RoomSavepath;
        $ret = file_put_contents($dir.'roomlist', json_encode($roomlist), LOCK_EX);
        return $ret;
    }

    public static function jsonMessage($message, $status = 1, $data = [])
    {
        $json = [
            "status"  => $status,
            "message" => $message,
            "data" => $data
        ];
        header('Content-Type: application/json');
        return json_encode($json);
    }

    public static function diceroll($text, $sys)
    {
        $url = self::getBcdiceUrl();

        $encordedText = urlencode($text);
        $encordedSys  = urlencode($sys);

        $ret = file_get_contents($url."?text={$encordedText}&sys={$encordedSys}");
        if(trim($ret) == '1' || trim($ret) == 'error'){
            $ret = "";
        }
        return trim(str_replace('onset: ', '', $ret));
    }

    public static function getBcdiceUrl()
    {
        if(BcdiceURL != "") return BcdiceURL;
        $_dir = str_replace("\\", "/", __DIR__);
        $fullPath = preg_replace("/src$/", "", $_dir) . "bcdice/roll.rb";
        $docRoot = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME']);
        $urlPath = str_replace($docRoot, "", $fullPath);
        $procotlName = !isset($_SERVER['HTTPS']) ? 'http://' : 'https://';
        return $procotlName . $_SERVER['SERVER_NAME'] . $urlPath;
    }

    public static function searchLog(array $chatLog, $time){
        if($time == 0) return $chatLog;
        $point = count($chatLog) - 1;
        $flag = false;
        for(;isset($chatLog[$point]) && $chatLog[$point]->time > $time; $point -= 1) $flag = true;
        if($flag) return array_slice($chatLog, $point);
        else return [];
    }

    //正直線形探索で十分と思うけど、念のため二分探索の関数もおいておきます
    private static function binarySearch(array $chatLog, $time){
        $point = floor(count($chatLog) / 2);
        $width = $point;
        while($width > 1){
            if($chatLog[$point]->time < $time) $point += floor($width / 2);
            if($chatLog[$point]->time > $time) $point -= floor($width / 2);
            $width = floor($width / 2);
        }
        return array_slice($chatLog, $point+1);
    }

    //$roomPathは、語尾が'/'で終わっているパス名を引数に与えてください
    public static function removeRoomData($roomPath){
        $dirArr = scandir($roomPath);
        foreach($dirArr as $child){
            if($child == '.' || $child == '..') continue;
            if(is_dir($roomPath.$child)){
                self::removeRoomData($roomPath.$child."/");
                continue;
            }
            if(!unlink($roomPath.$child)) throw new Exception("ファイル".$roomPath.$child."の削除に失敗しました");
        }
        if(!rmdir($roomPath)) throw new Exception("フォルダ".$roomPath."の削除に失敗しました");
    }

    public static function viewRoomlist(array $roomlist){
        $str = '';
        foreach($roomlist as $idx => $roomName){
            $str .= '<div class="form-check">'
            .'<label class="form-check-label room">'
            .'<input type="radio" class="form-check-input" name="room" value="'.$roomName.'">'.$roomName
            .'</label>'
            .'</div>';
        }
        return $str;
    }

}
