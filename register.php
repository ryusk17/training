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

$headTitle = '会社登録';
$errors = [];
$inputValues = COMPANY_ELEMENTS + COMMON_ELEMENTS;
$strArray = COMPANY_STRLEN_PARAMS + COMMON_STRLEN_PARAMS;
$errorMessages = COMPANY_ELEMENTS + COMMON_ELEMENTS;
$blankMessages = COMPANY_BLANK_MESSAGES + COMMON_BLANK_MESSAGES;
$wrongMessages = COMPANY_WRONG_MESSAGES + COMMON_WRONG_MESSAGES;

if (!empty($_POST)) {
    // 編集内容保持
    $inputValues = updateValue($inputValues, $_POST);

    // エラー処理
    $errors = isBlank($_POST, $errors);
    $errors = validStrlen($_POST, $strArray, $errors);

    $phone_number = convertNumber($_POST['phone_number']);
    $postal_code = convertNumber($_POST['postal_code']);
    if (!empty($phone_number) && !validNumber($phone_number, 11)) {
        $errors['phone_number'] = 'wrong';
    }
    if (!empty($postal_code) && !validNumber($postal_code, 7)) {
        $errors['postal_code'] = 'wrong';
    }

    if (!empty($_POST['mail_address']) && !validMail($_POST['mail_address'])) {
        $errors['mail_address'] = 'wrong';
    }

    if (empty($_FILES['image']['tmp_name'])) {
        $errors['image'] = 'wrong';
    } else {
        $ext = mime_content_type($_FILES['image']['tmp_name']);
        if (!array_search($ext, IMGS, true)) {
            $errors['image'] = 'wrong';
        }
    }

    // 画像を保持できないため
    if (empty($errors['image']) && !empty($errors)) {
        $errorMessages['image'] = SET_IMG;
    }

    // エラーメッセージ生成
    $errorMessages = errorMessage($errors, $errorMessages, $blankMessages, $wrongMessages);

    if (empty($errors)) {
        // 画像保持
        $getImage = file_get_contents($_FILES['image']['tmp_name']);

        $_SESSION['check'] = $_POST;
        $_SESSION['check']['phone_number'] = $phone_number;
        $_SESSION['check']['postal_code'] = $postal_code;
        $_SESSION['check']['image'] = $_FILES['image'];
        $_SESSION['check']['image']['get_image'] = $getImage;
        header('Location: check.php?type=register');
        exit;
    }
}

// 書き直し
if (!empty($_GET['action']) && $_GET['action'] === 'rewrite') {
    $inputValues = $_SESSION['check'];
    $errorMessages['image'] = SET_IMG;
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

        <!-- ファイル送信の際、enctype を変更 -->
        <form action="register.php" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <th>会社名</th>
                    <td>
                        <input type="text" name="company_name" maxlength="50" value="<?php echo h($inputValues['company_name']); ?>">
                        <p class="error"><?php echo h($errorMessages['company_name']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>代表</th>
                    <td>
                        <input type="text" name="representative_name" maxlength="20" value="<?php echo h($inputValues['representative_name']); ?>">
                        <p class="error"><?php echo h($errorMessages['representative_name']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>Tel</th>
                    <td>
                        <input type="text" name="phone_number" maxlength="11" value="<?php echo h($inputValues['phone_number']); ?>">
                        <p class="error"><?php echo h($errorMessages['phone_number']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td class="address">
                        <p><label for="postal" class="address-label">郵便番号</label><input type="text" name="postal_code" id="postal" maxlength="7" value="<?php echo h($inputValues['postal_code']); ?>"></p>
                        <p class="error"><?php echo h($errorMessages['postal_code']); ?></p>

                        <p class="pref"><label for="pref" class="address-label">都道府県</label><?php include(__DIR__ . '/tips/pref.php'); ?></p>
                        <p class="error pref-error"><?php echo h($errorMessages['prefectures_code']); ?></p>

                        <p><label for="address" class="location-label">住所</label><input type="text" name="address" id="address" maxlength="100" value="<?php echo h($inputValues['address']); ?>"></p>
                        <p class="error"><?php echo h($errorMessages['address']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>Mail</th>
                    <td>
                        <input type="text" name="mail_address" maxlength="100" value="<?php echo h($inputValues['mail_address']); ?>">
                        <p class="error"><?php echo h($errorMessages['mail_address']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>アイコン</th>
                    <td>
                        <input type="file" name="image">
                        <p class="error"><?php echo h($errorMessages['image']); ?></p>
                    </td>
                </tr>
            </table>
            <input type="submit" value="確認" class="btn">
        </form>

        <p class="back-page"><a href="index.php">会社一覧へ</a></p>


    </section>

    <script>
        $('#pref').val(<?php echo h($inputValues['prefectures_code']); ?>);

        // エラーがない p タグを削除
        $('.error').each(function(index, element) {
            if (!$(element).text()) {
                $(element).remove();
            }
        });
    </script>

</body>

</html>
