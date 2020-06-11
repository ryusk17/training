<?php
session_start();

if (!empty($_SESSION['join'])) {
    header('Location: ../index.php');
    exit;
}

require_once(__DIR__ . '/../tips/ini_error.php');
require_once(__DIR__ . '/../tips/connect_db.php');
require_once(__DIR__ . '/../tips/const.php');
require_once(__DIR__ . '/../tips/func.php');

$headTitle = '会員登録';
$inputValues = USER_ELEMENTS;
$errorMessages = USER_ELEMENTS;
$blankMessages = USER_BLANK_MESSAGES;
$wrongMessages = USER_WRONG_MESSAGES;
$errors = [];
$user_name = $_POST['user_name'] ?? '';

if (!empty($_POST)) {
    // エラーチェック
    $errors = isBlank($_POST, $errors);
    $errors = validStrlen($_POST, USER_STRLEN_PARAMS, $errors);
    if (!empty($_POST['password']) && mb_strlen($_POST['password']) < 8) {
        $errors['password'] = 'wrong';
    }

    // エラーメッセージ
    $errorMessages = errorMessage($errors, $errorMessages, $blankMessages, $wrongMessages);

    // ユーザーネーム重複チェック
    $user = $db->prepare('SELECT COUNT(*) AS cnt FROM users WHERE user_name=:user_name');
    $user->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR);
    $result = $user->execute();
    if (!$result) {
        $message = MESSAGES['outbreakError'];
    }
    $user = $user->fetch();
    if ((int) $user['cnt'] !== 0) {
        $errors['duplication'] = true;
        $errorMessages['user_name'] = '＊すでに登録されているユーザーネームです。';
    }

    if (empty($errors)) {
        $_SESSION['user'] = $_POST;
        header('Location: check.php');
        exit;
    }
}
// 書き直し
if (!empty($_GET['action']) && $_GET['action'] === 'rewrite') {
    $user_name = $_SESSION['user']['user_name'];
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

        <h2 class="title">会員登録</h2>

        <form action="index.php" method="post" enctype="application/x-www-form-urlencoded">
            <p class="user-name">
                <label class="user-name-label">ユーザーネーム</label>
                <input type="text" name="user_name" maxlength="50" value="<?php echo h($user_name); ?>">
                <span class="error"><?php echo h($errorMessages['user_name']); ?></span>
            </p>
            <p class="password">
                <label class="password-label">パスワード</label>
                <input type="password" name="password" minlength="8" maxlength="50" value="">
                <span class="error"><?php echo h($errorMessages['password']); ?></span>
            </p>
            <input type="submit" value="確認" class="btn">
        </form>

    </section>
</body>

</html>
