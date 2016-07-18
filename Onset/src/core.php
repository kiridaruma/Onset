<?php
require_once(dirname(__FILE__).'/config.php');

class Onset
{
    public static function isValidAccess($randKey)
    {
        if($randKey != $_SESSION['onset_rand']) return false;
        return true;
    }

    public static function getRoomlist()
    {
        global $config;
        $dir = $config['roomSavepath'];
        $text = file_get_contents($dir.'roomlist');
        return unserialize(rtrim($text));
    }

    public static function setRoomlist($roomlist)
    {
        global $config;
        $dir = $config['roomSavepath'];
        $ret = file_put_contents($dir.'roomlist', serialize($roomlist), LOCK_EX);
        return $ret !== FALSE;
    }

    public static function jsonStatus($message, $status = 1)
    {
        $json = [
            "status"  => $status,
            "message" => $message
        ];

        return json_encode($json);
    }

    public static function diceroll($text, $sys)
    {
        global $config;
        $url = $config['bcdiceURL'];

        $encordedText = urlencode($text);
        $encordedSys  = urlencode($sys);

        $s = "";
        if($config["enableSSL"]) $s = 's';
        $ret = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");
        if(trim($ret) == '1' || trim($ret) == 'error'){
            $ret = "";
        }
        return str_replace('onset: ', '', $ret);
    }
}

/**
 * Onset logger
 */
class Logger
{
    /**
     * @var DEBUG  デバッグ
     * @var INFO   情報
     * @var WARN   警告
     * @var DANGER 異常
     */
    const DEBUG  = 'Debug       ';
    const INFO   = 'Information ';
    const WARN   = 'Warning     ';
    const DANGER = 'Danger      ';

    /**
     * Logging function
     * ログを$config['saveLog']のファイルに記録します。
     *
     * $level にはself::DEBUG, self::INFO, self::WARN, self::DANGER などが入ります。
     *
     * e.g. Logger::log('Super error!', Logger::DANGER);
     *
     * @param string $log   text
     * @param string $level Logging level
     *
     * @return void
     */
    public static function log($log, $level = self::INFO)
    {
        global $config;

        $file = $config['saveLog'];

        $format = date('m/d H:i:s ')."%s: %s\n";

        switch ($level) {
        case self::DEBUG:
            file_put_contents($file, sprintf($format, $level, $log), FILE_APPEND);
            break;
        case self::INFO:
            file_put_contents($file, sprintf($format, $level, $log), FILE_APPEND);
            break;
        case self::WARN:
            file_put_contents($file, sprintf($format, $level, $log), FILE_APPEND);
            break;
        case self::DANGER:
            file_put_contents($file, sprintf($format, $level, $log), FILE_APPEND);
            break;
        }
    }
}
