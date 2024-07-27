<?php
// XSS対策
function eh($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=php-test', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

// パラメータの取得
$branchName = $_GET['branch_name'] ?? '';
$page = (int) ($_GET['page'] ?? 1);

// 1ページあたりのアイテム数
$limit = 5;

// データ取得のためのオフセットを計算
$offset = ($page - 1) * $limit;

// SQLクエリの作成
$sql = 'SELECT * FROM `branch` WHERE 1=1';

// カウントクエリの作成
$countSql = 'SELECT COUNT(*) FROM `branch` WHERE 1=1';
$params = [];

// 検索出力条件
if (!empty($branchName)) {
    $sql .= ' AND `branch_name` LIKE :branch_name';
    $countSql .= ' AND `branch_name` LIKE :branch_name';
    $params[':branch_name'] = '%' . $branchName . '%';
}

// クエリ実行
try {
    // PDOを使用してカウントクエリ実行、該当レコードの総数を取得
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $totalResults = $stmt->fetchColumn();
    // 全ページ数の計算：最低1ページ
    $totalPages = max(ceil($totalResults / $limit), 1);

    // データクエリ取得の実行
    $sql .= ' LIMIT :limit OFFSET :offset';
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $dataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo 'データベースエラーが発生しました。もう一度お試しください。';
    exit;
}

// ページネーション：◯件目ー◯件目を表示
$fromRecord = ($page - 1) * $limit + 1;
$toRecord = min($page * $limit, $totalResults);

// 最大5個までページネーションの数字ボタンの範囲を表示
$range = 2;
if ($page == 1 || $page == $totalPages) {
    $range = 4;
} elseif ($page == 2 || $page == $totalPages - 1) {
    $range = 3;
}

// DBの接続を閉じる
$pdo = null;

// 支店編集
$errors = [];
$user = null;

if (isset($_GET['id'])) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=php-test', 'root', 'root');
        $editSql = 'SELECT * FROM `branch` WHERE id = :id';
        $editStmt = $pdo->prepare($editSql);
        $editStmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $editStmt->execute();
        $user = $editStmt->fetch(PDO::FETCH_OBJ); // 1件のデータを取得

        if (!$user) {
            $errors['id'] = $errorMessage5;
            exit;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
} else {
    $errors['id'] = $errorMessage5;
}
?>
