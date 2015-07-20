<?php
namespace Demon\Module\Quest\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Core\Model\Hero;
use Demon\Exception\DataException;
use Demon\Exception\GameException;
use Demon\Module\Map\LinkType;
use Demon\Module\Map\Node;
use Demon\Module\Quest\Task;

class Quest extends DataObject {

    protected static $_dbRequests = [];
    protected $hero;
    protected $heroID;
    protected $task;
    protected $state;
    protected $data = '';

    public static function dbTableName()
    {
        return 'quest';
    }

    public static function dbTableFields()
    {
        return ['ID', 'heroID', 'task', 'state', 'data'];
    }

    public static function dbRequest($request)
    {
        if (!parent::dbRequest($request)) {
            $joinedFields = join(',', static::dbTableFields());
            switch ($request) {
                case 'load-heroID':
                    static::$_dbRequests['load-heroID'] = DB::getInstance()->prepare('SELECT ' . $joinedFields . ' FROM `' . static::dbTableName() . '` WHERE `heroID` = ?');
                    break;
                default:
                    throw new DataException('Unknown request "' . $request . '"');
            }
        }

        return static::$_dbRequests[$request];
    }

    public function setHero(Hero $hero) {
        $this->hero = $hero;
        $this->heroID = $hero->getID();
    }

    public function getTask() {
        //TODO ? specific field?
        return Task::getClass($this->task);
    }

    public function setTask($task) {
        $task = Task::getClass($task);
        $this->task = $task::getID();
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getData($fld = null) {
        $data = json_decode($this->data);
        if($fld !== null) {
            if(!isset($data[$fld])) {
                throw new GameException('Data field [' . $fld . '] not found');
            }
            $data = $data[$fld];
        }
        return $data;
    }

    public function setData($fld, $value = null) {
        $data = $this->getData();
        if(is_array($fld)) {
            foreach($fld as $key => $value) {
                $data[$key] = $value;
            }
        } else {
            $data[$fld] = $value;
        }
        $this->data = json_encode($data);
    }

    public function __toString() {
        //TODO correct naming
        return '(#' . $this->getID() . ')';
    }

    public function toArray() {
        //TODO return data?
        return [
            'task' => $this->task,
            'state' => $this->state,
        ];
    }

    public static function toArrayAll($array) {
        $ret = [];
        foreach($array as $el) {
            $task = $el->getTask();
            $ret[$task::getID()] = $el->toArray();
        }

        return $ret;
    }

}