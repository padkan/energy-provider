<?php
namespace Core;

use PDO;
use App\Config;

/**
 * [Class base model]
 */
abstract class BaseModel {
    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;
 
        if ($db === null) {
            $host = Config::DB_HOST;
            $dbname = Config::DB_NAME;
            $username = Config::DB_USER;
            $password = Config::DB_PASSWORD;
            $port = Config::DB_PORT;

            $dsn = "mysql:dbname=$dbname;host=$host;port=$port;charset=utf8";
            $db = new PDO($dsn , $username, $password);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
        return $db;
    }
}