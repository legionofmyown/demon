<?php
namespace Demon\Core\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Exception\DataException;
use Demon\Module\Family\Ancestor;

class Character extends DataObject {

    protected static $_dbRequests = [];
    protected $hero;
    protected $heroID;
    protected $name = '';
    protected $family;

    public static function dbTableName()
    {
        return 'character';
    }

    public static function dbTableFields()
    {
        return ['ID', 'heroID', 'name', 'family'];
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

    public function getHero() {
        if($this->hero === null) {
            $this->hero = Hero::get($this->heroID);
        }
        return $this->hero;
    }

    public function getHeroID() {
        return $this->heroID;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getFamily() {
        $family = Ancestor::getClass($this->family);
        return $family;
    }

    public function setFamily($family) {
        $family = Ancestor::getClass($family);
        $this->family = $family::getID();
    }

    public function __toString() {
        return $this->getName() . ' (#' . $this->getID() . ')';
    }

    public function toArray() {
        $family = $this->getFamily();
        return [
            'ID' => $this->getID(),
            'name' => $this->getName(),
            'family' => $family::$name,
        ];
    }

}