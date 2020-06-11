<?php
session_start();

if (empty($_SESSION['join'])) {
    header('Location: ../user/login.php');
    exit;
}

require_once(__DIR__ . '/../tips/ini_error.php');
require_once(__DIR__ . '/../tips/const.php');
require_once(__DIR__ . '/../tips/connect_db.php');
require_once(__DIR__ . '/../tips/func.php');

$companyId = $_GET['company_id'];
$employeeId = $_GET['employee_id'];
$headTitle = '社員編集';
$message = '';
$errors = [];
$strArray = EMPLOYEE_STRLEN_PARAMS + COMMON_STRLEN_PARAMS;
$errorMessages = EMPLOYEE_ELEMENTS + COMMON_ELEMENTS;
$blankMessages = EMPLOYEE_BLANK_MESSAGES + COMMON_BLANK_MESSAGES;
$wrongMessages = EMPLOYEE_WRONG_MESSAGES + COMMON_WRONG_MESSAGES;

$isDeleted = isDeleted($db, 'employees', $employeeId);

if ($isDeleted === 'error' || $isDeleted) {
    $_SESSION['message'] = MESSAGES['outbreakError'];
    if ($isDeleted) {
        $_SESSION['message'] = MESSAGES['alreadyDelete'];
    }
    header('Location: index.php?company_id=' . $companyId);
    exit;
}

$oldEmployees = $db->prepare('SELECT * FROM employees WHERE id=:id');
$oldEmployees->bindValue(':id', $employeeId, PDO::PARAM_INT);
$result = $oldEmployees->execute();
if ($result == false) {
    $message = MESSAGES['outbreakError'];
}
$inputValues = $oldEmployees->fetch();

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

    $errorMessages = errorMessage($errors, $errorMessages, $blankMessages, $wrongMessages);

    if (empty($errors)) {
        $_SESSION['check'] = $_POST;
        $_SESSION['check']['company_id'] = $companyId;
        $_SESSION['check']['employee_id'] = $employeeId;
        $_SESSION['check']['phone_number'] = $phone_number;
        $_SESSION['check']['postal_code'] = $postal_code;
        header('Location: check.php?type=edit');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<?php require_once(__DIR__ . '/../tips/head.php'); ?>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="../user/logout.php">ログアウト</a></li>
            </ul>
        </nav>
    </header>


    <section class="main">

        <?php if (!empty($message)) : ?>
            <div class="message">
                <p><?php echo h($message); ?></p>
            </div>
        <?php endif; ?>

        <form action="edit.php?company_id=<?php echo h($companyId); ?>&employee_id=<?php echo h($employeeId); ?>" method="post" enctype="application/x-www-form-urlencoded">
            <table>
                <tr>
                    <th>ID</th>
                    <td><?php echo h($inputValues['id']) ?></td>
                </tr>
                <tr>
                    <th>社員名</th>
                    <td>
                        <input type="text" name="employee_name" maxlength="20" value="<?php echo h($inputValues['employee_name']) ?>">
                        <p class="error"><?php echo h($errorMessages['employee_name']); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>部署</th>
                    <td>
                        <input type="text" name="division_name" maxlength="20" value="<?php echo h($inputValues['division_name']) ?>">
                        <p class="error"><?php echo h($errorMessages['division_name']); ?></p>
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
                        <p><label for="postal" class="address-label">郵便番号</label>
                            <input type="text" name="postal_code" id="postal" maxlength="7" value="<?php echo h($inputValues['postal_code']); ?>"></p>
                        <p class="error"><?php echo h($errorMessages['postal_code']); ?></p>

                        <p class="pref"><label for="pref" class="address-label">都道府県</label>
                            <?php include(__DIR__ . '/../tips/pref.php'); ?></p>
                        <p class="error pref-error"><?php echo h($errorMessages['prefectures_code']); ?></p>

                        <p><label for="address" class="location-label">住所</label>
                            <input type="text" name="address" id="address" maxlength="100" value="<?php echo h($inputValues['address']); ?>"></p>
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
            </table>

            <input type="submit" value="確認" class="btn">
        </form>

        <p class="back-page"><a href="index.php?company_id=<?php echo h($inputValues['company_id']); ?>">社員一覧へ</a></p>

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
