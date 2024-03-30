<?php  // Db.php

class Db {
    public static function getHandle() {
        static $dbh = null;
        if (null === $dbh) {
            try {
                $dbname = "bbs_2022";
                $host = "localhost";
                $charset = "utf8mb4";
                $user = "bbs_2022_user";
                $pass = "bbs_2022_pass";
                //
                $dsn = "mysql:dbname={$dbname};host={$host};charset={$charset}";
                // オプションの追加
                $options = [
                    // 必須
                    PDO::ATTR_EMULATE_PREPARES => false,  // エミュレート無効
                    PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,  // 複文無効
                    // お好みで
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // エラー時に例外を投げる(好み)
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // fetchAllとかで「hash配列」にする(好み)
                ];
                //
                $dbh = new PDO($dsn, $user, $pass, $options);
            } catch(Throwable $e) {
                echo "catch Throwable <br>";
                echo $e->getMessage();
                exit;
            }
        }
        return $dbh;
    }
}
