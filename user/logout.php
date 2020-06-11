<?php
session_start();

if (empty($_SESSION['join'])) {
    header('Location: index.php');
    exit;
}

require_once(__DIR__ . '/../tips/ini_error.php');
require_once(__DIR__ . '/../tips/const.php');
require_once(__DIR__ . '/../tips/connect_db.php');
require_once(__DIR__ . '/../tips/func.php');

// ログアウト時に session を破棄
$_SESSION = [];

$_SESSION['message'] = MESSAGES['logout'];

header('Location: login.php');
exit;
