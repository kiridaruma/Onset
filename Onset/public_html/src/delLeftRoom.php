<?php
require_once(__DIR__.'/core.php');

$dir = RoomSavepath;
$limitLeftTime = RoomDelTime;
$roomlist = Onset::getRoomlist();
$i = 0;

foreach ($roomlist as $room => $data) {
    $roompath = $data->path;
    $leftTime = filemtime($dir.$roompath);

    $_dir     = $dir.$roompath;

    if (time() - $leftTime > $limitLeftTime) {
        try {
            foreach (scandir($_dir.'/connect/') as $k) {
                if ($k == "." || $k == "..") continue;
                if (!unlink($_dir.'/connect/'.$k)) throw new Exception();
            }

            if(!rmdir($_dir.'/connect/')) throw new Exception();

            foreach (scandir($_dir) as $k) {
                if ($k == "." || $k == "..") continue;
                if (!unlink($_dir.$k)) throw new Exception();
            }

            if (!rmdir($dir.$roompath)) throw new Exception();

            unset($roomlist->{$room});
            if(!Onset::saveRoomlist($roomlist)) throw new Exception();
        } catch (Exception $e) {
            echo Onset::jsonMessage('部屋自動削除の際にエラーが起こりました', -1);
            die();
        }
        $i++;
    }
}

echo Onset::jsonMessage('ok');
