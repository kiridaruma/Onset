<?php
return [
    /*
    * Onsetの管理パスワードです
    * 簡単なものに設定しないでください
    */
    'pass'  => '',

    /*
     * 部屋データを置くディレクトリへのパスです
     * カスタマイズする場合は良しなに...
     */
    'roomSavepath'  => __DIR__."/../room/",

    /*
     * bcdiceへのURL
     * ダイスボットへのパスを書いてください
     * デフォルトではindex.phpと同じ階層にあります
     */
    'bcdiceURL'     => "localhost/bcdice/roll.rb",

    /*
     * SSLを有効にするか
     * URLの先頭についてる、httpsってやつです
     * わからない人はいじらないほうがいいと思います
     */
    'enableSSL'     => false,

    /*
     * 最大部屋数
     * 1部屋当たりはそこまで容量食いません
     * サーバーのスペックに合わせて適当に設定してください
     */
    'roomLimit' => 100,

    /*
     * 部屋名の長さ制限
     */
    'maxRoomName'   => 30,

    /**
     * 部屋のパスワードの最小長
     */
    'minPassLength' => 5,

    /*
     * チャットの最大文字数と、ニックネームの最大文字数
     */
    'maxText'   => 300,
    'maxNick'   => 20,

    /*
     *部屋が自動削除されるまでの時間
     *秒数で指定してください
     *デフォルトでは10日で設定しています(60秒×60分×24時間×10日)
     */
    'roomDelTime'   => 60 * 60 * 24 * 10,

    /*
        ローカルホストで動かす時の設定
     */
    'localhost' =>  false,
    'resolve'   =>  [
        'hostname'  =>  'onset.localhost',
        'port'      =>  80,
        'host_ip'   =>  '127.0.0.1'
    ],

    'saveLog'   => __DIR__ . '/../logs/log.txt',
    'timezone'  => 'Asia/Tokyo'
];
