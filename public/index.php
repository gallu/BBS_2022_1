<?php  // index.php
// https://dev2.m-fr.net/アカウント名/BBS/
ob_start();
session_start();

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../libs/Bbs.php');

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// twigの準備
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// エラー情報の把握
$error = $_SESSION["flash"]["error"]  ??  [];
$datum = $_SESSION["flash"]["datum"]  ??  [];
$message = $_SESSION["flash"]["message"]  ??  [];
unset($_SESSION["flash"]);

// ページ数の取得
$page_num = @intval($_GET["p"] ?? 1);
if (0 >= $page_num) {
    $page_num = 1;
}
// var_dump($page_num);

// 投稿の読み込み
$bbs_data = Bbs::getList($page_num);
//var_dump($bbs_data);

// 「より古い投稿」の有無判定
if (count($bbs_data) === (Bbs::PER_PAGE + 1)) {
    $older_flg = true;
    array_pop($bbs_data);
} else {
    $older_flg = false;
}

// 新旧のページ数の追加
$older_page = $page_num + 1;
$newer_page = $page_num - 1;

// 出力用変数の準備
$context = [
    "bbs_data" => $bbs_data,
    "error" => $error,
    "datum" => $datum,
    "message" => $message,
    "older_page" => $older_page,
    "newer_page" => $newer_page,
    "older_flg" => $older_flg,
    "now_page" => $page_num,
];

// 出力
echo $twig->render('index.twig', $context);
