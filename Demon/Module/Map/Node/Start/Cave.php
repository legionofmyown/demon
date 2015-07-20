<?php
namespace Demon\Module\Map\Node\Start;

use Demon\Core\Model\Hero;
use Demon\Module\Map\MapObject\Npc\Brigand;
use Demon\Module\Map\MapService;
use Demon\Module\Map\Node;
use Demon\Module\Quest\QuestService;
use Demon\Module\Quest\Task;
use Demon\Module\Quest\Task\Intro\Gatherarmy;

class Cave extends Node {
    public static $ID = 'start.cave';
    public static $name = 'Cave';
    public static $group = 'start';

    public static function onEnter(Hero $hero) {
        $ret = [];

        $quest = $hero->hasQuest(Gatherarmy::getID());

        // LOGIC
        // hasn't quest
        // nodeObject doesn't exist
        if($quest === null && MapService::addNodeObject($hero, Cave::getID(), Brigand::getID(), []) !== null) {
            $ret['modules'][MapService::getID()] = MapService::getHeroData($hero);
        }

        return $ret;
    }
}