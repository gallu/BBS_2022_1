<?php  // online_comment.php
//
ob_start();
session_start();

//
require_once(__DIR__ . '/../libs/Db.php');
require_once(__DIR__ . '/../libs/Bbs.php');

// 入力データを受け取る
$bbs_id = (string)($_POST["bbs_id"] ?? "");
$comment = (string)($_POST["comment"] ?? "");
$now_page = (int)($_POST["p"] ?? 1);
var_dump($bbs_id, $comment, $now_page);

// validate
$error = [];
// コメントが空ならinvalid
if ("" === $comment) {
    $error[] = "comment empty";
}
// bbs_idが存在しない番号ならinvalid
$bbs = Bbs::find($bbs_id);
if (false === $bbs) {
    $error[] = "bbs_id invalid";
}
//
if ([] !== $error) {
    header("Location: index.php?p=" . rawurlencode($now_page));
    exit;
}

// 情報を追加する
$datum = [
        "bbs_id" => $bbs_id,
        "comment" => $comment,
];
$datum["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
$datum["from_ip"] = $_SERVER["REMOTE_ADDR"];
var_dump($datum);

// コメントをテーブルに書き込む(insert)
try {
    // DBハンドルを取得
    $dbh = Db::getHandle();
    //var_dump($dbh);

    // プリペアドステートメントを作成
    $sql = 'INSERT INTO oneline_comments(bbs_id, comment_body, user_agent, from_ip, created_at)
        VALUES(:bbs_id, :comment, :user_agent, :from_ip, :created_at);';
    $pre = $dbh->prepare($sql);
    // var_dump($pre);

    // 値をバインド
    foreach($datum as $k => $v) {
        $pre->bindValue(":{$k}", $v);
    }
    $pre->bindValue(":created_at", date(DATE_ATOM));

    // SQLを実行
    $r = $pre->execute();
    var_dump($r);
} catch (Throwable $e) {
    header("Location: index.php?p=" . rawurlencode($now_page));
    exit;
}

// flashメッセージに「コメント書いた！」って書いておく

// index.phpに遷移する
header("Location: index.php?p=" . rawurlencode($now_page) . "#id_" . rawurlencode($bbs_id));



