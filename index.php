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

$message = $_SESSION['message'] ?? '';
$headTitle = '会社一覧';
$order = 'DESC';
$freeWords = '';
$notFound = '';
$keywords = [];
$i = 0;
$peopleSort = 'DESC';

unset($_SESSION['message']);

if (!empty($_GET['order']) && $_GET['order'] == 'DESC') {
    $order = 'ASC';
}
if (!empty($_GET['people_sort']) && $_GET['people_sort'] == 'DESC') {
    $peopleSort = 'ASC';
}

// 社員数（全社員合計から社員削除フラグ合計を除く）
$sql = 'SELECT c.*, COUNT(e.company_id) - COUNT(e.deleted) AS cnt FROM companies c LEFT JOIN employees e ON c.id = e.company_id WHERE c.deleted IS NULL GROUP BY c.id, c.company_name, c.representative_name, c.phone_number, c.address, c.mail_address, c.image';

// デフォルトデータ数(検索結果0と区別するため)
$defaultCompanies = $db->query($sql);
$defaultCompanyNum = $defaultCompanies->rowCount();

// フリーワード検索
if (!empty($_GET['free_word'])) {
    $freeWords = $_GET['free_word'];
    $keywords = convertKeywords($_GET['free_word']);
    $sql = companySql($keywords);
}

// ID 昇順降順
if (!empty($_GET['order'])) {
    $sql .= ' ORDER BY c.id ' . $_GET['order'];
}

// 社員数昇順降順
if (!empty($_GET['people_sort'])) {
    // 社員数が同値の場合はID順にしている
    $sql .= ' ORDER BY cnt ' . $_GET['people_sort'] . ', c.id ASC';
}

// デフォルト昇順
if (empty($_GET['order']) && empty($_GET['people_sort'])) {
    $sql .= ' ORDER BY c.id ASC';
}

$companies = $db->prepare($sql);
foreach ($keywords as $keyword) {
    $companies->bindValue(":free_word{$i}", '%' .  addcslashes($keyword, '\_%') . '%', PDO::PARAM_STR);
    ++$i;
}

$result = $companies->execute();

if ($result === false) {
    $message = MESSAGES['outbreakError'];
}

$companies = $companies->fetchAll();

if ($defaultCompanyNum !== 0 && count($companies) === 0) {
    $notFound = '「 ' . $freeWords . ' 」に一致する情報は見つかりませんでした。';
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

        <h2 class="title"><a href="index.php">会社一覧</a></h2>

        <a href="register.php" class="btn-link">新規登録</a>

        <form action="index.php" method="get" class="form-search-company">
            <span>会社名・代表名・Tel・住所・Mail</span>
            <input class="input-search input-search-company" type="search" name="free_word" placeholder="キーワードを入力" value="<?php echo h($freeWords); ?>">
            <input type="submit" value="検索" class="btn-search">
        </form>

        <table class="company-top">
            <thead>
                <tr>
                    <th>
                        <a href="index.php?order=<?php echo h($order); ?>&free_word=<?php echo h($freeWords); ?>">
                            ID
                        </a>
                    </th>
                    <th>アイコン</th>
                    <th>会社名</th>
                    <th>代表</th>
                    <th>Tel</th>
                    <th>住所</th>
                    <th>Mail</th>
                    <th>
                        <a href="index.php?people_sort=<?php echo h($peopleSort); ?>&free_word=<?php echo h($freeWords); ?>">
                            社員数
                        </a>
                    </th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company) : ?>
                    <tr>
                        <td><?php echo h($company['id']); ?></td>
                        <td>
                            <img src="img/<?php echo h($company['image']); ?>" alt="<?php echo h($company['company_name']); ?>のアイコン画像" class="image-top">
                        </td>
                        <td>
                            <a href="employees/index.php?company_id=<?php echo h($company['id']); ?>"><?php echo h($company['company_name']); ?>
                            </a>
                        </td>
                        <td><?php echo h($company['representative_name']); ?></td>
                        <td><?php echo h($company['phone_number']); ?></td>
                        <td><?php echo h($company['address']); ?></td>
                        <td><?php echo h($company['mail_address']); ?></td>
                        <td><?php echo h($company['cnt']) . '人'; ?></td>
                        <td>
                            <a href="edit.php?company_id=<?php echo h($company['id']); ?>">
                                編集
                            </a>
                        </td>
                        <td>
                            <a href="delete.php?company_id=<?php echo h($company['id']); ?>">
                                削除
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="not-found"><?php echo h($notFound); ?></p>

    </section>

    <script>
        <?php if (!empty($message)) : ?>
            <?php if ($message === MESSAGES['doneDelete'] || $message === MESSAGES['doneEdit'] || $message === MESSAGES['doneRegister'] || $message === MESSAGES['doneLogin']) : ?>
                $('.message').css('color', '#41bf55');
            <?php endif; ?>
            setTimeout(function() {
                $('.message').fadeOut();
            }, 5000);
        <?php endif; ?>

        if (!$('.not-found').text()) {
            $('.not-found').remove();
        }
    </script>

</body>

</html>
