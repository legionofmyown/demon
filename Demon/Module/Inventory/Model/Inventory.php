<?php
namespace Demon\Module\Inventory\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Core\Model\Hero;
use Demon\Exception\DataException;
use Demon\Exception\GameException;
use Demon\Module\Inventory\Item;

class Inventory extends DataObject {

    protected static $_dbRequests = [];
    /** @var  Hero */
    protected $hero;
    protected $heroID;
    protected $item;
    protected $number = 0;

    public static function dbTableName()
    {
        return 'inventory';
    }

    public static function dbTableFields()
    {
        return ['ID', 'heroID', 'item', 'number'];
    }

    public static function dbRequest($request)
    {
        if(!parent::dbRequest($request)) {
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

    public function getItem() {
        //TODO ? specific field?
        return Item::getClass($this->item);
    }

    public function setItem($item) {
        $item = Item::getClass($item);
        $this->item = $item::getID();
    }

    public function getNumber() {
        return $this->number;
    }

    public function setNumber($number) {
        $this->number = $number;
    }

    public function modNumber($number) {
        $this->number += $number;
        if($this->number < 0) {
            throw new GameException('Subtracted more items then available');
        }

        if($this->number == 0) {
            $this->delete();
        }
    }

    public function delete() {
        $this->hero->removeInventory($this);
        parent::delete();
    }

    public function __toString() {
        //TODO correct naming
        return '(#' . $this->getID() . ')';
    }

    public function toArray() {
        $item = $this->getItem();
        return [
            'item' => $this->item,
            'group' => $item::getGroup(),
            'number' => $this->number,
        ];
    }

}