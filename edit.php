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

$companyId = $_GET['company_id'];
$headTitle = '会社編集';
$message = '';
$errors = [];
$strArray = COMPANY_STRLEN_PARAMS + COMMON_STRLEN_PARAMS;
$errorMessages = COMPANY_ELEMENTS + COMMON_ELEMENTS;
$blankMessages = COMPANY_BLANK_MESSAGES + COMMON_BLANK_MESSAGES;
$wrongMessages = COMPANY_WRONG_MESSAGES + COMMON_WRONG_MESSAGES;

$isDeleted = isDeleted($db, 'companies', $companyId);

if ($isDeleted === 'error' || $isDeleted) {
    $_SESSION['message'] = MESSAGES['outbreakError'];
    if ($isDeleted) {
        $_SESSION['message'] = MESSAGES['alreadyDelete'];
    }
    header('Location: index.php');
    exit;
}

$oldCompanies = $db->prepare('SELECT * FROM companies WHERE id=:id');
$oldCompanies->bindValue(':id', $companyId, PDO::PARAM_INT);
$result = $oldCompanies->execute();
if ($result === false) {
    $message = MESSAGES['outbreakError'];
}
$inputValues = $oldCompanies->fetch();
// 書き直しの際にエラーになるため
$oldImage = $inputValues['image'];

if (!empty($_POST)) {
    $inputValues = updateValue($inputValues, $_POST);

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

    // 画像
    if (!empty($_FILES['image']['tmp_name'])) {
        $ext = mime_content_type($_FILES['image']['tmp_name']);
        if (!array_search($ext, IMGS, true)) {
            $errors['image'] = 'wrong';
        }
    }

    // 画像更新かつその他でエラーの場合
    if (empty($errors['image']) && !empty($errors) && !empty($_FILES['image']['tmp_name'])) {
        $errorMessages['image'] = SET_IMG;
    }

    $errorMessages = errorMessage($errors, $errorMessages, $blankMessages, $wrongMessages);

    if (empty($errors)) {
        $_SESSION['check'] = $_POST;
        $_SESSION['check']['company_id'] = $inputValues['id'];
        $_SESSION['check']['phone_number'] = $phone_number;
        $_SESSION['check']['postal_code'] = $postal_code;
        $_SESSION['check']['image'] = $oldImage;

        // 画像を変更する場合
        if (!empty($_FILES['image']['tmp_name'])) {
            $getImage = file_get_contents($_FILES['image']['tmp_name']);
            $_SESSION['check']['image'] = $_FILES['image'];
            $_SESSION['check']['image']['get_image'] = $getImage;
        }
        header('Location: check.php?type=edit');
        exit;
    }
}

// 書き直し
if (!empty($_GET['action']) && $_GET['action'] === 'rewrite') {
    $inputValues = $_SESSION['check'];
    if (!empty($inputValues['image']['get_image'])) {
        $errorMessages['image'] = SET_IMG;
    }
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

        <form action="edit.php?company_id=<?php echo h($companyId); ?>" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <th>ID</th>
                    <td><?php echo h($companyId) ?></td>
                </tr>

                <tr>
                    <th>会社名</th>
                    <td>
                        <input type="text" name="company_name" maxlength="50" value="<?php echo h($inputValues['company_name']) ?>">
                        <p class="error"><?php echo h($errorMessages['company_name']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>代表</th>
                    <td>
                        <input type="text" name="representative_name" maxlength="20" value="<?php echo h($inputValues['representative_name']) ?>">
                        <p class="error"><?php echo h($errorMessages['representative_name']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>Tel</th>
                    <td>
                        <input type="text" name="phone_number" maxlength="11" value="<?php echo h($inputValues['phone_number']) ?>">
                        <p class="error"><?php echo h($errorMessages['phone_number']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td class="address">
                        <p><label for="postal" class="address-label">郵便番号</label><input type="text" name="postal_code" id="postal" maxlength="7" value="<?php echo h($inputValues['postal_code']) ?>"></p>
                        <p class="error"><?php echo h($errorMessages['postal_code']); ?></p>

                        <p class="pref"><label for="pref" class="address-label">都道府県</label>
                            <!-- 下記scriptで選択済み都道府県表示 -->
                            <?php include(__DIR__ . '/tips/pref.php') ?></p>
                        <p class="error pref-error"><?php echo h($errorMessages['prefectures_code']); ?></p>

                        <p><label for="address" class="location-label">住所</label><input type="text" name="address" id="address" maxlength="100" value="<?php echo h($inputValues['address']) ?>"></p>
                        <p class="error"><?php echo h($errorMessages['address']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>Mail</th>
                    <td>
                        <input type="text" name="mail_address" maxlength="100" value="<?php echo h($inputValues['mail_address']) ?>">
                        <p class="error"><?php echo h($errorMessages['mail_address']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>アイコン</th>
                    <td>
                        <p>現在のアイコン</p>
                        <img src="img/<?php echo h($oldImage); ?>" alt="<?php echo h($inputValues['company_name']); ?>のアイコン画像" class="image">

                        <p class="change-img-message">変更する場合は画像を選択してください。</p>
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

        $('.error').each(function(index, element) {
            if (!$(element).text()) {
                $(element).remove();
            }
        });
    </script>
</body>

</html>
