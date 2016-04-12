<?php

function CoC_res($text){
    if(preg_match("/res\([1-9]\d?-[1-9]\d?\)/", $text, $match) === 0){
        return 0;
    }

    $hyphen = preg_replace("/(res\(|\))/", "", $match[0]);
    $arr = explode("-", $hyphen);
    $success = 50 + 5*($arr[0] - $arr[1]);

    if($success <= 0){
        $result .= "自動失敗<br>\n";
    }elseif ($success >= 100) {
        $result .= "自動成功<br>\n";
    }else{
        $rand = mt_rand(1,100);
        if($rand <= $success){
            $res = "成功";
        }else{
            $res = "失敗";
        }
        $result .= "目標値{$success}→ダイス結果{$rand}({$res})<br>\n";
    }
    return $result;
}