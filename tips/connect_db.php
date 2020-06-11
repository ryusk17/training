<?php
try {
    $db = new PDO('mysql:dbname=データベース名;host=ホスト名;charset=utf8', 'ユーザー名', 'パスワード');
} catch (PDOException $e) {
    echo $e->getMessage();
}
