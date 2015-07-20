<?php
namespace Demon\Core;

use Demon\Exception\DataException;

class DB {
    private static $instance = null;
    public static $config = [];

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new \PDO(self::$config['connection'], self::$config['username'], self::$config['password']);
        }

        return self::$instance;
    }

    public static function query(\PDOStatement $request, $params = []) {
        if(!$request->execute($params)) {
            $info = $request->errorInfo();
            throw new DataException('Error in DB query ' . $info[2]);
        }
        return $request->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function lastInsertId() {
        return self::getInstance()->lastInsertId();
    }
}