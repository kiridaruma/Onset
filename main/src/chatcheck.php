<?php
session_start();
require_once 'func.php';



if(filemtime('../log/xxlogxx.txt') > $_SESSION['time']){
    echo file_get_contents('../log/xxlogxx.txt');
    $_SESSION['time'] = time();
}else{
    echo "none";
}

?>
