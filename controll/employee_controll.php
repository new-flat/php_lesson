<?php

require_once 'xss.php';
require_once __DIR__ . '/../class/employee_class.php';

// DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=php-test', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// パラメータの取得
$name = $_GET['name'] ?? '';
$gender = $_GET['gender'] ?? '';
$branch = $_GET['branch'] ?? '';
$page = (int) ($_GET['page'] ?? 1);

// 1ページあたりのアイテム数
$limit = 5;

// データ取得のためのオフセットを計算
$offset = ($page - 1) * $limit;

// SQLクエリの作成
$sql = 'SELECT * FROM `employee` WHERE is_deleted = 0';

// カウントクエリの作成
$countSql = 'SELECT COUNT(*) FROM `employee` WHERE is_deleted = 0';
$params = [];

// 名前での検索条件
if (!empty($name)) {
    $sql .= ' AND (username LIKE :username OR kana LIKE :kana)';
    $countSql .= ' AND (username LIKE :username OR kana LIKE :kana)';
    $params[':username'] = $params[':kana'] = '%' . $name . '%';
}

// 性別での検索条件
if (!empty($gender)) {
    if ($gender === 'null') {
        $sql .= " AND gender IS NULL";
        $countSql .= ' AND gender IS NULL';
    } else {
        $sql .= " AND gender = :gender";
        $countSql .= ' AND gender = :gender';
        $params[':gender'] = (int) $gender;
    }
}
// 部署での検索条件
if (!empty($branch)) {
    $sql .= " AND branch = :branch";
    $countSql .= ' AND branch = :branch';
    $params[':branch'] = (int) $branch;
}

// クエリ実行
try {
    // PDOを使用してカウントクエリ実行、該当レコードの総数を取得
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $totalResults = $stmt->fetchColumn();
    $totalPages = ceil($totalResults / $limit);

    // データクエリ取得の実行
    $sql .= ' LIMIT :limit OFFSET :offset';
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $dataArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Employeeクラスのインスタンスを作成
    $employees = [];
    foreach ($dataArray as $data) {
        $employees[] = new Employee($data);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo $e->getMessage();
    exit;
}

// ページネーション：◯件目ー◯件目を表示
$fromRecord = ($page - 1) * $limit + 1;
if ($page == $totalPages && $totalResults % $limit !== 0) {
    $toRecord = ($page - 1) * $limit + $totalResults % $limit;
} else {
    $toRecord = $page * $limit;
}

// 最大5個までページネーションの数字ボタンの範囲を表示
if ($page == 1 || $page == $totalPages) {
    $range = 4;
} elseif ($page == 2 || $page == $totalPages - 1) {
    $range = 3;
} else {
    $range = 2;
}

// DBの接続を閉じる
$pdo = null;

// 社員編集
$errors = array();
$user = null;
if (isset($_GET["id"])) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=php-test', "root", "root");
        $edit_sql = "SELECT * FROM `employee` WHERE id = :id";
        $edit_stmt = $pdo->prepare($edit_sql);
        $edit_stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $edit_stmt->execute();
        $user = $edit_stmt->fetch(PDO::FETCH_OBJ); // 1件のデータを取得

        if ($user->email === "") {
            $user->email = null;
        }

        if (!$user) {
            $errors['id'] = "URLが間違っています";
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    $errors['id'] = "URLが間違っています";
}



?>
