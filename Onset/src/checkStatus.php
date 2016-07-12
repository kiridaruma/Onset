<?php
require_once('core.php');
require_once('config.php');
header('Content-Type: text/plain');

$coreStatusArr = [];
$coreStatus = true;
foreach (scandir('./') as $key => $val) {
    if($val == '.' || $val == '..') continue;
    $coreStatusArr[$key] = is_readable($val) && is_executable($val);
}

foreach ($coreStatusArr as $key => $val) {
    echo $val ? "" : function(){$key."にアクセスできません\n"; $coreStatus = false;};
}
echo $coreStatus ? "コア機能は正常です\n" : "";

$url = $config['bcdiceURL'];
$s = $config['enableSSL'] ? 's' : '';
file_get_contents("http{$s}://{$url}?list=1");
echo strpos($http_response_header[0], '200') !== FALSE ? "ダイスボットの設定は正常です\n" : "ダイスボットにアクセスできません\n";

$roomPath = $config['roomSavepath'];
$roomDirStatus = is_writable($roomPath) && is_readable($roomPath);
$roomlistStatus = is_writable($roomPath) && is_readable($roomPath);
echo $roomDirStatus && $roomlistStatus ? "部屋データの設定は正常です\n" : "部屋データにアクセスできません\n";
