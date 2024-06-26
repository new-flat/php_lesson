次のSQL文を解釈して、テーブルに挿入するサンプルデータを15個作成してください。
value-1 には日本の俳優の名前をランダムに出力してください。
value-2 はvalue-1の名前をひらがなで出力してください。
value-3 1 or 2 or null のいずれかをランダムで出力してください。
value-4 は yyyy/mm/dd形式でランダムな生年月日を出力してください。


INSERT INTO `php-test`( `username`, `kana`, `gender`, `birth-date`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]') 



index.phpにてデータベースのレコード数が5件を超えた場合、1ページ5件ずつのページング表示されるようにしたい。
現在のコードから修正・追加できるコードを記述してください。また解説もお願いします。

[index.php]
<!-- 初期画面 -->

<?php

require_once("controll.php");
require_once("error_message.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="header" class="wrapper">
       <h1>社員一覧</h1>
    </div>
    
    <div id="main" class="wrapper">
       <div class="form">
            <form class="formu" action="search.php" method="get">
                <div class="form-list">
                    <p>氏名</p>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name_value, ENT_QUOTES, 'UTF-8'); ?>">
                </div>  
                <div class="form-list">  
                    <p>性別</p>
                    <select name="gender">
                        <option value="" selected>全て</option>
                        <option value="null" <?php echo ($gender === '') ? 'selected' : ''; ?>>不明</option>
                        <option value="1" <?php echo ($gender === '1') ? 'selected' : ''; ?>>男</option>
                        <option value="2" <?php echo ($gender === '2') ? 'selected' : ''; ?>>女</option>
                    </select>
                </div>  
              
                <input class="search" type="submit" value="検索" name="search">
                      
            </form>
        </div>
        <div class="list">
          <?php  if(empty($data_array)): ?>
                <p class = "error_search"><?php echo $error_message3; ?></p>
          <?php else: ?>    
            <table class="table">
                <thead>
                    <tr>
                        <th class="table-title">氏名</th>
                        <th class="table-title">かな</th>
                        <th class="table-title">性別</th>
                        <th class="table-title">年齢</th>
                        <th class="table-title">生年月日</th>
                        <th class="table-title"></th>
                    </tr>
                </thead>

                <?php foreach($index_stmt as $data): ?>
                <tbody>
                    <tr>
                        <th><?php echo htmlspecialchars($data["username"], ENT_QUOTES, 'UTF-8'); ?></th>
                        <td data-label="かな"><?php echo htmlspecialchars($data['kana'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label="性別">
                            <?php 
                                if($data["gender"] === 1){
                                    echo "男";
                                } elseif($data["gender"] === 2) {
                                    echo "女";
                                } else {
                                    echo "不明";
                                }
                            ?>
                        </td>
                        <td data-label="年齢">
                            <?php 
                            $birthDate = str_replace("-", "", $data["birth_date"]);
                            $age = floor((date('Ymd') - $birthDate) / 10000);
                            echo $age;
                            ?>
                        </td>
                        <td data-label="生年月日"><?php echo htmlspecialchars($data['birth_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label=""><a class="edit-btn" href="edit.php?id=<?php echo($data['id']) ?>">編集</a></td>

                    </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
          <?php endif; ?>  
        </div>

        <div class="pageNation" >
            <!-- ◯件中◯-◯件目を表示 -->
            <p class=><?php echo $total_results; ?>件中<?php echo $from_record ?>-<?php echo $to_record ?>件目を表示</p>

            <!-- 前のページボタン -->
            <?php if($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET,['page' => $page - 1])); ?>"><<</a>
            <?php else: ?>    
                <span class="disabled"><<</span>
            <?php endif; ?>
            
            <!-- ページ番号リンク -->
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <?php if($i == $page): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>    
                    <a href="?<?php echo http_build_query(array_merge($_GET,['page' => $i])); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?> 
            
            <!-- 次のページボタン -->
            <?php if($page < $total_pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">>></a>  
            <?php else: ?>      
                <span class="disabled">>></span>
            <?php endif; ?>    
        </div>
    </div>
</body>
</html>


[controll.php]
<?php

$data_array = array();

// DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=php-test', "root", "root");
} catch(PDOException $e) {
    echo $e->getMessage();
}

// SQLクエリの作成
$sql = "SELECT `id`, `username`, `kana`, `gender`, `birth_date` FROM `php-test` WHERE 1 " ;
// カウントクエリの作成
$count_sql = "SELECT COUNT(*) FROM `php-test` WHERE 1 ";
$params = array();



$name = isset($_GET["name"]) ? $_GET["name"] : '';
$gender = isset($_GET["gender"]) ? $_GET["gender"] : 'null';
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;

//1ページあたりのアイテム数 
$limit = 5;
// データ取得のためのオフセットを計算
$offset = ($page - 1) * $limit;






// 全ページ数の計算：総件数20件、1ページあたり5件表示する場合、全ページはceil(20 / 5) = 4

$index_sql = "SELECT * FROM `php-test`"; 

$index_stmt = $pdo->query($index_sql);


$index_sql .= " LIMIT :limit OFFSET :offset";
$index_stmt = $pdo->prepare($index_sql);
foreach ($params as $key => $value) {
    $index_stmt->bindValue($key, $value);
}
    $index_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $index_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $index_stmt->execute();
    
 
    

// 検索出力条件
if (!empty($name)) {
    $sql .= " AND (`username` LIKE :name OR `kana` LIKE :kana)";
    $count_sql .= " AND (`username` LIKE :name OR `kana` LIKE :kana)";
    $params[':name'] = '%' . $name . '%';
    $params[':kana'] = '%' . $name . '%';
    $name_value = $_GET['name'];
} else {
    $name_value = '';
}


if($gender === 'null') {
    $sql .= " AND `gender` IS NULL";
    $count_sql .= " AND `gender` IS NULL";
}elseif($gender !== '') {
    $sql .= " AND `gender` = :gender";
    $count_sql .= " AND `gender` = :gender";
    $params[':gender'] = (int)$gender;
} 




// クエリ実行
try {
    // PDOを使用してカウントクエリ実行、該当レコードの総数を取得
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_results = $stmt->fetchColumn();
    // 全ページ数の計算：総件数20件、1ページあたり5件表示する場合、全ページはceil(20 / 5) = 4
    $total_pages = ceil($total_results / $limit);

    // データクエリ取得の実行
    // LIMITとOFFSETを使って特定の範囲のデータだけを取得する
    $sql .= " LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data_array = $stmt->fetchAll();
} catch (PDOException $e) {
    echo $e->getMessage();
}


// ページネーション：◯件目ー◯件目を表示
$from_record = ($page - 1) * $limit + 1;
if($page == $total_pages && $total_results % $page !== 0) {
    $to_record = ($page - 1) * $limit + $total_results % $limit;
} else {
    $to_record = $page * $limit;
}

// 最大5個までページネーションの数字ボタンの範囲を表示
if ($page == 1 || $page == $total_pages) {
    $range = 4;
} elseif ($page == 2 || $page == $total_pages - 1) {
    $range = 3;
} else {
    $range = 2;
}






// DBの接続を閉じる
$pdo = null

?>







<!-- ここからページネーション設定 -->
<p class="from_to"><?php echo $search_count ?>件中 <?php echo $from_record; ?> - <?php echo $to_record; ?> 件目を表示</p>

<div class="pagenation">
    <!-- 戻るボタン -->
    <?php if ($page >= 2) : ?>
        <a href="?page=<?php echo (htmlspecialchars($page, ENT_QUOTES) - 1); ?>" class="page_feed">&laquo;</a>

    <?php else : ?>
        <span class="first_last_page">&laquo;</span>

    <?php endif; ?>

    <!-- ページ選択 -->
    <?php for ($i = 1; $i <= $max_page; $i++) : ?>
        <?php if ($i >= $page - $range && $i <= $page + $range) : ?>
            <?php if ($i == $page) : ?>
                <span class="now_page_number"><?php echo $i; ?></span>
            <?php else : ?>
                <a href="?page=<?php echo $i; ?>" class="page_number"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- 進むボタン -->
    <?php if ($page < $max_page) : ?>
        <a href="?page=<?php echo (htmlspecialchars($page, ENT_QUOTES) + 1); ?>" class="page_feed">&raquo;</a>

    <?php else : ?>
        <span class="first_last_page">&raquo;</span>

    <?php endif; ?>

</div>