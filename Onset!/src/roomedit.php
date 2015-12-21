<?php
require_once('config.php');
session_start();

if($_POST['rand'] != $_SESSION['onset_rand']){
      echo "無効なアクセス:invalid_access";
      die();
}

$name = isset($_POST['name']) || $_POST['name'] != 0 ? $_POST['name'] : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] != 0 ? $_POST['pass'] : FALSE;
$mode = $_POST['mode'];

if(!$name || !$pass){
      echo "部屋名とパスワードを入力してください";
      die();
}

if(mb_strlen($name) > 30){
      echo "部屋名が長過ぎます";
      die();
}

$dir = "../room/";

switch ($mode) {
      case 'create':

            $name = str_replace("/", "／", $name);
            $name = htmlspecialchars($name, ENT_QUOTES);

            if(file_exists($dir.$name)){
                  echo "同名の部屋がすでに存在しています(ブラウザバックをおねがいします)";
                  die();
            }

            $hash = password_hash($pass, PASSWORD_DEFAULT);
            unset($pass);     //念の為、平文のパスワードを削除
            mkdir($dir.$name);
            touch("{$dir}{$name}/pass.hash");
            touch("{$dir}{$name}/xxlogxx.txt");

            chmod($dir.$name, 0777);
            chmod("{$dir}{$name}/pass.hash", 0666);
            chmod("{$dir}{$name}/xxlogxx.txt", 0666);
            file_put_contents("{$dir}{$name}/pass.hash", $hash);

            header("Location: ../index.php");

            break;

      case 'del':

            if(!file_exists($dir.$name)){
                  echo "部屋が存在しません(ブラウザバックをおねがいします)";
                  die();
            }

            $hash = file_get_contents("{$dir}{$name}/pass.hash");
            if(!password_verify($pass, $hash) && $config['pass'] != $pass){
                  echo "パスワードを間違えています(ブラウザバックをおねがいします)";
                  die();
            }

            unlink("{$dir}{$name}/xxlogxx.txt");
            unlink("{$dir}{$name}/pass.hash");
            unlink("{$dir}{$name}/key.txt");
            rmdir($dir.$name);

            header("Location: ../index.php");

            break;
}
