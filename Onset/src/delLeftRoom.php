<?php
require_once('config.php');
require_once('core.php');

$dir = $config['roomSavepath'];
$limitLeftTime = $config['roomDelTime'];
$roomlist = Onset::getRoomlist();
$i = 0;

foreach($roomlist as $room => $data){
    $roompath = $data['path'];
    $leftTime = filemtime($dir.$roompath);
    
    if(time() - $leftTime > $limitLeftTime){
        try{
            foreach(scandir($dir.$roompath."/connect/") as $value){
                if($value == "." || $value == "..") continue;
                unlink("{$dir}{$roompath}/connect/{$value}") ? "" : function(){throw new Exception();};
            }
            rmdir("{$dir}{$roompath}/connect/") ? "" : function(){throw new Exception();};

            foreach(scandir($dir.$roompath) as $value){
                if($value == "." || $value == "..") continue;
                unlink("{$dir}{$roompath}/{$value}") ? "" : function(){throw new Exception();};
            }
            rmdir($dir.$roompath) ? "" : function(){throw new Exception();};

            unset($roomlist[$room]);
            Onset::setRoomlist($roomlist) ? "" : function(){throw new Exception();};
        } catch(Exception $e) {
            echo Onset::errorJson('部屋自動削除の際にエラーが起こりました');
        }
        $i++;
    }
}

echo Onset::okJson('ok');