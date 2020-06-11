<?php
// 社員削除
session_start();

if (empty($_SESSION['join'])) {
    header('Location: ../user/login.php');
    exit;
}

require_once(__DIR__ . '/../tips/ini_error.php');
require_once(__DIR__ . '/../tips/const.php');
require_once(__DIR__ . '/../tips/connect_db.php');
require_once(__DIR__ . '/../tips/func.php');

$employeeId = $_GET['employee_id'];
$companyId = $_GET['company_id'];

$isDeleted = isDeleted($db, 'employees', $employeeId);

if ($isDeleted === 'error') {
    $_SESSION['message'] = MESSAGES['outbreakError'];
} elseif ($isDeleted) {
    $_SESSION['message'] = MESSAGES['alreadyDelete'];
} else {
    $deleteFlag = $db->prepare('UPDATE employees SET deleted=now() WHERE id=:id');
    $deleteFlag->bindValue(':id', $employeeId, PDO::PARAM_INT);
    $result = $deleteFlag->execute();

    if ($result) {
        $_SESSION['message'] = MESSAGES['doneDelete'];
    } else {
        $_SESSION['message'] = MESSAGES['outbreakError'];
    }
}

header('Location: ./index.php?company_id=' . $companyId);
exit;
