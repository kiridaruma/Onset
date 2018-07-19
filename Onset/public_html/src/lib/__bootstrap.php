<?php

// オートロードの設定
spl_autoload_register(function ($className) {
    require __DIR__ . "/{$className}.php";
});

// エラーハンドリング(例外を投げたり、ロギングしたり)
set_error_handler(function ($errno, $errstr, $errfile, $errline, $_) {
    $str = sprintf("エラー[%d]:\t%s\t%s(line%d)", $errno, $errstr, $errfile, $errline);
    throw new \RuntimeException($str);
});
