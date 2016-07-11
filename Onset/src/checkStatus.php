<?php
require_once('core.php');
require_once('config.php');
header('Content-Type: text/plain');
echo Onset::checkPermition() ? "部屋データの読み書きは正常です" : "部屋データの読み書きに問題があります"; echo "\n";
echo Onset::checkBcdice() ? "ダイスボットの設定は正常です" : "ダイスボットの設定に問題があります"; echo "\n";