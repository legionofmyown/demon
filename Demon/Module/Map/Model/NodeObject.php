<?php
namespace Demon\Module\Map\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Core\Model\Hero;
use Demon\Exception\DataException;
use Demon\Exception\GameException;
use Demon\Module\Map\MapObject;
use Demon\Module\Map\Node;

class NodeObject extends DataObject {

    protected static $_dbRequests = [];
    protected $hero;
    protected $heroID;
    protected $node;
    protected $object;
    protected $data = '';

    public static function dbTableName()
    {
        return 'node_objects';
    }

    public static function dbTableFields()
    {
        return ['ID', 'heroID', 'node', 'object', 'data'];
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

    public function getHero() {
        if($this->hero === null) {
            $this->hero = Hero::get($this->heroID);
        }
        return $this->hero;
    }

    public function getHeroID() {
        return $this->heroID;
    }

    public function setHero(Hero $hero) {
        $this->hero = $hero;
        $this->heroID = $hero->getID();
    }

    public function getNode() {
        //TODO ? specific field?
        return Node::getClass($this->node);
    }

    public function setNode($node) {
        $node = Node::getClass($node);
        $this->node = $node::getID();
    }

    public function getObject() {
        //TODO ? specific field?
        return MapObject::getClass($this->object);
    }

    public function setObject($object) {
        $object = MapObject::getClass($object);
        $this->object = $object::getID();
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
        $obj = $this->getObject();
        return [
            'ID' => $this->ID,
            'group' => $obj::getGroup(),
            'object' => $this->object,
            'data' => $this->data,
        ];
    }

}