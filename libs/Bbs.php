<?php  // Bbs.php

//
require_once(__DIR__ . '/Db.php');

class Bbs {
    // 1ページあたりの表示数
    public const PER_PAGE = 5;
    
    /**
     * 投稿一覧の取得
     */
    public static function getList($page_num) {
        // DBハンドルを取得
        $dbh = Db::getHandle();
        
        // プリペアドステートメントを作成
        $sql = 'SELECT * FROM bbses ORDER BY bbs_id DESC LIMIT :limit OFFSET :offset ;';
        $pre = $dbh->prepare($sql);
        
        // 値をバインド
        $pre->bindValue(":limit", self::PER_PAGE + 1, PDO::PARAM_INT);
        $pre->bindValue(":offset", ($page_num - 1) * self::PER_PAGE, PDO::PARAM_INT);
        
        // SQLを実行
        $r = $pre->execute();

        // データを取得
        $data = $pre->fetchAll();

        // データに「一行コメント」を付け足す
        foreach($data as $k => $v) {
            // 対象bbs_idの一行コメント(群)を取得する
            $sql = 'SELECT * FROM oneline_comments WHERE bbs_id = :bbs_id ORDER BY created_at DESC;';
            $pre = $dbh->prepare($sql);
            // バインド
            $pre->bindValue(":bbs_id", $v["bbs_id"]);
            // SQL実行
            $r = $pre->execute();
//var_dump($r);

            // 一行コメント(群)を追加
            $data[$k]["oneline_comment"] = $pre->fetchAll();
        }
//var_dump($data);

        return $data;
    }

    /**
     * 1つの書き込みデータを取得する
     */
    public static function find($bbs_id) {
        // DBハンドルを取得
        $dbh = Db::getHandle();
        
        // プリペアドステートメントを作成
        $sql = 'SELECT * FROM bbses WHERE bbs_id = :bbs_id;';
        $pre = $dbh->prepare($sql);
        
        // 値をバインド
        $pre->bindValue(":bbs_id", $bbs_id);
        
        // SQLを実行
        $r = $pre->execute();

        // データを取得
        $datum = $pre->fetch();
        return $datum;
    }

    /**
     * 1つの書き込みデータを削除する
     */
    public static function delete($bbs_id) {
        // DBハンドルを取得
        $dbh = Db::getHandle();
        try {
            // トランザクション
            $dbh->beginTransaction();

            // 一行コメントの削除
            // プリペアドステートメントを作成
            $sql = 'DELETE FROM oneline_comments WHERE bbs_id = :bbs_id;';
            $pre = $dbh->prepare($sql);
            // 値をバインド
            $pre->bindValue(":bbs_id", $bbs_id);
            // SQLを実行
            $r = $pre->execute();

            // BBS本体の削除
            // プリペアドステートメントを作成
            $sql = 'DELETE FROM bbses WHERE bbs_id = :bbs_id;';
            $pre = $dbh->prepare($sql);
            // 値をバインド
            $pre->bindValue(":bbs_id", $bbs_id);
            // SQLを実行
            $r = $pre->execute();

            // トランザクション終了
            $dbh->commit();
        } catch(Throwable $e) {
var_dump($e->getMessage()); exit;
            // トランザクション異常終了
            $dbh->rollback();
            return false;
        }

        return true;
    }
}




























