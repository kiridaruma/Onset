<?php

$str = "Objeeeeeeeeeect.";

$obj = array(
	"key" => $str
);

$obj = json_encode($obj);

echo $obj;
