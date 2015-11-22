<?php

class Roll{


            //ダイスロールの結果
            //これがチャットの本文の下に付く
            //ダイスロール系はこの変数に.=してください
      private $result;

            //チャット本文
            //コンストラクトの際に引数で与えられる
            //文字置換系はこの変数を操作してください
      private $text;

            //コンストラクタには処理したい関数を入れてください
      public function __construct($arg_text){
            $this->text = $arg_text;

            $this->url_replace();
            $this->dice();
            $this->CoC_res();
      }

            //置換した文字列を返す
      public function text(){
            return $this->text;
      }

            //ダイスの結果等を返す
      public function result(){
            return $this->result;
      }

            //URL自動変換
      private function url_replace(){
            if(preg_match('/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u', $text) != 0){
                  return FALSE;
            }
            $pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u';
            $rep = '<a href="\1">\1</a>';
            $this->text = preg_replace($pattern, $rep, $this->text);
      }


            //nDxダイスロール
      private function dice(){
            if(preg_match("/[1-9]\d?[dD][1-9]\d{0,2}([-+][1-9]\d?[dD][1-9]\d{0,2}|[-+][1-9]\d{0,2}){0,4}((&lt;=|&gt;=)[1-9]\d{0,2})?/", $this->text, $match) === 0){
                  return FALSE;   //ダイスコマンドにマッチしない場合
            }

                        //大なり､小なりが含まれていた場合
            if(preg_match("/(&lt;=|&gt;=)[1-9]\d{0,2}/", $match[0], $comparison) === 1){
                  $match[0] = str_replace($comparison[0], "", $match[0]);

                  preg_match("/(&lt;=|&gt;=)/", $comparison[0], $comp_state);
                  $decision_value = str_replace($comp_state[0], "", $comparison[0]);
            }
                  //まずコマンドを[-+]で分ける
            preg_match_all("/([-+]?[1-9]\d?[dD][1-9]\d{0,2}|[-+][1-9]\d?)/", $match[0], $matched, PREG_PATTERN_ORDER);

            for($i = 0; $i < count($matched[0]); $i++) {  //わけられたコマンドを一つづつ処理していく

                  if(stripos($matched[0][$i], "d") !== FALSE){    //nDxコマンドの処理
                        $split = preg_split("/[dD]/", $matched[0][$i]);
                        if(strpos($split[0], "-") !== FALSE){     //コマンドがマイナスの処理
                              $roll[$i]["check"] = "minus";
                              $dice_count = substr($split[0], 1);
                        }elseif(strpos($split[0], "+") !== FALSE){      //コマンドがプラスの処理
                              $roll[$i]["check"] = "plus";
                              $dice_count = substr($split[0], 1);
                        }else{      //コマンドが符号なし(ひとつ目のコマンド)の処理
                              $roll[$i]["check"] = "plus";
                              $dice_count = $split[0];
                        }

                        $total_dice += $dice_count;

                        if($total_dice > 20){      //ダイスの個数を20個に制限
                              $this->result .= "nDx:err/ダイスの個数が多すぎます<br>\n";
                              return FALSE;
                        }

                        for ($j=0; $j < $dice_count; $j++) {  //実際にダイスを振る処理
                              $roll[$i]["dice"][$j] = mt_rand(1, $split[1]);
                        }

                  }else{      //定数の処理
                        if(strpos($matched[0][$i], "-") !== FALSE){  //マイナスの定数の処理
                              $roll[$i]["check"] = "constant/minus";
                              $roll[$i]["dice"] = str_replace("-", "", $matched[0][$i]);
                        }elseif(strpos($matched[0][$i], "+") !== FALSE){ //プラスの定数の処理
                              $roll[$i]["check"] = "constant/plus";
                              $roll[$i]["dice"] = str_replace("+", "", $matched[0][$i]);
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

                  //先頭の[-+]を消す
            $return["text"] = ltrim($return["text"], "+");
            $return["text"] = ltrim($return["text"], "-");

            $this->result .= "{$return['text']}→{$return['num']}";

                  //大なり､小なりがあった時の処理
            switch ($comp_state[0]) {
                  case '&lt;=':
                        if($return['num'] <= (int)$decision_value){
                              $res = "成功";
                        }else{
                              $res = "失敗";
                        }
                  break;
                  case '&gt;=':
                        if($return['num'] >= (int)$decision_value){
                              $res = "成功";
                        }else{
                              $res = "失敗";
                        }
                  break;
                  default:
                        $res = FALSE;
            }

            if($res === FALSE){
                  $this->result .= "<br>\n";
            }else{
                  $this->result .= "{$comp_state[0]}{$decision_value}...{$res}<br>\n";
            }

      }

      private function CoC_res(){
            if(preg_match("/res\([1-9]\d?-[1-9]\d?\)/", $this->text, $match) === 0){
                  return 0;
            }

            $hyphen = preg_replace("/(res\(|\))/", "", $match[0]);
            $arr = explode("-", $hyphen);
            $success = 50 + 5*($arr[0] - $arr[1]);

            if($success <= 0){
                  $this->result .= "自動失敗<br>\n";
            }elseif ($success >= 100) {
                  $this->result .= "自動成功<br>\n";
            }else{
                  $rand = mt_rand(1,100);
                  if($rand <= $success){
                        $res = "成功";
                  }else{
                        $res = "失敗";
                  }
                  $this->result .= "目標値{$success}→ダイス結果{$rand}({$res})<br>\n";
            }
      }

}
