<?php
namespace Demon\Module\Map\MapObject\Npc;

use Demon\Core\Model\Hero;
use Demon\Module\Army\ArmyService;
use Demon\Module\Inventory\InventoryService;
use Demon\Module\Map\MapObject;
use Demon\Module\Map\MapService;
use Demon\Module\Map\Node\Start\Cave;
use Demon\Module\Quest\QuestService;
use Demon\Module\Quest\Task\Intro\Gatherarmy;

class Brigand extends MapObject {
    public static $ID = 'npc.brigand';
    public static $name = 'Brigand';
    public static $group = 'npc';

    public static function onSpeak(Hero $hero, $objectID) {
        $ret = [];

        if(QuestService::addQuest($hero, Gatherarmy::getID()) !== null) {

            $hero->addModule(QuestService::getID());
            $hero->addModule(InventoryService::getID());
            $hero->addModule(ArmyService::getID());
            $hero->save();


            $ret['modules'][QuestService::getID()] = QuestService::getHeroData($hero);

            MapService::removeNodeObject($hero, $objectID);
            MapService::addNodeObject($hero, Cave::getID(), MapObject\Item\Introbrigands::getID());

            $ret['modules'][MapService::getID()] = MapService::getHeroData($hero);
        }

        return $ret;
    }
}