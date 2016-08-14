<?php
namespace App\Onset;

use Slim\Container;
use App\Onset\Onset;
use App\Onset\Exception\RoomException;
use Symfony\Component\Translation\Translator;
use Illuminate\Validation\{
    Validator,
    Factory
};

class Room
{
    private $container;
    private $onset;
    private $logger;
    private $config;

    public function __construct(Container $c, Onset $onset)
    {
        $this->container    = $c;
        $this->onset        = $onset;
        $this->logger       = $c->get('logger');
        $this->config       = $c->get('config');
    }

    /**
     * 部屋の作成
     * @param  array    POSTのパラメータ
     * @return array    JSONで返す用のデータ
     */
    public function create($params)
    {
        $result = [];
        $factory = new Factory(new Translator('ja'));
        $validator = $factory->make($params, [
            'rand'      => 'required',
            'roomPw'    => 'required|min:'.intval($this->config->minPassLength),
            'roomName'  => 'required|max:'.intval($this->config->maxRoomName)
        ], self::getErrorMessages());
        try {
            $roomName = $params['roomName'];
            $roomList = $this->onset->getRoomlist();

            if (!$this->onset->isValidAccess($params['rand'])) {
                throw new \Exception('不正なアクセス。');
            } elseif ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            } elseif (isset($roomList[$roomName])) {
                throw new \Exception('同名の部屋がすでに存在しています。');
            } elseif (count($roomList) >= $this->config->roomLimit) {
                throw new \Exception('部屋数制限いっぱいです。');
            }

            $uuid = uniqid('', true);
            $roomDir = rtrim($this->config->roomSavepath,'/')."/{$uuid}";

            if (!mkdir($roomDir)) {
                throw new RoomException('部屋ディレクトリ作成に失敗しました。');
            } elseif (!mkdir($roomDir.'/connect')) {
                throw new RoomException('接続ディレクトリ作成に失敗しました。');
            } elseif (!touch($roomDir.'/pass.hash')) {
                throw new RoomException('パスワードハッシュの生成に失敗しました。');
            } elseif (!touch($roomDir.'/xxlogxx.txt')) {
                throw new RoomException('チャットログの生成に失敗しました。');
            } elseif (!touch($roomDir.'/chatLogs.json')) {
                throw new RoomException('チャットログの生成に失敗しました。');
            } elseif (!chmod($roomDir,                 0777)) {
                throw new RoomException('パーミッションの変更に失敗しました。');
            } elseif (!chmod($roomDir.'/connect/',     0777)) {
                throw new RoomException('パーミッションの変更に失敗しました。');
            } elseif (!chmod($roomDir.'/pass.hash',    0666)) {
                throw new RoomException('パーミッションの変更に失敗しました。');
            } elseif (!chmod($roomDir.'/chatLogs.json',0666)) {
                throw new RoomException('パーミッションの変更に失敗しました。');
            } elseif (!chmod($roomDir.'/xxlogxx.txt',  0666)) {
                throw new RoomException('パーミッションの変更に失敗しました。');
            }

            $hash = password_hash($params['roomPw'], PASSWORD_DEFAULT);
            if (!file_put_contents($roomDir.'/pass.hash', $hash)) {
                throw new RoomException('パスワードハッシュのデータ挿入に失敗しました。');
            }
            $roomList[$roomName]["path"] = $uuid;
            if (!$this->onset->setRoomlist($roomList)) {
                throw new RoomException('部屋一覧の処理に失敗しました。');
            }
            $result['status'] = true;
        } catch (RoomException $e){
            $this->logger->critical("create_room:{$e->getMessage()}", $params);
            $result = [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $result = [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        }
        return $result;
    }

    /**
     * 部屋の削除
     * @param  array    POSTのパラメータ
     * @return array    JSONで返す用のデータ
     */
    public function remove($params)
    {
        $result = [];
        $factory = new Factory(new Translator('ja'));
        $validator = $factory->make($params, [
            'rand'      => 'required',
            'roomPw'    => 'required|min:'.intval($this->config->minPassLength),
            'roomName'  => 'required|max:'.intval($this->config->maxRoomName)
        ], self::getErrorMessages());
        try {
            $roomName = $params['roomName'];
            $roomList = $this->onset->getRoomlist();

            if (!$this->onset->isValidAccess($params['rand'])) {
                throw new \Exception('不正なアクセス。');
            } elseif ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            } elseif (!isset($roomList[$roomName])) {
                throw new \Exception('部屋が存在しません');
            }

            $roomId   = $roomList[$roomName]['path'];
            $dir        = $this->config->roomSavepath;
            $roomDir       = $dir.$roomId;
            $pass       = $params['roomPw'];
            $hash       = file_get_contents($roomDir.'/pass.hash');
            if (!password_verify($pass, $hash) && $pass != $this->config->pass) {
                throw new \Exception('パスワードを間違えています');
            }

            /*  削除処理    */
            foreach (scandir($roomDir.'/connect/') as $k) {
                if ($k == '.' || $k == '..') {
                    continue;
                } elseif (!unlink($roomDir.'/connect/'.$k)) {
                    throw new RoomException('接続ディレクトリの削除に失敗。');
                }
            }
            if (!rmdir($roomDir.'/connect/')) {
                throw new RoomException('接続ディレクトリの削除に失敗。');
            }

            foreach (scandir($roomDir) as $k) {
                if ($k == '.' || $k == '..') {
                    continue;
                } elseif (!unlink($roomDir.'/'.$k)) {
                    throw new RoomException('部屋ディレクトリの削除に失敗。');
                }
            }
            if (!rmdir($roomDir)) {
                throw new RoomException('部屋ディレクトリの削除に失敗。');
            }
            unset($roomList[$roomName]);
            if (!$this->onset->setRoomlist($roomList)) {
                throw new RoomException('部屋リストからの削除に失敗');
            }

            $result['status'] = true;
        } catch (RoomException $e) {
            $this->logger->critical("remove_room:{$e->getMessage()}", $params);
            $result = [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $result = [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        }
        return $result;
    }

    /**
     * ログイン処理. 問題なければ status = true
     * @param  array $params
     * @return array statusがfalseならmessage付き
     */
    public function enter($params)
    {
        $result = [];
        $factory = new Factory(new Translator('ja'));
        $validator = $factory->make($params, [
            'playerName'    => 'required|max:'.intval($this->config->maxNick),
            'roomPw'        => 'required|min:'.intval($this->config->minPassLength),
            'roomName'      => 'required|max:'.intval($this->config->maxRoomName)
        ], self::getErrorMessages());
        try {
            $roomName = $params['roomName'];
            $roomList = $this->onset->getRoomlist();

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            } elseif (!isset($roomList[$roomName])) {
                throw new \Exception('部屋が存在しません');
            }

            $roomId     = $roomList[$roomName]['path'];
            $dir        = $this->config->roomSavepath;
            $roomDir    = $dir.$roomId;
            $pass       = $params['roomPw'];
            $hash       = file_get_contents($roomDir.'/pass.hash');
            if (!password_verify($pass, $hash) && $pass != $this->config->pass) {
                throw new \Exception('パスワードを間違えています');
            }
            $id = ip2long($_SERVER['REMOTE_ADDR']) + mt_rand();
            $_SESSION['onset_playername']   = $params['playerName'];
            $_SESSION['onset_roomid']       = $roomId;
            $_SESSION['onset_playerid']     = dechex($id);
            $result['status']       = true;
        } catch (\Exception $e) {
            $result = [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        }
        return $result;
    }

    public function autoremove($params)
    {
        $result = ['status' => true];
        $dir = $this->config->roomSavepath;
        $limitLeftTime = $this->config->roomDelTime;
        $roomList = $this->onset->getRoomlist();
        $i = 0;

        foreach ($roomList as $room => $data) {
            $roomId = $data['path'];
            $leftTime = filemtime($dir.$roomId);
            $roomDir     = $dir.$roomId;

            if (time() - $leftTime > $limitLeftTime) {
                try {
                    foreach (scandir($roomDir.'/connect/') as $k) {
                        if ($k == "." || $k == "..") continue;
                        if (!unlink($roomDir.'/connect/'.$k)) {
                            throw new RoomException($room);
                        }
                    }
                    if(!rmdir($roomDir.'/connect/')) {
                        throw new RoomException($room);
                    }

                    foreach (scandir($roomDir) as $k) {
                        if ($k == "." || $k == "..") continue;
                        if (!unlink($roomDir.$k)) {
                            throw new RoomException($room);
                        }
                    }
                    if (!rmdir($dir.$roomId)) {
                        throw new RoomException($room);
                    }

                    unset($roomList[$roomName]);
                    if($this->onset->setRoomlist($roomList)) {
                        throw new RoomException($room);
                    }
                } catch (RoomException $e) {
                    $this->logger->critical('autoremove_error:'.$e->getMessage());
                    $result = [
                        'status'    => false,
                        'message'   => $e->getMessage()
                    ];
                } catch (\Exception $e) {
                    $result = [
                        'status'    => false,
                        'message'   => $e->getMessage()
                    ];
                }
                $i++;
            }
        }
        return $result;
    }

    public function read($params)
    {
        $result             = [];
        $params['roomId']   = $_SESSION['onset_roomid'];
        $roomId             = $params['roomId'];
        $factory = new Factory(new Translator('ja'));
        $validator = $factory->make($params, [
            'roomId'    =>  'required',
            'time'      =>  'required'
        ], self::getErrorMessages());
        try {
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
            $roomDir = $this->config->roomSavepath . $roomId;
            $data = '';
            $time = $params['time'];
            if ($time < filemtime($roomDir."/xxlogxx.txt") * 1000) {
                $fp = fopen($roomDir."/xxlogxx.txt", 'r');
                do {
                    $line = fgets($fp);
                    if($line !== false) {
                        $data .= $line;
                    }
                } while($line !== false);
                fclose($fp);
            }
            $tmp = $roomDir."/connect/".$_SESSION['onset_playerid'];
            file_put_contents($tmp, time()."\n".$_SESSION['onset_playername'], LOCK_EX);
            $result = [
                'status'    =>  true,
                'data'      =>  $data
            ];
        } catch (\Exception $e) {
            $result = [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        }
        clearstatcache();
        return $result;
    }

    public function write($params)
    {
        $result             = [];
        $params['roomId']   = $_SESSION['onset_roomid'];
        $factory = new Factory(new Translator('ja'));
        $validator = $factory->make($params, [
            'playerName'        =>  'required|max:'.intval($this->config->maxNick),
            'roomId'            =>  'required',
            'chatContent'       =>  'required|max:'.intval($this->config->maxText),
            'diceSystem'        =>  'required'
        ], self::getErrorMessages());
        try {
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
            $_SESSION['onset_playername'] = $params['playerName'];
            $roomId         = $params['roomId'];
            $roomDir        = $this->config->roomSavepath . $roomId;
            $playerName     = $params['playerName'];
            $chatContent    = $params['chatContent'];
            $diceSystem     = $params['diceSystem'];
            $diceRes        = $this->onset->diceRoll($chatContent, $diceSystem);

            // json
            $rawJson = [
                "time"        => date('U'),
                "playerId"    => $_SESSION['onset_playerid'],
                "playerName"  => $playerName,
                "chatContent" => $chatContent,
                "diceRes"     => $diceRes,
                "diceSystem"  => $diceSystem
            ];
            $json   = $this->onset->getChatLogs($roomId);
            $json[] = $rawJson;
            $json   = json_encode($json, JSON_UNESCAPED_UNICODE);
            file_put_contents($roomDir.'/chatLogs.json', $json, LOCK_EX);

            /*  チャット用ログの生成  */
            $chatContent = nl2br($chatContent);
            $diceRes     = htmlspecialchars($diceRes,     ENT_QUOTES);
            $playerName  = htmlspecialchars($playerName,  ENT_QUOTES);
            $chatContent = htmlspecialchars($chatContent, ENT_QUOTES);
            $line = "<div class=\"chat\"><b>{$playerName}</b>({$_SESSION['onset_playerid']})<br>\n{$chatContent}<br>\n<i>{$diceRes}</i></div>\n";
            $line = $line . file_get_contents($roomDir.'/xxlogxx.txt');
            file_put_contents($roomDir.'/xxlogxx.txt', $line, LOCK_EX);

            $result['status'] = true;
        } catch (\Exception $e) {
            $this->logger->critical('write', [$e->getMessage()]);
            $result['status'] = false;
        }
        return $result;
    }

    /**
     * ログイン状況の確認
     * @return string ログイン状況
     */
    public function users($params){
        $result = '';
        $params['roomId'] = $_SESSION['onset_roomid'];
        $factory = new Factory(new Translator('ja'));
        $validator = $factory->make($_SESSION, [
            'onset_playerid'    =>  'required',
            'onset_roomid'      =>  'required',
            'onset_playername'  =>  'required'
        ], self::getErrorMessages());
        try {
            if ($validator->fails()) {
                throw new \Exception('不正なアクセス');
            }
            $roomId   = $_SESSION['onset_roomid'];
            $playerId = $_SESSION['onset_roomid'];
            $roomDir = $this->config->roomSavepath . "{$roomId}/connect/";
            $loginUserList = scandir($roomDir);
            $params['lock'] = $params['lock'] ?? null;
            if($params['lock'] === 'unlock') {
                file_put_contents($roomDir.$playerId, time()."\n".$_SESSION['onset_playername']);
                die();
            }

            file_put_contents($roomDir.$roomId, time()."\n".$_SESSION['onset_playername']."\nlocked");
            $ret = '';
            $num = 0;
            foreach($loginUserList as $playerId) {
                if($playerId == "." || $playerId == "..") continue;
                $data = explode("\n",file_get_contents($roomDir.$playerId));
                if (count($data) === 3) {
                    list($time, $playerName, $isLock) = $data;
                } else {
                    $time = $data[0];
                    $playerName = $data[1];
                    $isLock = null;
                }

                if($time + 5 < time() && $isLock !== 'locked') {
                    unlink($roomDir.$playerId);
                    continue;
                }
                $ret .= $playerName.'#'.$playerId."\n";
                $num++;
            }
            $result = $num."人がログイン中\n".$ret;
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * 現在いる部屋のチャットログを取得する
     * @return string
     */
    public function getChatLog()
    {
        $dir = $this->config->roomSavepath;
        $chatLog = $dir.$_SESSION['onset_roomid']."/xxlogxx.txt";
        $text = file_get_contents($chatLog);
        // $text    = htmlspecialchars_decode(strip_tags(file_get_contents($chatLog)));
        return $text;
    }

    /**
     * 入力値チェックのエラーメッセージを取得
     * @return array
     */
    private static function getErrorMessages()
    {
        return [
            'rand.required'         =>  '不正なアクセス',
            'roomPw.required'       =>  'パスワードが空です',
            'roomPw.min'            =>  'パスワードが短すぎます',
            'roomName.required'     =>  '部屋名がセットされていません',
            'roomName.max'          =>  '部屋名が長すぎます',
            'playerName.required'   =>  '名前がセットされていません',
            'playerName.max'        =>  '名前が長すぎます',
            'chatContent.required'  =>  '本文がセットされていません',
            'chatContent.max'       =>  '本文が長すぎます',
            'diceSystem.required'   =>  'diceSystemがセットされていません',
            'time.required'         =>  '不正なアクセス'
        ];
    }
}
