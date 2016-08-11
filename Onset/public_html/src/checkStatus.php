<?php
require_once 'core.php';
header('Content-Type: text/plain');

$coreStatusArr = array();
foreach (scandir('./') as $val) {
    if($val == '.' || $val == '..') continue;
    $coreStatusArr[$val] = is_readable($val);
}

foreach ($coreStatusArr as $key => $val) {
    echo $val ? "" : $key."にアクセスできません\n";
}

$BCDiceURL = Config::bcdiceURL;
$SSL       = Config::enableSSL ? 's' : '';

file_get_contents("http{$SSL}://{$BCDiceURL}?list=1");
echo strpos($http_response_header[0], '200') !== FALSE ? "ダイスボットの設定は正常です\n" : "ダイスボットにアクセスできません\n";

$dir = Config::roomSavepath;
$dirStatus = is_writable($dir) && is_readable($dir);
$roomListStatus = is_writable($dir) && is_readable($dir);
echo $dirStatus && $roomListStatus ? "部屋データの設定は正常です\n" : "部屋データにアクセスできません\n";
