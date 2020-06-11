<?php
session_start();

if (!empty($_SESSION['join'])) {
    header('Location: ../index.php');
    exit;
}

require_once(__DIR__ . '/../tips/ini_error.php');
require_once(__DIR__ . '/../tips/const.php');
require_once(__DIR__ . '/../tips/connect_db.php');
require_once(__DIR__ . '/../tips/func.php');

$headTitle = '会員確認';

if (!empty($_POST)) {
    // DB insert
    $user = $db->prepare('INSERT INTO users SET user_name=:user_name, password=:password, created=now(), modified=now()');
    $user->bindValue(':user_name', $_SESSION['user']['user_name'], PDO::PARAM_STR);
    // XXX 暗号化とハッシュ関数について調べる
    $user->bindValue(':password', sha1($_SESSION['user']['password']), PDO::PARAM_STR);
    $result = $user->execute();

    // 確認 -> ログインページ
    // session リセット
    if ($result) {
        unset($_SESSION['user']);
        $_SESSION['message'] = MESSAGES['doneUserRegister'];
        header('Location: login.php');
        exit;
    }
    $message = MESSAGES['outbreakError'];
}
?>
<!DOCTYPE html>
<html lang="ja">

<?php require_once(__DIR__ . '/../tips/head.php'); ?>

<body>
    <header>
    </header>

    <section class="main">

        <?php if (!empty($message)) : ?>
            <div class="message">
                <p><?php echo h($message); ?></p>
            </div>
        <?php endif; ?>

        <h2 class="title">記載内容を確認してください。</h2>

        <form action="check.php" method="post" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="action" value="submit">
            <p class="user-name">ユーザーネーム : <?php echo h($_SESSION['user']['user_name']); ?></p>
            <p class="password">パスワード : 【表示されません】</p>
            <input type="submit" value="登録" class="btn">
        </form>

        <p class="rewrite"><a href="index.php?action=rewrite">書き直す</a></p>

    </section>
</body>

</html>
