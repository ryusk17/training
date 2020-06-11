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

$headTitle = '社員確認';
$message = '';
$companyId = $_SESSION['check']['company_id'];

if (!empty($_POST)) {
    if ($_GET['type'] === 'register') {
        $employee = $db->prepare('INSERT INTO employees SET company_id=:company_id, employee_name=:employee_name, division_name=:division_name,phone_number=:phone_number, postal_code=:postal_code, prefectures_code=:prefectures_code, address=:address, mail_address=:mail_address, created=now(), modified=now()');
        $employee->bindValue(':company_id', $_SESSION['check']['company_id'], PDO::PARAM_STR);
    }

    if ($_GET['type'] === 'edit') {
        $employee = $db->prepare('UPDATE employees SET  employee_name=:employee_name, division_name=:division_name,phone_number=:phone_number, postal_code=:postal_code, prefectures_code=:prefectures_code, address=:address, mail_address=:mail_address, modified=now() WHERE id=:id');
        $employee->bindValue(':id', $_SESSION['check']['employee_id'], PDO::PARAM_STR);
    }

    $employee->bindValue(':employee_name', $_SESSION['check']['employee_name'], PDO::PARAM_STR);
    $employee->bindValue(':division_name', $_SESSION['check']['division_name'], PDO::PARAM_STR);
    $employee->bindValue(':phone_number', $_SESSION['check']['phone_number'], PDO::PARAM_STR);
    $employee->bindValue(':postal_code', $_SESSION['check']['postal_code'], PDO::PARAM_STR);
    $employee->bindValue(':prefectures_code', $_SESSION['check']['prefectures_code'], PDO::PARAM_INT);
    $employee->bindValue(':address', $_SESSION['check']['address'], PDO::PARAM_STR);
    $employee->bindValue(':mail_address', $_SESSION['check']['mail_address'], PDO::PARAM_STR);
    $result = $employee->execute();

    if ($result) {
        $_SESSION['message'] = MESSAGES['doneRegister'];
        if ($_GET['type'] === 'edit') {
            $_SESSION['message'] = MESSAGES['doneEdit'];
        }
        unset($_SESSION['check']);
        header('Location: index.php?company_id=' . $companyId);
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

        <form action="check.php?type=<?php echo h($_GET['type']); ?>" method="post" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="action" value="submit">
            <table>
                <?php if ($_GET['type'] === 'edit') : ?>
                    <tr>
                        <th>ID</th>
                        <td>
                            <?php echo h($_SESSION['check']['company_id']); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th>社員名</th>
                    <td>
                        <?php echo h($_SESSION['check']['employee_name']); ?>
                    </td>
                </tr>
                <tr>
                    <th>部署名</th>
                    <td>
                        <?php echo h($_SESSION['check']['division_name']); ?>
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
            </table>
            <button type="submit" class="btn">登録</button>
        </form>

        <?php if ($_GET['type'] === 'register') : ?>
            <p class="rewrite"><a href="register.php?action=rewrite&company_id=<?php echo h($_SESSION['check']['company_id']); ?>">書き直す</a></p>
        <?php endif; ?>

        <?php if ($_GET['type'] === 'edit') : ?>
            <p class="rewrite"><a href="edit.php?company_id=<?php echo h($_SESSION['check']['company_id']); ?>&employee_id=<?php echo h($_SESSION['check']['employee_id']); ?>&action=rewrite">書き直す</a></p>
        <?php endif; ?>

        <p class="back-page"><a href="index.php?company_id=<?php echo h($_SESSION['check']['company_id']); ?>">社員一覧へ</a></p>

    </section>
</body>
