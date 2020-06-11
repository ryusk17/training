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

$headTitle = 'ログイン';
$user_name = $_POST['user_name'] ?? '';
$message = $_SESSION['message'] ?? '';

unset($_SESSION['message']);

if (!empty($_POST)) {
    $user = $db->prepare('SELECT COUNT(*) AS cnt FROM users WHERE user_name=:user_name AND password=:password');
    $user->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
    $user->bindValue(':password', sha1($_POST['password']), PDO::PARAM_STR);
    $result = $user->execute();

    if ($result) {
        $user = $user->fetch();
        if ((int) $user['cnt'] === 1) {
            $_SESSION['join'] = $_POST['user_name'];
            $_SESSION['message'] = MESSAGES['doneLogin'];
            header('Location: ../index.php');
            exit;
        }
    }
    $message = MESSAGES['loginError'];
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

        <h2 class="title">会員ログイン</h2>

        <form action="login.php" method="post" enctype="application/x-www-form-urlencoded">
            <p class="user-name">
                <label class="user-name-label">ユーザーネーム</label>
                <input type="text" name="user_name" maxlength="50" value="<?php echo h($user_name); ?>">
            </p>
            <p class="password">
                <label class="password-label">パスワード</label>
                <input type="password" name="password" minlength="8" maxlength="50" value="">
            </p>
            <input type="submit" value="ログイン" class="btn">
        </form>

    </section>

    <script>
        <?php if (!empty($message)) : ?>
            <?php if ($message === MESSAGES['doneUserRegister'] || $message === MESSAGES['logout']) : ?>
                $('.message').css('color', '#41bf55');
            <?php endif; ?>
            setTimeout(function() {
                $('.message').fadeOut();
            }, 5000);
        <?php endif; ?>
    </script>
</body>

</html>
