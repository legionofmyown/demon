<?php
namespace Demon\Module\Army\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Core\Model\Character;
use Demon\Core\Model\Hero;
use Demon\Exception\DataException;
use Demon\Exception\GameException;
use Demon\Module\Army\Troop;

class Squad extends DataObject {

    protected static $_dbRequests = [];
    /** @var  Hero */
    protected $hero;
    protected $heroID;
    /** @var  Character */
    protected $character;
    protected $characterID;
    protected $troop;
    protected $number = 0;

    public static function dbTableName()
    {
        return 'squad';
    }

    public static function dbTableFields()
    {
        return ['ID', 'heroID', 'characterID', 'troop', 'number'];
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

    public function setCharacter(Character $character) {
        $this->character = $character;
        $this->characterID = $character->getID();
    }

    public function getCharacter() {
        return $this->character;
    }

    public function getCharacterID() {
        return $this->characterID;
    }

    public function getTroop() {
        //TODO ? specific field?
        return Troop::getClass($this->troop);
    }

    public function setTroop($troop) {
        $troop = Troop::getClass($troop);
        $this->troop = $troop::getID();
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
            throw new GameException('Subtracted more troops then available');
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
        return [
            'ID' => $this->ID,
            'commander' => $this->characterID,
            'troop' => $this->troop,
            'number' => $this->number,
        ];
    }

}