<?php

require_once __DIR__."/../../../db.cfg.php";

class DB {
    private static function initDB()
    {
        $dbConnection = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME."; charset=utf8", DB_USER, DB_PASS);
        $dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbConnection->exec("set names utf8");
        // TODO: вывести ошибку подключения
        return $dbConnection;
    }
    
    private static function parseBoundArrays(string $sql, array $params)
    {
        foreach ($params as $key=>$value) {
            if (is_array($value)) {
                if (preg_match("/[^a-zA-Z0-9_]/", $key)) {
                    var_dump("Invalid name for prepared statement parameter '{$key}'");
                }
                unset($params[$key]);
                $arr_params = [];
                for ($i=0;$i<count($value);$i++) {
                    $arr_params[$key."_arr_unqe08dfa_".$i] = $value[$i];
                }
                if (preg_match_all("/\:{$key}\b/", $sql)>1) {
                    var_dump("Prepared statement parameter '{$key}' used several times in query. Choose different name");
                }
                $params = array_merge($params, $arr_params);
                $sql = preg_replace("/\:{$key}\b/", implode(", ", array_map(function ($item) {
                    return ":" . $item;
                }, array_keys($arr_params))), $sql);
            }
        }
        return [$sql, $params];
    }
    
    //////////////
    
    /**
     * Default request to DB
     *
     * @param string $sql
     * @param array $params
     */
    public static function q(string $sql, array $params)
    {
        $dbConnection = self::initDB();
        list($sql, $params) = self::parseBoundArrays($sql, $params);
        $stmt = $dbConnection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    /**
     * Get one row
     *
     * @param string $sql
     * @param array $params
     */
    public static function q1(string $sql, array $params)
    {
        $dbConnection = self::initDB();
        list($sql, $params) = self::parseBoundArrays($sql, $params);
        $stmt = $dbConnection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    /**
     * Insert row
     *
     * @param string $sql
     * @param array $params
     */
    public static function qi(string $sql, array $params)
    {
        $dbConnection = self::initDB();
        list($sql, $params) = self::parseBoundArrays($sql, $params);
        $stmt = $dbConnection->prepare($sql);
        if ($stmt->execute($params)) {
            return $dbConnection->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Get last incremented ID
     *
     */
    public static function qInsertId()
    {
        $dbConnection = self::initDB();
        return $dbConnection->lastInsertId();
    }
    
    /**
     * Get count of changed row
     *
     * @return void
     */
    public static function qChangedCount() // количество измененных записей
    {
        $sql = "SELECT ROW_COUNT() row_count;";
        $params = [];
        $dbConnection = self::initDB();
        list($sql, $params) = self::parseBoundArrays($sql, $params);
        $stmt = $dbConnection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['row_count'] ?? null;
    }    
}

