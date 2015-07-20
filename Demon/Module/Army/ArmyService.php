<?php
namespace Demon\Module\Army;

use Demon\Core\Model\Character;
use Demon\Core\Model\Hero;
use Demon\Exception\GameException;
use Demon\Module\Army\Model\Squad;
use Demon\Module\Inventory\InventoryService;
use Demon\Module\Inventory\Item\Unit;
use Demon\Module\Map\MapService;
use Demon\Module\Map\Model\NodeObject;
use Demon\Module\Module;

class ArmyService extends Module {
    public static $ID = 'army';

    public static function getHeroData(Hero $hero) {
        //return army data
        return [
            'squads' => Squad::toArrayAll($hero->getSquads()),
        ];
    }

    public static function createSquad(Hero $hero, Character $character, $troop, $number) {
        if($character->getHeroID() !== $hero->getID()) {
            throw new GameException('Character ' . $character . ' doesn\'t belong to ' . $hero);
        }

        $squads = $hero->getSquads();
        foreach($squads as $squad) {
            /** @var Squad $squad */
            if($squad->getCharacterID() === $character->getID()) {
                throw new GameException('Character ' . $character . ' already commanding another squad');
            }
        }

        $troopObj = Troop::getClass($troop);
        InventoryService::modItemNumber($hero, $troopObj::$itemType, -$number);

        $squad = Squad::create();
        $squad->setHero($hero);
        $squad->setCharacter($character);
        $squad->setTroop($troopObj::getID());
        $squad->setNumber($number);
        $squad->save();

        $hero->addSquad($squad);

        $ret = [];
        $ret['modules'][ArmyService::getID()] = ArmyService::getHeroData($hero);
        $ret['modules'][InventoryService::getID()] = InventoryService::getHeroData($hero);

        return $ret;
    }

    public static function attackNeutral(Hero $hero, $squads, $objectID) {
        /** @var NodeObject $object */
        $object = NodeObject::get($objectID);

        if($object->getHeroID() !== $hero->getID()) {
            throw new GameException('Node object ' . $object . ' doesn\'t belong to ' . $hero);
        }

        //TODO ? check if node is reachable?

        $type = $object->getObject();
        if($type::getGroup() !== 'army') {
            throw new GameException('Node object ' . $object . ' is not attackable');
        }

        foreach($squads as $key => $squadID) {
            /** @var Squad $squad */
            $squad = Squad::get($squadID);
            if($squad->getHeroID() !== $hero->getID()) {
                throw new GameException('Squad ' . $squad . ' doesn\'t belong to ' . $hero);
            }
            $squads[$key] = $squad;
        }

        $combat = new Combat();

        //add hero's units
        foreach($squads as $squad) {
            //TODO get correct positions from formation
            $posx = 0;
            $posy = 0;

            $class = $squad->getTroop();
            $troop = new $class($hero, Character::get($squad->getCharacterID()), $squad->getNumber(), $squad->getID());
            $combat->addTroop($troop, $posx, $posy);
        }

        //add neutral's units
        $neutral = new Hero();
        $neutral->setName('Neutral');
        $data = $object->getData()->squads;
        foreach($data as $nsquad) {
            //TODO get correct positions from formation
            $posx = 0;
            $posy = 0;

            //TODO use correct configuration
            $commander = new Character();
            $commander->setName($nsquad->commander);
            $class = Troop::getClass($nsquad->troop);
            $troop = new $class($neutral, $commander, $nsquad->number);
            $combat->addTroop($troop, $posx, $posy);
        }

        $ret = static::processCombat($hero, $combat);

        //TODO ? update NodeObject?

        if($ret['winner'] == $hero->getID()) {
            MapService::removeNodeObject($hero, $objectID);
        }

        return ['modules' => ['army' => [
            'squads' => Squad::toArrayAll($hero->getSquads()),
            'battles' => [
                $ret,
            ],
        ]]];
    }

    public static function processCombat(Hero $hero, Combat $combat)
    {
        $winner = null;
        $turns = 0;
        $ret = [];

        while ($winner === null && $turns < 50) {
            $winner = $combat->makeTurn();
            $ret[$turns] = $combat->getCombatLog();
            $turns++;
        }

        //TODO send combat log to second participant

        //modify squads
        $troops = $combat->getTroops();
        foreach($troops as $troop) {
            /** @var Troop $troop */
            $squadID = $troop->getSquadID();
            if($squadID != null) {
                //troop has real owner
                /** @var Squad $squad */
                $squad = Squad::get($squadID);
                //TODO ? wounded? ressurect?
                $squad->setNumber($troop->getCurPpl());
                $squad->save();
            } else {
                //is a neutral army
                //TODO
            }
        }

        return [
            'log' => $ret,
            'winner' => $winner,
        ];
    }

}