<?php

require_once 'config.php';

function num_check($num){
    return ctype_digit($num) && $num <= ROOM_LIMIT ? $num : FALSE;
}

function url_replace($text){
      $pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u';
      $rep = '<a href="\1">\1</a>';
      $text = preg_replace($pattern, $rep, $text);
      return $text;
}

    //ダイスコマンドはかなりスパゲッティになってます
function dice($text){
if(preg_match("/[1-9]\d?[dD][1-9]\d{0,2}([-+][1-9]\d?[dD][1-9]\d{0,2}|[-+][1-9]\d{0,2})*/", $text, $match) === 0){
    return FALSE;   //ダイスコマンドにマッチしない場合
}else{  //ダイスコマンドにマッチする場合
        //まずコマンドを[-+]で分ける
        preg_match_all("/([-+]?[1-9]\d?[dD][1-9]\d{0,2}|[-+][1-9]\d?)/", $match[0], $matched, PREG_PATTERN_ORDER);

        foreach ($matched[0] as $key => $value) {  //わけられたコマンドを一つづつ処理していく

             if(stripos($value, "d") !== FALSE){ //nDxコマンドの処理
                  $split = preg_split("/[dD]/", $value);

                  if(strpos($split[0], "-") !== FALSE){   //コマンドがマイナスの処理
                        $roll[$key]["check"] = "minus";
                        $dice_count = substr($split[0], 1);
                  }elseif(strpos($split[0], "+") !== FALSE){  //コマンドがプラスの処理
                        $roll[$key]["check"] = "plus";
                        $dice_count = substr($split[0], 1);
                  }else{  //コマンドが符号なし(ひとつ目のコマンド)の処理
                        $roll[$key]["check"] = "plus";
                        $dice_count = $split[0];
                  }

                  for ($i=0; $i < $dice_count; $i++) {  //実際にダイスを振る処理
                        $roll[$key]["dice"][$i] = mt_rand(1, $split[1]);
                  }

            }else{  //定数の処理
                  if(strpos($value, "-") !== FALSE){  //マイナスの定数の処理
                        $roll[$key]["check"] = "constant/minus";
                        $roll[$key]["dice"] = str_replace("-", "", $value);
                  }elseif(strpos($value, "+") !== FALSE){ //プラスの定数の処理
                        $roll[$key]["check"] = "constant/plus";
                        $roll[$key]["dice"] = str_replace("+", "", $value);
                  }
            }

      }

      foreach ($roll as $value) { //ダイスロールの結果を合計して、結果を文字列と数字で整える
            switch ($value["check"]) {
                  case 'plus':
                        $return["text"] = $return["text"]."+(".implode(",", $value["dice"]).")";
                        $return["num"] = $return["num"] + array_sum($value["dice"]);
                  break;
                  case 'minus':
                        $return["text"] = $return["text"]."-(".implode(",", $value["dice"]).")";
                        $return["num"] = $return["num"] - array_sum($value["dice"]);
                  break;
                  case 'constant/plus':
                        $return["text"] = $return["text"]."+".$value["dice"];
                        $return["num"] = $return["num"] + $value["dice"];
                  break;
                  case 'constant/minus':
                        $return["text"] = $return["text"]."-".$value["dice"];
                        $return["num"] = $return["num"] - $value["dice"];
                  break;
            }
      }

            //最後に、先頭の[-+]を消す
      $return["text"] = ltrim($return["text"], "+");
      $return["text"] = ltrim($return["text"], "-");
      return $return;

      }
}
 ?>
