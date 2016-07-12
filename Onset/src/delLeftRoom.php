<?php
require_once('config.php');
require_once('core.php');

$dir = $config['roomSavepath'];
$limitLeftTime = $config['roomDelTime'];

foreach(scandir($dir) as $val){
    if($val == '.' || $val == '..') continue;
    $leftTime = filemtime($val);
    $roompath = $val;
    
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
            echo Onset::errorJson('error');
        }
    }
}

echo Onset::okJson('ok');