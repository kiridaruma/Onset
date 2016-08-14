<?php
namespace App\Onset;

use GuzzleHttp\Client;
use Slim\Container;

class Onset
{
    private $config;
    private $logger;

    public function __construct(Container $c)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $this->config = $c->get('config');
        $this->logger = $c->get('logger');
    }

    public function isValidAccess($randKey)
    {
        error_log($randKey);
        error_log($_SESSION['onset_rand']);

        return $randKey === strval($_SESSION['onset_rand']) ? true : false;
    }

    public function getRoomlist()
    {
        $dir = $this->config['roomSavepath'];
        if (!file_exists("{$dir}roomlist")) {
            file_put_contents("{$dir}roomlist", 'a:0:{}');
        }
        $chatContent = file_get_contents("{$dir}roomlist");
        return unserialize(rtrim($chatContent));
    }

    public function setRoomlist($roomList)
    {
        $dir = $this->config['roomSavepath'];
        $ret = file_put_contents("{$dir}roomlist", serialize($roomList), LOCK_EX);
        return $ret !== FALSE;
    }

    public function diceRoll($text, $diceSystem	)
    {
        $url = $this->config['bcdiceURL'];

        $encordedText = urlencode($text);
        $encordedSys  = urlencode($diceSystem);

        $s = "";
        if($this->config["enableSSL"]) $s = 's';
        $url = "http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}";
        $result = '';
        try {
            $client = new Client();
            if ($this->config['localhost'] === true) {
                $resolve = [sprintf(
                    "%s:%d:%s",
                    $this->config['resolve']['hostname'],
                    $this->config['resolve']['port'],
                    $this->config['resolve']['host_ip']
                )];
                $url = "http{$s}://{$this->config['resolve']['hostname']}/bcdice/roll.rb?text={$encordedText}&sys={$encordedSys}";
                $result = $client->get($url, ['curl' => [CURLOPT_RESOLVE =>  $resolve]])
                        ->getBody();
            } else {
                $result = $client->get($url)->getBody();
            }
            if(trim($result) == '1' || trim($result) == 'error'){
                $result = "";
            }
        } catch (\Exception $e){
            $this->logger->critical('diceroll', ['message' => $e->getMessage()]);
            $result = '';
        }
        return str_replace('onset: ', '', $result);
    }

    /**
     * $diceSystemの取得
     * @return array
     */
    public function getDiceSystemList()
    {
        $result = [];
        $url    = $this->config['bcdiceURL'];
        $s      = $this->config['enableSSL'] ? 's' : '';
        $url    = "http{$s}://{$url}?list=1";
        try {
            $client = new Client();
            if ($this->config['localhost'] === true) {
                $resolve = [sprintf(
                    "%s:%d:%s",
                    $this->config['resolve']['hostname'],
                    $this->config['resolve']['port'],
                    $this->config['resolve']['host_ip']
                )];
                $url    = "http{$s}://{$this->config['resolve']['hostname']}/bcdice/roll.rb?list=1";
                $result = explode(
                    "\n",
                    $client->get($url, ['curl' => [CURLOPT_RESOLVE =>  $resolve]])
                        ->getBody()
                );
            } else {
                $result = explode("\n", $client->get($url)->getBody());
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->logger->critical(
                'Guzzle_error',
                ['message' => $e->getMessage(), 'config'=> $this->config['resolve']]
            );
            $result = [];
        } catch (\Exception $e){
            $this->logger->critical('error_in_getDiceSystemList', ['message' => $e->getMessage()]);
            $result = [];
        }
        return $result;
    }

    public function get($key)
    {
        return $this->config[$key] ?? null;
    }

    public function getChatLogs($roomId, $isDecode = true)
    {
        $dir  = $this->config['roomSavepath'];
        $data = '';
        if (file_exists($dir.$roomId.'/chatLogs.json')) {
            $data = file_get_contents($dir.$roomId.'/chatLogs.json');
        }
        return json_decode($data, $isDecode);
    }
}
