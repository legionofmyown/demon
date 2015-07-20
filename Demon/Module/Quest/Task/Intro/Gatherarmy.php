<?php
namespace Demon\Module\Quest\Task\Intro;

use Demon\Core\Model\Hero;
use Demon\Module\Army\Model\Squad;
use Demon\Module\Army\Troop\Brigand;
use Demon\Module\Family\Ancestor\Main;
use Demon\Module\Map\MapObject\Army\Wolves;
use Demon\Module\Map\MapService;
use Demon\Module\Map\Node\Start\Cave;
use Demon\Module\Quest\Model\Quest;
use Demon\Module\Quest\QuestService;
use Demon\Module\Quest\Task;

class Gatherarmy extends Task {
    public static $ID = 'intro.gatherarmy';
    public static $name = 'Gather army';
    public static $group = 'intro';

    public static function checkTaskState(Hero $hero, Quest $quest) {
        //check general and army
        $squads = $hero->getSquads();
        foreach($squads as $squad) {
            /** @var Squad $squad */
            $family = $squad->getCharacter()->getFamily();
            $troop = $squad->getTroop();
            if($family::getID() === Main::getID() && $troop::getID() === Brigand::getID()) {
                Gatherarmy::onCompleted($hero, $quest);
            }
        }
    }

    public static function onCompleted(Hero $hero, Quest $quest) {
        $quest->setState(Task::COMPLETED);
        $quest->save();
        //TODO reward etc.

        //next quest
        MapService::addNodeObject($hero, Cave::getID(), Wolves::getID(), [
            //TODO check nodeobject config
            //TODO correct params
            'squads' => [
                [
                    'commander' => 'Alpha Wolf',
                    'troop' => 'wolf',
                    'number' => 12,
                ],
            ],
        ]);

        //TODO ? give quest here?
        QuestService::addQuest($hero, Defeatwolves::getID());
    }
}