<?php
namespace Demon\Core;

use Demon\Exception\DataNotFoundException;
use Demon\Exception\MultipleDataException;
use Demon\Exception\DataException;


abstract class DataObject {
    public static $instances = [];
    protected $ID = null;
    protected $_dbIsNew = false;
    protected static $_dbRequests = [];

    public static function get($id, $data = []) {
        $class = get_called_class();
        if(!isset(self::$instances[$class])) {
            self::$instances[$class] = [];
        }
        if(!isset(self::$instances[$class][$id])) {
            /** @var DataObject $obj */
            $obj = new $class;
            $obj->ID = $id;
            $obj->load($data);

            self::$instances[$class][$id] = $obj;
        }

        return self::$instances[$class][$id];
    }

    public static function create() {
        $obj = new static();
        $obj->_dbIsNew = true;

        return $obj;
    }

    public function getID() {
        return $this->ID;
    }

    /**
     * @return string
     * @throws DataException
     */
    public static function dbTableName() {
        throw new DataException('DB table is not defined in ' . get_called_class());
    }

    /**
     * @return array
     * @throws DataException
     */
    public static function dbTableFields() {
        throw new DataException('DB table fields are not defined');
    }

    /**
     * @return \PDOStatement
     * @throws DataException
     */
    public static function dbRequest($request) {
        if (!isset(static::$_dbRequests[$request])) {
            $joinedFields = join(',', static::dbTableFields());
            switch ($request) {
                case 'load-id':
                    static::$_dbRequests['load-id'] = DB::getInstance()->prepare('SELECT ' . $joinedFields . ' FROM `' . static::dbTableName() . '` WHERE `id` = ?');
                    break;
                case 'save':
                    $tmp = $tmp2 = [];
                    foreach(static::dbTableFields() as $key) {
                        $tmp[] = '`' . $key . '` = VALUES(`' . $key .'`)';
                        $tmp2[] = '?';
                    }
                    static::$_dbRequests['save'] = DB::getInstance()->prepare('INSERT INTO `' . static::dbTableName() . '` (' . $joinedFields . ') VALUES (' . join(',', $tmp2) . ') ON DUPLICATE KEY UPDATE ' . join(',', $tmp));
                    break;
                case 'delete':
                    static::$_dbRequests['delete'] = DB::getInstance()->prepare('DELETE FROM `' . static::dbTableName() . '` WHERE `id` = ?');
                    break;
                default:
                    return false;
            }
        }
        return true;
    }

    public function save()
    {
        $data = [];
        foreach(static::dbTableFields() as $key) {
            $data[] = $this->$key;
        }
        DB::query(static::dbRequest('save'), $data);
        if ($this->_dbIsNew) {
            $this->ID = DB::lastInsertId();
            $this->_dbIsNew = false;
        }
    }

    public function delete() {
        unset(self::$instances[get_called_class()][$this->getID()]);
        DB::query(static::dbRequest('delete'), [$this->getID()]);
    }

    public function load($data = []) {
        if(!count($data)) {
            $data = DB::query(static::dbRequest('load-id'), [$this->ID]);
            if(count($data) == 0) {
                throw new DataNotFoundException('Empty dataset on ' . get_called_class() . '::load "ID" = "' . $this->ID . '"');
            }
            $data = $data[0];
        }
        foreach(static::dbTableFields() as $key) {
            $this->$key = $data[$key];
        }
    }


    public static function getOneBy($field, $value) {
        if(!is_array($value)) $value = [$value];
        $res = DB::query(static::dbRequest('load-' . $field), $value);

        $num = count($res);
        if($num == 0) {
            throw new DataNotFoundException('Empty dataset on ' . get_called_class() . '::getOneBy "' . $field . '" = "' . join(' : ', $value) . '"');
        } elseif($num == 1) {
            $res = $res[0];
            return self::get($res['ID'], $res);
        } else {
            throw new MultipleDataException('Multiple results in dataset on ' . get_called_class() . '::getOneBy "' . $field . '" = "' . join(' : ', $value) . '"');
        }
    }

    public static function getAllBy($field, $value) {
        if(!is_array($value)) $value = [$value];
        $res = DB::query(static::dbRequest('load-' . $field), $value);
        $ret = [];

        foreach($res as $line) {
            $ret[] = self::get($line['ID'], $line);
        }

        return $ret;
    }

    abstract public function toArray();

    public static function toArrayAll($array) {
        $ret = [];
        foreach($array as $el) {
            /** @var DataObject $el */
            $ret[$el->getID()] = $el->toArray();
        }

        return $ret;
    }

}