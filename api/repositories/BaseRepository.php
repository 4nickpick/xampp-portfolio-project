<?php

class BaseRepository {

    private static $db;
    private $connection;

    public function __construct() {
        $this->connection = new MySQLi(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);
    }

    function __destruct() {
        $this->connection->close();
    }

    public static function getConnection() {
        if (self::$db == null) {
            self::$db = new Database();
        }
        return self::$db->connection;
    }

    public function select($sql = "" , $params = [])
    {
        try {
            $stmt = $this->executeStatement($sql, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);				
            $stmt->close();
            return $result;
        } catch(Exception $e) {
            throw New Exception($e->getMessage());
        }
        return false;
    }

    public function executeStatement($sql  = "", $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            if($params) {
                if(is_array($params[1])) {
                    $stmt->bind_param($params[0], ...$params[1]);
                }
                else {
                    $stmt->bind_param($params[0], $params[1]);
                }
            }
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception($e->getMessage());
        }	
    }
}