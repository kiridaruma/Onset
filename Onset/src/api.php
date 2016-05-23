<?php
require_once('core.php');
require_once('config.php');

/*
 * Onset Web API
 * Author 赤城。
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

  public function setRoomID($roomName) {
    $this->roomID = (string)filter_var($roomName);
  }

  public function setRoomPass($roomPassword) {
    $this->roomPassword = (string)filter_var($roomPassword);
  }

  private function setResultMessageID($code) {
    $this->resultHash['resultMessages']['code'] = $code;
  }

  private function setResultMessageContent($text) {
    $this->resultHash['resultMessages']['message'] = $text;
  }

  public function executeFunction($f) {
    if($f === null || $f === '') {
      $this->setResultMessageID('400 Bad Request.');
      $this->setResultMessageContent('Please set action.');

      echo json_encode($this->resultHash);

      return false;
    }

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

  public function getRoomLists() {
    global $roomLists;

    header("Content-type: application/json");

    if(!json_encode($roomLists)) {
      $this->setResultMessageID('500 Internal Server Error.');
      $this->setResultMessageContent('An error has occurred in internal. Please tell this to administrator.');
      echo json_encode($this->resultHash);
      return false;
    }

    $roomLists = array_merge($this->resultHash, $roomLists);
    echo json_encode($roomLists);
    return true;
  }

  public function getChatLogs() {
    global $roomLists;
    global $dir;

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

    $roomInfoJSON = json_decode(file_get_contents($dir.$this->roomID.'/roomInfo.json'), true);

    if(isCorrectPassword($this->roomPassword, $roomInfoJSON['roomPassword']) === false) {
      $this->setResultMessageID('401 Unauthorized.');
      $this->setResultMessageContent('Illegal password. Please request CORRECT password.');

      echo json_encode($this->resultHash);

      return false;
    }

    $chatLogsJSON = json_decode(file_get_contents($dir.$this->roomID.'/chatLogs.json'), true);
    $chatLogsJSON = array_merge($this->resultHash, $chatLogsJSON);

    echo json_encode($chatLogsJSON);

    return true;
  }
}

if(!$_GET['roomID']) $roomID             = '';
if(!$_GET['roomPassword']) $roomPassword = '';
$instance = new OnsetAPI($roomID, $roomPassword);
$instance->executeFunction($_GET['action']);
