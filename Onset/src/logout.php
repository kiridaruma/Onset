<?php

// セッションスタート
session_start();

// "Search and destroy" - Quote
session_destroy();

// index.phpに転送します
header("Location: ../index.php");
