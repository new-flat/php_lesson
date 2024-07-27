<?php
// edit_send2.php

require_once 'controll.php';
require_once 'error_message.php';
require_once 'header.php'; // セッション開始とCSRFトークン生成

// トークンが送信されているか確認
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        die('リクエストが無効です');
    }

    // トークンが一致するか確認
    // ⏬なぜhash_equalsを使うのか？
    // 通常の比較演算子(`==`や`===`)を使うと、文字列の長さや部分的な一致によって処理時間が変わることがあり、
    // これを攻撃者が悪用して文字列を推測するリスクがあります。hash_equalsは文字列の全体を一度に比較し、
    // 常に一定の時間をかけるため、タイミング攻撃を防止するのに適しています。
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // トークンが一致する場合
        $id = $_POST['id'] ?? null;
        $editName = $_POST['editName'];
        $editKana = $_POST['editKana'];
        $editGender = $_POST['editGender'];
        $editDate = $_POST['editDate'] ?? null;
        $editEmail = $_POST['editEmail'];
        $editCommute = isset($_POST['editCommute']) ? trim($_POST['editCommute']) : null;
        $editBlood = $_POST['editBlood'];
        $editMarried = $_POST['editMarried'] ?? null;
    } else {
        die("リクエストが無効です");
    }

    $errors = array();

    if ($editDate === '') {
        $editDate = null;
    }

    // 必須項目のチェック
    if (empty($editName)) {
        $errors['editName'] = $error_message1;
    }
    if (empty($editKana)) {
        $errors['editKana'] = $error_message2;
    }
    if (empty($editEmail)) {
        $errors['editEmail'] = $error_message6;
    }
    if (empty($editBlood)) {
        $errors['editBlood'] = $error_message7;
    }

    // 通勤時間のバリデーション（1以上の整数かどうか）
    if (!empty($editCommute)) {
        if (!filter_var($editCommute, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            $errors['editCommute'] = '1以上の整数を入力してください';
        }
    }

    // 編集データをDBに登録
    if (empty($errors)) {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=php-test',
                'root',
                'root',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $updateSql = "UPDATE `php-test` 
                          SET username = :username, kana = :kana, gender = :gender, 
                              birth_date = :birth_date, email = :email, commute_time = :commute_time, 
                              blood_type = :blood_type, married = :married 
                          WHERE id = :id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindValue(':username', $editName);
            $updateStmt->bindValue(':kana', $editKana);
            $updateStmt->bindValue(':gender', $editGender === '' ? null : $editGender, PDO::PARAM_INT);
            $updateStmt->bindValue(':birth_date', $editDate);
            $updateStmt->bindValue(':email', $editEmail);
            $updateStmt->bindValue(':commute_time', $editCommute === '' ? null : $editCommute, PDO::PARAM_INT);
            $updateStmt->bindValue(':blood_type', $editBlood, PDO::PARAM_STR);
            $updateStmt->bindValue(':married', $editMarried === '' ? null : $editMarried, PDO::PARAM_INT);
            $updateStmt->bindValue(':id', $id, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                // 成功したら同じページ（edit_employee.php）に戻り、URLを追加してメッセージを表示
                header("Location: edit_employee.php?id=" . $id . "&success=2");
                // リダイレクト後に不要なコードが実行されないようにする
                exit;
            } else {
                echo '登録に失敗しました';
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $pdo = null;
    } else {
        // エラーがある場合はフォームに戻る
        $errorData = [
            'editName' => $editName,
            'editKana' => $editKana,
            'editGender' => $editGender,
            'editDate' => $editDate,
            'editEmail' => $editEmail,
            'editCommute' => $editCommute,
            'editBlood' => $editBlood,
            'editMarried' => $editMarried
        ];

        $errorQuery = http_build_query(['errors' => json_encode(['messages' => $errors, 'data' => $errorData])]);
        header("Location: edit_employee.php?id=" . $id . "&" . $errorQuery);
        exit;
    }
}