<?php
require_once(dirname(__FILE__).'/config.php');

/*
 * isIllegalAccess
 */
function isIllegalAccess($rand, $onset_rand) {
	if($rand != $onset_rand) {
		return false;
	}
	return true;
}

/*
 * getRoomlist
 */
function getRoomlist() {
	global $config;
	$dir = $config['roomSavepath'];
	return unserialize(file_get_contents($dir.'roomlist'));
}

/*
 * isExistRoom
 */
function isExistRoom($roomlist, $room) {
	if(isset($roomlist[$room])) {
		return true;
	}
	return false;
}

/*
 * isLongRoomName
 */
function isLongRoomName($name) {
	global $config;
	if(mb_strlen($name) >= $config['maxRoomName']) {
		echo "部屋名が長過ぎます。";
		echo "(".mb_strlen($name)."文字)";
		die();
	}
	return true;
}

/*
 * isLongChat
 */
function isLongChat($text, $name) {
	global $config;
	if(mb_strlen($text) >= $config["maxChatText"]) {
		echo "送信するテキストの文字数が多すぎます(ブラウザバックをお願いします)。";
		echo "最大数:".$config["maxChatText"];
		die();
	}

	if(mb_strlen($name) >= $config["maxChatNick"]) {
		echo "送信する名前の文字数が多すぎます(ブラウザバックをお願いします)。";
		echo "最大数:".$config["maxChatNick"];
		die();
	}
	return true;
}

/*
 * isNULLRoom
 */
function isNULLRoom($room) {
	if($room === NULL) {
		echo "Invalid Access: Room number is null.";
		die();
	}
	return true;
}

/*
 * isSetNamePass
 */
function isSetNameAndPass($name, $pass) {
	if(!$name) {
		echo "部屋名を設定してください。";
		die();
	}

	if(!$pass) {
		echo "パスワードを設定してください。";
		die();
	}
	return true;
}
