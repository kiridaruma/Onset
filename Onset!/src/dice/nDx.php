<?php

function nDx($text){
    if(preg_match("/[1-9]\d?[dD][1-9]\d{0,2}([-+][1-9]\d?[dD][1-9]\d{0,2}|[-+][1-9]\d{0,2}){0,4}((&lt;=|&gt;=)[1-9]\d{0,2})?/", $text, $match) === 0){
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
                $result .= "nDx:err/ダイスの個数が多すぎます<br>\n";
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

    $result .= "{$return['text']}→{$return['num']}";

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
        $result .= "<br>\n";
    }else{
        $result .= "{$comp_state[0]}{$decision_value}...{$res}<br>\n";
    }
    return $result;
}