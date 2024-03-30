<?php  // delete.php

ob_start();
session_start();

require_once(__DIR__ . '/../libs/Bbs.php');

// 入力削除コードの取得
$delete_code = @strval($_POST["delete_code"] ?? "");
// bbs_idの取得
$bbs_id = @strval($_POST["bbs_id"] ?? "");

var_dump($delete_code, $bbs_id);

// validate
if ( ("" === $delete_code)||("" === $bbs_id) ) {
    header("Location: ./index.php");
    exit;
}

// 「書き込み」データの取得
$bbs = Bbs::find($bbs_id);
// 念のため
if (false === $bbs) {
    header("Location: ./index.php");
    exit;
}
//var_dump($bbs);

// 「入力された削除コード」と「書き込みの削除コード」の比較
if ($delete_code !== $bbs["delete_code"]) {
//var_dump($delete_code, $bbs["delete_code"]);
    $_SESSION["flash"]["error"]["delete_code"] = true;
    header("Location: ./index.php");
    exit;
}

// 書き込みの削除
Bbs::delete($bbs_id);

// 削除できたのでTopPageに返す
$_SESSION["flash"]["message"]["delete"] = true;
header("Location: ./index.php");

