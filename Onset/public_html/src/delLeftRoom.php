<?php
require_once __DIR__ . '/core.php';

$roomlist = Onset::getRoomlist();
$i = 0;

foreach ($roomlist as $room => $data) {

    $dir = RoomSavepath . $data->path . "/";
    $leftTime = filemtime($dir);

    if (time() - $leftTime > RoomDelTime) {
        try {
            Onset::removeRoomData($dir);
            unset($roomlist->{$room});
            if (!Onset::saveRoomlist($roomlist)) {
                throw new Exception("部屋インデックスデータの保存に失敗しました");
            }

        } catch (Exception $e) {
            echo Onset::jsonMessage($e->getMessage(), -1);
            die();
        }
        $i += 1;
    }
}

echo $i > 0 ? Onset::jsonMessage($i . '部屋削除しました') : Onset::jsonMessage('ok');
