<?php
namespace Demon\Core\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Exception\DataException;
use Demon\Module\Army\Model\Squad;
use Demon\Module\Inventory\Item;
use Demon\Module\Inventory\Model\Inventory;
use Demon\Module\Map\Model\Link;
use Demon\Module\Map\Model\NodeObject;
use Demon\Module\Map\Node;
use Demon\Module\Module;
use Demon\Module\Quest\Model\Quest;

class Hero extends DataObject {

    protected static $_dbRequests = [];
    protected $user;
    protected $userID;
    protected $name = '';
    protected $modules = '';
    protected $links = null;
    protected $quests = null;
    protected $inventory = null;
    protected $squads = null;
    protected $nodeObjects = null;
    protected $lastNode = null;

    public static function dbTableName()
    {
        return 'hero';
    }

    public static function dbTableFields()
    {
        return ['ID', 'userID', 'name', 'modules', 'lastNode'];
    }

    public static function dbRequest($request)
    {
        if (!parent::dbRequest($request)) {
            $joinedFields = join(',', static::dbTableFields());
            switch ($request) {
                case 'load-userID':
                    static::$_dbRequests['load-userID'] = DB::getInstance()->prepare('SELECT ' . $joinedFields . ' FROM `' . static::dbTableName() . '` WHERE `userID` = ?');
                    break;
                default:
                    throw new DataException('Unknown request "' . $request . '"');
            }
        }

        return static::$_dbRequests[$request];
    }

    public function setUser(User $user) {
        $this->user = $user;
        $this->userID = $user->getID();
    }

    public function getUser() {
        return $this->user;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getModules() {
        if($this->modules === '') {
            $ret = [];
        } else {
            $ret = explode(':', $this->modules);
        }
        return $ret;
    }

    public function getLastNode() {
        return Node::getClass($this->lastNode);
    }

    public function setLastNode($node) {
        $node = Node::getClass($node);
        $this->lastNode = $node::getID();
    }

    public function addModule($moduleID) {
        if($this->hasModule($moduleID) === null) {
            $module = Module::getClass($moduleID);
            $modules = $this->getModules();
            $modules[] = $module::getID();
            $this->modules = join(':', $modules);

            return true;
        }

        return false;
    }

    public function hasModule($moduleID) {
        $mods = explode(':', $this->modules);
        foreach($mods as $mod) {
            if($mod == $moduleID) return true;
        }

        return null;
    }

    public function addLink(Link $link) {
        if($this->hasLink($link) === null) {
            $this->links[] = $link;
            return true;
        }

        return false;
    }

    public function hasLink(Link $link) {
        $links = $this->getLinks();
        foreach($links as $lnk) {
            /** @var Link $lnk */
            $f1 = $lnk->getFromNode();
            $f2 = $link->getFromNode();
            $t1 = $lnk->getFromNode();
            $t2 = $link->getFromNode();

            if($f1::getID() == $f2::getID() && $t1::getID() == $t2::getID()) {
                return $lnk;
            }
        }

        return null;
    }

    public function addQuest(Quest $quest) {
        $task = $quest->getTask();
        if($this->hasQuest($task::getID()) === null) {
            $this->quests[] = $quest;
            return true;
        }

        return false;
    }

    public function hasQuest($questID) {
        $quests = $this->getQuests();
        foreach($quests as $quest) {
            /** @var Quest $quest */
            $task = $quest->getTask();
            if($task::getID() == $questID) {
                return $quest;
            }
        }

        return null;
    }

    public function addSquad(Squad $squad) {
        //TODO ? check if squad exists??
        $this->getSquads();
        if(!in_array($squad, $this->squads)) {
            $this->squads[] = $squad;
        }
    }

    public function addNodeObject(NodeObject $object) {
        $obj = $object->getObject();
        if($this->hasNodeObject($obj::getID()) === null) {
            $this->nodeObjects[] = $object;
            return true;
        }

        return false;
    }

    public function hasNodeObject($nodeObjID) {
        $nodeObjs = $this->getNodeObjects();
        foreach($nodeObjs as $obj) {
            /** @var NodeObject $obj */
            $nobj = $obj->getObject();
            if($nobj::getID() == $nodeObjID) {
                return $obj;
            }
        }
        return null;
    }

    public function removeNodeObject(NodeObject $object) {
        $objects = $this->getNodeObjects();
        foreach($objects as $key => $obj) {
            if($obj->getID() === $object->getID()) {
                unset($this->nodeObjects[$key]);
                break;
            }
        }
    }

    public function modInventory($itemID, $number) {
        //TODO check if there is place in inventory
        /** @var Inventory $inv */
        $inv = $this->hasInventory($itemID);
        if($inv !== null) {
            $inv->modNumber($number);
        } elseif($number > 0) {
            $inv = Inventory::create();
            $inv->setHero($this);
            $inv->setItem($itemID);
            $inv->setNumber($number);
            $inv->save();
            $this->inventory[] = $inv;
        } else {

        }

        return $inv;
    }

    /**
     * @param string $itemID
     * @return Inventory|null
     */
    public function hasInventory($itemID) {
        $items = $this->getInventory();
        foreach($items as $item) {
            /** @var Inventory $item */
            $citem = $item->getItem();
            if($citem::getID() == $itemID) {
                return $item;
            }
        }
        return null;
    }

    public function removeInventory(Inventory $inv) {
        $inventory = $this->getInventory();
        foreach($inventory as $key => $inven) {
            if($inven->getID() === $inv->getID()) {
                unset($this->inventory[$key]);
                break;
            }
        }
    }

    public function getLinks() {
        if($this->links === null) {
            $this->links = Link::getAllBy('heroID', $this->getID());
        }
        return $this->links;
    }

    public function getQuests() {
        if($this->quests === null) {
            $this->quests = Quest::getAllBy('heroID', $this->getID());
        }
        return $this->quests;
    }

    public function getSquads() {
        if($this->squads === null) {
            $this->squads = Squad::getAllBy('heroID', $this->getID());
        }
        return $this->squads;
    }

    public function getInventory() {
        if($this->inventory === null) {
            $this->inventory = Inventory::getAllBy('heroID', $this->getID());
        }
        return $this->inventory;
    }

    public function getNodeObjects() {
        if($this->nodeObjects === null) {
            $this->nodeObjects = NodeObject::getAllBy('heroID', $this->getID());
        }
        return $this->nodeObjects;
    }

    public function __toString() {
        return $this->getName() . ' (#' . $this->getID() . ')';
    }

    public function toArray() {
        return [
            'ID' => $this->getID(),
            'name' => $this->getName(),
            'lastNode' => $this->lastNode,
            'modules' => $this->getModules(),
        ];
    }

}