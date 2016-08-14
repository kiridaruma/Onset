<?php
namespace App\Onset\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class OnsetController extends Controller
{
    public function index(Request $request, Response $response, $args)
    {
        $_SESSION['onset_rand'] = $rand = mt_rand();
        $del_time   = $this->onset->get('roomDelTime');
        $dir        = $this->onset->get('roomSavepath');
        $roomList   = [];
        foreach($this->onset->getRoomlist() as $roomName => $data){
            if (time() - filemtime($dir.$data['path']) > $del_time) {
                continue;
            }
            $roomList[$roomName] = $data;
        }
        return $this->view->render($response, 'index.twig', [
            'rand' => $rand,
            'roomlist' => $roomList
        ]);
    }

    public function help(Request $request, Response $response, $args)
    {
        return $this->view->render($response, 'help.twig', [
            'title' => 'Onset!とは?'
        ]);
    }

    public function chat(Request $request, Response $response, $args)
    {
        if (!isset($_SESSION['onset_roomid'])) {
            return $response->withRedirect('/room/logout');
        } else {
            return $this->view->render($response, 'onset.twig', [
                'onset_playername'  =>  $_SESSION['onset_playername'],
                'onset_playerid'    =>  $_SESSION['onset_playerid'],
                'diceSystemList'    =>  $this->onset->getDiceSystemList()
            ]);
        }
    }

    public function status(Request $request, Response $response, $args)
    {
        $result = '';
        $BCDiceURL = $this->config->bcdiceURL;
        $SSL       = $this->config->enableSSL ? 's' : '';
        file_get_contents("http{$SSL}://{$BCDiceURL}?list=1");
        if (isset($http_response_header)) {
            $result = strpos($http_response_header[0], '200') !== FALSE ? "ダイスボットの設定は正常です\n" : "ダイスボットにアクセスできません\n";
        } else {
            $result = "ダイスボットにアクセスできません\n";
        }

        $dir = $this->config->roomSavepath;
        $dirStatus = is_writable($dir) && is_readable($dir);
        $roomListStatus = is_writable($dir) && is_readable($dir);
        $result =  $dirStatus && $roomListStatus ? "部屋データの設定は正常です\n" : "部屋データにアクセスできません\n";
        return $response->write($result);
    }
}
