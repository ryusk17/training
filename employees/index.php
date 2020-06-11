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

$message = $_SESSION['message'] ?? '';
$companyId = $_GET['company_id'];
$headTitle = '社員一覧';
$order = 'DESC';
$divisions = [];
$prefs = [];
$searchEmployeeName = $_GET['employee_name'] ?? '';
$searchDivisionName = $_GET['division_name'] ?? '';
$searchPrefecturesCode = $_GET['prefectures_code'] ?? '';
$searchResult = '';

unset($_SESSION['message']);

// デフォルトのデータ
// 部署、都道府県を配列格納(検索後もすべての部署等が検索できるように)
$defaultEmployeeData = $db->prepare('SELECT * FROM employees WHERE company_id=:id AND deleted IS NULL');
$defaultEmployeeData->bindValue(':id', $companyId, PDO::PARAM_INT);
$defaultResult = $defaultEmployeeData->execute();
if ($defaultResult === false) {
    $message = MESSAGES['outbreakError'];
}
$defaultEmployeeData = $defaultEmployeeData->fetchAll();
$defaultEmployeeNum = count($defaultEmployeeData);
foreach ($defaultEmployeeData as $employee) {
    if (!in_array($employee['division_name'], $divisions)) {
        array_push($divisions, $employee['division_name']);
    }
    if (!in_array($employee['prefectures_code'], $prefs)) {
        array_push($prefs, $employee['prefectures_code']);
    }
}

$sql = employeeSql($searchEmployeeName, $searchDivisionName, $searchPrefecturesCode);
$employees = $db->prepare($sql);
$employees->bindValue(':id', $companyId, PDO::PARAM_INT);
$employees = bindEmployeeSql($employees, $searchEmployeeName, $searchDivisionName, $searchPrefecturesCode);
$result = $employees->execute();
if ($result === false) {
    $message = MESSAGES['outbreakError'];
}
$employees = $employees->fetchAll();

$employeeNum = count($employees);
if ($defaultEmployeeNum !== 0 && $employeeNum === 0) {
    $searchResult = '「 ' . $searchEmployeeName . ' ' . $searchDivisionName . ' ' . PREF_CODES[$searchPrefecturesCode] . ' 」に一致する情報は見つかりませんでした。';
}

// 昇順降順
if (!empty($_GET['order']) && $_GET['order'] === 'DESC') {
    $order = 'ASC';
    $employees = sortArray($employees, 'id');
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

        <h2 class="title"><a href="index.php?company_id=<?php echo h($companyId); ?>">社員一覧</a></h2>

        <a href="./register.php?company_id=<?php echo h($companyId); ?>" class="btn-link">新規登録</a>

        <form action="index.php" method="get" class="form-search-employee">
            <input type="hidden" name="company_id" value="<?php echo h($companyId); ?>">
            <p class="search-employee-element">
                <label for="">社員名</label>
                <input type="search" name="employee_name" class="input-search" value="<?php echo h($searchEmployeeName); ?>" placeholder="社員名">
            </p>
            <p class="search-employee-element">
                <label for="division">部署</label>
                <select name="division_name" id="division" class="select-search">
                    <option value="" selected>部署</option>
                    <?php foreach ($divisions as $division) : ?>
                        <option value="<?php echo h($division); ?>"><?php echo h($division); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p class="search-employee-element">
                <label for="pref">都道府県</label>
                <select name="prefectures_code" id="pref" class="select-search">
                    <option value="" selected>都道府県</option>
                    <?php foreach ($prefs as $pref) : ?>
                        <option value="<?php echo h($pref); ?>"><?php echo h(PREF_CODES[$pref]); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <input type="submit" value="検索" class="btn-search">
        </form>

        <table class="employee-top">
            <thead>
                <tr>
                    <th>
                        <a href="index.php?company_id=<?php echo h($companyId); ?>&order=<?php echo h($order); ?>&employee_name=<?php echo h($searchEmployeeName); ?>&division_name=<?php echo h($searchDivisionName); ?>&prefectures_code=<?php echo h($searchPrefecturesCode); ?>">
                            ID
                        </a>
                    </th>
                    <th>社員名</th>
                    <th>部署</th>
                    <th>Tel</th>
                    <th>住所</th>
                    <th>Mail</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee) : ?>
                    <tr>
                        <td><?php echo h($employee['id']); ?></td>
                        <td><?php echo h($employee['employee_name']); ?></td>
                        <td><?php echo h($employee['division_name']); ?></td>
                        <td><?php echo h($employee['phone_number']); ?></td>
                        <td>
                            <p>〒<?php echo h($employee['postal_code']); ?></p>
                            <p><?php echo h(PREF_CODES[$employee['prefectures_code']]); ?><?php echo h($employee['address']); ?></p>
                        </td>
                        <td><?php echo h($employee['mail_address']); ?></td>
                        <td>
                            <a href="edit.php?company_id=<?php echo h($companyId); ?>&employee_id=<?php echo h($employee['id']); ?>">
                                編集
                            </a>
                        </td>
                        <td>
                            <a href="delete.php?employee_id=<?php echo h($employee['id']); ?>&company_id=<?php echo h($employee['company_id']); ?>">
                                削除
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="not-found"><?php echo h($searchResult) ?></p>

        <p class="back-page"><a href="../index.php">会社一覧へ</a></p>

    </section>

    <script>
        <?php if (!empty($message)) : ?>
            <?php if ($message === MESSAGES['doneDelete'] || $message === MESSAGES['doneEdit'] || $message === MESSAGES['doneRegister']) : ?>
                $('.message').css('color', '#41bf55');
            <?php endif; ?>
            setTimeout(function() {
                $('.message').fadeOut();
            }, 5000);
        <?php endif; ?>

        if (!$('.not-found').text()) {
            $('.not-found').remove();
        }

        $("#division").val('<?php echo h($searchDivisionName); ?>');
        $("#pref").val('<?php echo h($searchPrefecturesCode); ?>');
    </script>

</body>

</html>
