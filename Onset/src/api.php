<?php
require_once('core.php');
require_once('config.php');

/**
 * Class OnsetAPI
 *
 * @author Akagi.
 * @version 1.0.0
 *
 */
class OnsetAPI {
  private $roomID        = '';
  private $roomPassword  = '';
  private $resultHash    = [
    'resultMessages'    => [
      'code'            => '200',
      'message'         => 'OK'
    ]
  ];

  public function __construct($roomID, $roomPassword) {
    $this->setRoomID($roomID);
    $this->setRoomPass($roomPassword);
  }

  /**
   * 部屋IDのセッター。
   *
   * @param string $roomID 部屋ID
   *
   */
  public function setRoomID($roomID) {
    $this->roomID = (string)filter_var($roomName);
  }

  /**
   * 部屋パスワードのセッター。
   *
   * @param string $roomPassword 部屋パスワード
   *
   */
  public function setRoomPass($roomPassword) {
    $this->roomPassword = (string)filter_var($roomPassword);
  }

  /**
   * 結果配列のHTTPステータスコードのセッター。
   *
   * @param string $code HTTPステータスコード
   *
   */
  private function setResultMessageID($code) {
    $this->resultHash['resultMessages']['code'] = $code;
  }

  /**
   * 結果配列のメッセージのセッター。
   *
   * @param string $text メッセージ
   *
   */
  private function setResultMessageContent($text) {
    $this->resultHash['resultMessages']['message'] = $text;
  }

  /**
   * 一連のメソッドの実行
   *
   * @param string $f 実行したいAPIの指定
   *
   * @return mixed APIの実行結果
   *
   */
  public function executeFunction($f) {
    // $fが空なら400を返す。
    if($f === null || $f === '') {
      $this->setResultMessageID('400 Bad Request.');
      $this->setResultMessageContent('Please set action.');

      // $this->resultHashをエンコード及び返却
      echo json_encode($this->resultHash);

      return false;
    }

    // $f...this is fantastic.
    switch($f) {
    case 'getRoomLists':
      $this->getRoomLists();
      break;
    case 'getChatLogs':
      $this->getChatLogs();
      break;
    default:
      break;
    }
  }

  /**
   * 部屋一覧の取得
   *
   * @return resource 部屋一覧の生JSON
   *
   */
  public function getRoomLists() {
    // global宣言して部屋一覧配列借りる
    global $roomLists;

    // THIS IS JSON RIGHT?
    header("Content-type: application/json");

    // $roomListsが存在しないなら...?
    if(!json_encode($roomLists)) {
      // 500.
      $this->setResultMessageID('500 Internal Server Error.');
      // エラーの時間じゃヴォケ!
      $this->setResultMessageContent('An error has occurred in internal. Please tell this to administrator.');
      // 配列吐いて終わり。
      echo json_encode($this->resultHash);
      return false;
    }

    // 結果配列と部屋一覧配列をマージするんです。
    $roomLists = array_merge($this->resultHash, $roomLists);
    // "Alpha Beta Charlie Delta Echo."
    echo json_encode($roomLists);
    return true;
  }

  /**
   * チャットログの外部からの取得
   *
   * @return resource JSON生データ
   *
   */
  public function getChatLogs() {

    // 殆どの処理はgetRoomListsと一緒です。

    // global.
    global $roomLists;
    global $dir;

    // Say AGAIN.
    // THIS IS JSON RIGHT?
    header("Content-type: application/json");

    // roomIDが未設定なら終わり。
    if($this->roomID === '') {
      $this->setResultMessageID('400 Bad Request.');
      $this->setResultMessageContent('roomID is NOT set.');

      echo json_encode($this->resultHash);

      return false;
    }

    // roomLists.jsonにroomIDのデータが存在しなかったら終わり。
    if(!$roomLists[$this->roomID]) {
      $this->setResultMessageID('404 Not Found.');
      $this->setResultMessageContent('There is no data for requested roomID.');

      echo json_encode($this->resultHash);

      return false;
    }

    /*
     * ここより謎展開。
     *
     * 1. 部屋の情報取得
     * 2. 部屋の情報内にあるパスワードハッシュと投げられたパスを照合
     * 3. ^が正しいなら、チャットログを持ってくる。
     * 4. チャットログと結果配列をマージ。
     * 5. 出力。
     *
     */
    $roomInfoJSON = json_decode(file_get_contents($dir.$this->roomID.'/roomInfo.json'), true);

    // パスワードチェック。
    if(isCorrectPassword($this->roomPassword, $roomInfoJSON['roomPassword']) === false) {
      $this->setResultMessageID('401 Unauthorized.');
      $this->setResultMessageContent('Illegal password. Please request CORRECT password.');

      echo json_encode($this->resultHash);

      return false;
    }

    // チャットログを取得。
    $chatLogsJSON = json_decode(file_get_contents($dir.$this->roomID.'/chatLogs.json'), true);

    // マージ。
    $chatLogsJSON = array_merge($this->resultHash, $chatLogsJSON);

    // ECHO!
    echo json_encode($chatLogsJSON);

    return true;
  }
}

if(!$_GET['roomID'])       $roomID       = '';
if(!$_GET['roomPassword']) $roomPassword = '';
$instance = new OnsetAPI($roomID, $roomPassword);
$instance->executeFunction($_GET['action']);
