<?php  // write.php
//
ob_start();
session_start();

//
require_once(__DIR__ . '/../libs/Db.php');

// 書き込まれた内容を把握
$params = ["title", "name", "delete_code", "body"];
$datum = [];
foreach($params as $p) {
    $datum[$p] = strval($_POST[$p] ?? "");
}
//var_dump($datum);

// validate(データが「正しい」か、の判定)
$error = [];
if ("" === $datum["body"]) {
    $error["body_must"] = true;
}
//
if ([] !== $error) {
    // 情報を持ちまわる
    $_SESSION["flash"]["error"] = $error;
    $_SESSION["flash"]["datum"] = $datum;
    header("Location: ./index.php");
    exit;
}

//echo "ok";

// 情報を追加する
$datum["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
$datum["from_ip"] = $_SERVER["REMOTE_ADDR"];
//var_dump($datum);

try {
    // 書き込みを投稿(DBにinsert)する
    // DBハンドルを取得
    $dbh = Db::getHandle();
    //var_dump($dbh);

    // プリペアドステートメントを作成
    $sql = 'INSERT INTO bbses(name, title, delete_code, body, user_agent, from_ip, created_at)
        VALUES(:name, :title, :delete_code, :body, :user_agent, :from_ip, :created_at);';
    $pre = $dbh->prepare($sql);
    // var_dump($pre);

    // 値をバインド
    foreach($datum as $k => $v) {
        $pre->bindValue(":{$k}", $v);
    }
    $pre->bindValue(":created_at", date(DATE_ATOM));

    // SQLを実行
    $r = $pre->execute();
    // var_dump($r);
} catch (Throwable $e) {
    $_SESSION["flash"]["error"]["write"] = true;
    $_SESSION["flash"]["datum"] = $datum;
    header("Location: ./index.php");
    exit;
}

// 表示側に飛ばす
$_SESSION["flash"]["message"]["success"] = true;
header("Location: ./index.php");

