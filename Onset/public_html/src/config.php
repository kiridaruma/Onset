<?php
/*
 * Onset!の設定ファイルです
 * マスターパスワードや管理設定はここから行えます
 */


class config{
    /*
     * Onsetの管理パスワードです
     * 簡単なものに設定しないでください
     */
    const pass = "";

    /*
     * 部屋データを置くディレクトリへのパスです
     * カスタマイズする場合は良しなに...
     */
    const roomSavepath = __DIR__."/../../room/";

    /*
     * bcdiceへのURL
     * ダイスボットへのパスを書いてください
     * デフォルトではindex.phpと同じ階層にあります
     */
    const bcdiceURL = "trpg.moegi.mydns.jp/TRPG/Onset/public_html/bcdice/roll.rb";

    /*
     * SSLを有効にするか
     * URLの先頭についてる、httpsってやつです
     * わからない人はいじらないほうがいいと思います
     */
    const enableSSL = true;

    /*
     * 最大部屋数
     * 1部屋当たりはそこまで容量食いません
     * サーバーのスペックに合わせて適当に設定してください
     */
    const roomLimit = 100;

    /*
     * 部屋名の長さ制限
     */
    const maxRoomName = 30;

    /*
     * チャットの最大文字数と、ニックネームの最大文字数
     */
    const maxText = 300;
    const maxNick = 20;

    /*
     *部屋が自動削除されるまでの時間
     *秒数で指定してください
     *デフォルトでは10日で設定しています(60秒×60分×24時間×10日)
     */
    const roomDelTime  = 60 * 60 * 24 * 10;

    /*
     * ログファイル
     */
    const saveLog = __DIR__ . '/../log.txt';
}
