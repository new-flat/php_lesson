<?php
require_once 'header.php'; // セッション開始とCSRFトークン生成
require_once __DIR__ . '/../controll/branch_controll.php';
require_once __DIR__ . '/../controll/branch_function.php';
require_once __DIR__ . '/../controll/not_login.php';
require_once __DIR__ . '/../class/branch_class.php';
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>支店編集</title>
    <link rel="stylesheet" href='/employee_site/css/style.css'>
</head>

<body>
    <div id="main" class="wrapper">
        <div id="menu-title">
            <h1 class="title-name">支店編集</h1>
        </div>

        <!-- 成功メッセージ -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
            <p class="success-message">更新しました</p>
        <?php endif ?>

        <!--　URLが間違っていればエラーメッセージ表示 -->
        <?php if (isset($errors['id'])) : ?>
            <p style="margin:0"><?php echo eh($errors['id']); ?></p>
        <?php else : ?>
            <form action="/php_lesson/controll/editB_controll.php" method="POST" class="edit-class">
                <input type="hidden" name="csrf_token" value="<?php echo eh($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="originalId" value="<?php echo eh($branch->id) ?>">
                
                <!-- 支店名 -->
                <div>
                    <div class="label">
                        <label class="insertLabel">支店名</label>
                        <p class="insertMust">必須</p>
                    </div>
                    <input type="text" name="editBranch" value="<?php echo eh($errors['data']['editBranch'] ?? $branch->branch_name ?? ''); ?>">
                    <?php if (!empty($errors['messages']['editBranch'])) : ?>
                        <p><?php echo eh($errors['messages']['editBranch']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- 住所 -->
                <div>
                    <div class="label">
                        <label class="insertLabel">住所</label>
                        <p class="insert-must">必須</p>
                    </div>
                    <div>
                        <select name="editPrefecture">
                            <option value="">都道府県を選択</option>
                            <?php foreach ($branchSelect as $key => $pref) : ?>
                                <option value="<?php echo eh($key); ?>" <?php echo ($branch->prefecture === $key) ? 'selected' : ''; ?>>
                                    <?php echo eh($pref); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['messages']['editPrefecture'])) : ?>
                            <p class="error"><?php echo eh($errors['messages']['editPrefecture']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="text" name="editCity" value="<?php echo eh($errors['data']['editCity'] ?? $branch->city ?? ''); ?>">
                        <?php if (!empty($errors['messages']['editCity'])) : ?>
                            <p class="error"><?php echo eh($errors['messages']['editCity']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="text" name="editAddress" value="<?php echo eh($errors['data']['editAddress'] ?? $branch->address ?? ''); ?>">
                        <?php if (!empty($errors['messages']['editAddress'])) : ?>
                            <p class="error"><?php echo eh($errors['messages']['editAddress']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <input type="text" name="editBuild" value="<?php echo eh($errors['data']['editBuild'] ?? $branch->building ?? ''); ?>">
                        <?php if (!empty($errors['messages']['editBuilding'])) : ?>
                            <p class="error"><?php echo eh($errors['messages']['editBuilding']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 電話番号 -->
                <div>
                    <div class="label">
                        <label class="insertLabel">電話番号</label>
                        <p class="insertMust">必須</p>
                    </div>
                    <input type="text" name="editTel" value="<?php echo eh($errors['data']['editTel'] ?? $branch->tel ?? ''); ?>">
                    <?php if (!empty($errors['messages']['editTel'])) : ?>
                        <p class="error"><?php echo eh($errors['messages']['editTel']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- 並び順（ID） -->
                <div>
                    <div class="label">
                        <label class="insertLabel">並び順</label>
                        <p class="insertMust">必須</p>
                    </div>
                    <input type="text" name="editId" value="<?php echo eh($errors['data']['editId'] ?? $branch->id ?? ''); ?>">
                    <?php if (!empty($errors['messages']['editId'])) : ?>
                        <p class="error"><?php echo eh($errors['messages']['editId']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- 保存ボタン -->
                <input type="submit" class="submit-btn"  name="edit">
            </form>
        <?php endif; ?>
    </div>
</body>

</html>
