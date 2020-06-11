<?php
session_start();

if (empty($_SESSION['join'])) {
    header('Location: user/login.php');
    exit;
}

require_once(__DIR__ . '/tips/ini_error.php');
require_once(__DIR__ . '/tips/const.php');
require_once(__DIR__ . '/tips/connect_db.php');
require_once(__DIR__ . '/tips/func.php');

$headTitle = '会社確認';
$message = '';

// 編集にて画像更新しないことを考慮
if (empty($_SESSION['check']['image']['name'])) {
    $image = $_SESSION['check']['image'];
    // if を用いることで $image が配列だった場合を除外
    $imageSrc = 'img/' . $image;
} else {
    $base64 = base64_encode($_SESSION['check']['image']['get_image']);
    $image = date('YmdHis') . $_SESSION['check']['image']['name'];
    $imageSrc = "data:{$_SESSION['check']['image']['tmp_name']};base64,{$base64}";
}

if (!empty($_POST)) {
    if ($_GET['type'] === 'register') {
        $company = $db->prepare('INSERT INTO companies SET company_name=:company_name, representative_name=:representative_name, phone_number=:phone_number, postal_code=:postal_code, prefectures_code=:prefectures_code, address=:address, mail_address=:mail_address, image=:image,created=now(), modified=now()');
    }

    if ($_GET['type'] === 'edit') {
        $company = $db->prepare('UPDATE companies SET company_name=:company_name, representative_name=:representative_name, phone_number=:phone_number, postal_code=:postal_code, prefectures_code=:prefectures_code, address=:address, mail_address=:mail_address, image=:image, modified=now() WHERE id=:id');
        $company->bindValue(':id', $_SESSION['check']['company_id'], PDO::PARAM_INT);
    }

    $company->bindValue(':company_name', $_SESSION['check']['company_name'], PDO::PARAM_STR);
    $company->bindValue(':representative_name', $_SESSION['check']['representative_name'], PDO::PARAM_STR);
    $company->bindValue(':phone_number', $_SESSION['check']['phone_number'], PDO::PARAM_STR);
    $company->bindValue(':postal_code', $_SESSION['check']['postal_code'], PDO::PARAM_STR);
    $company->bindValue(':prefectures_code', $_SESSION['check']['prefectures_code'], PDO::PARAM_INT);
    $company->bindValue(':address', $_SESSION['check']['address'], PDO::PARAM_STR);
    $company->bindValue(':mail_address', $_SESSION['check']['mail_address'], PDO::PARAM_STR);
    $company->bindValue(':image', $image, PDO::PARAM_STR);
    $result = $company->execute();

    if ($result) {
        // 画像アップロード
        if (!empty($_SESSION['check']['image']['get_image'])) {
            file_put_contents('img/' . $image, $_SESSION['check']['image']['get_image'], LOCK_EX);
        }

        $_SESSION['message'] = MESSAGES['doneRegister'];
        if ($_GET['type'] === 'edit') {
            $_SESSION['message'] = MESSAGES['doneEdit'];
        }
        unset($_SESSION['check']);
        header('Location: index.php');
        exit;
    }

    $message = MESSAGES['outbreakError'];
}
?>
<!DOCTYPE html>
<html lang="ja">

<?php require_once(__DIR__ . '/tips/head.php'); ?>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="user/logout.php">ログアウト</a></li>
            </ul>
        </nav>
    </header>

    <section class="main">

        <?php if (!empty($message)) : ?>
            <div class="message">
                <p><?php echo h($message); ?></p>
            </div>
        <?php endif; ?>

        <form action="check.php?type=<?php echo h($_GET['type']); ?>" method="post" enctype="application/x-www-form-urlencoded">
            <!-- $_POST を空にしないため -->
            <input type="hidden" name="action" value="submit">
            <table>
                <?php if (!empty($_SESSION['check']['company_id'])) : ?>
                    <tr>
                        <th>ID</th>
                        <td>
                            <?php echo h($_SESSION['check']['company_id']); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th>会社名</th>
                    <td>
                        <?php echo h($_SESSION['check']['company_name']); ?>
                    </td>
                </tr>
                <tr>
                    <th>代表</th>
                    <td>
                        <?php echo h($_SESSION['check']['representative_name']); ?>
                    </td>
                </tr>
                <tr>
                    <th>Tel</th>
                    <td>
                        <?php echo h($_SESSION['check']['phone_number']); ?>
                    </td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td>
                        <p><span>郵便番号 : </span><?php echo h($_SESSION['check']['postal_code']); ?></p>
                        <p class="pref"><span>都道府県 : </span><?php echo h(PREF_CODES[$_SESSION['check']['prefectures_code']]); ?></p>
                        <p><span>住所 : </span><?php echo h($_SESSION['check']['address']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>Mail</th>
                    <td>
                        <?php echo h($_SESSION['check']['mail_address']); ?>
                    </td>
                </tr>
                <tr>
                    <th>アイコン</th>
                    <td>
                        <img src="<?php echo h($imageSrc); ?>" alt="アイコン画像" class="image">
                    </td>
                </tr>
            </table>
            <input type="submit" value="登録" class="btn">
        </form>

        <?php if ($_GET['type'] === 'register') : ?>
            <p class="rewrite"><a href="register.php?action=rewrite">書き直す</a></p>
        <?php endif; ?>

        <?php if ($_GET['type'] === 'edit') : ?>
            <p class="rewrite"><a href="edit.php?company_id=<?php echo h($_SESSION['check']['company_id']); ?>&action=rewrite">書き直す</a></p>
        <?php endif; ?>


        <p class="back-page"><a href="index.php">会社一覧へ</a></p>

    </section>
</body>
