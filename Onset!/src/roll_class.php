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
            if(preg_match("/[1-9]\d?[dD][1-9]\d{0,2}([-+][1-9]\d?[dD][1-9]\d{0,2}|[-+][1-9]\d{0,2})*/", $this->text, $match) === 0){
                  return FALSE;   //ダイスコマンドにマッチしない場合
            }else{      //ダイスコマンドにマッチする場合
                  //まずコマンドを[-+]で分ける
                  preg_match_all("/([-+]?[1-9]\d?[dD][1-9]\d{0,2}|[-+][1-9]\d?)/", $match[0], $matched, PREG_PATTERN_ORDER);

                  foreach ($matched[0] as $key => $value) {  //わけられたコマンドを一つづつ処理していく
                         if(stripos($value, "d") !== FALSE){ //nDxコマンドの処理
                               $split = preg_split("/[dD]/", $value);
                               if(strpos($split[0], "-") !== FALSE){   //コマンドがマイナスの処
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

                  //先頭の[-+]を消す
            $return["text"] = ltrim($return["text"], "+");
            $return["text"] = ltrim($return["text"], "-");
            $this->result .= "{$return['text']}→{$return['num']}\n";

            }
      }

}
