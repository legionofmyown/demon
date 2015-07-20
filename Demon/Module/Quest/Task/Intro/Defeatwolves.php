<?php
namespace Demon\Module\Quest\Task\Intro;

use Demon\Core\Model\Hero;
use Demon\Module\Map\MapObject\Army\Wolves;
use Demon\Module\Map\Model\NodeObject;
use Demon\Module\Quest\Model\Quest;
use Demon\Module\Quest\Task;

class Defeatwolves extends Task
{
    public static $ID = 'intro.defeatwolves';
    public static $name = 'Defeat wolves';
    public static $group = 'intro';

    public static function checkTaskState(Hero $hero, Quest $quest)
    {
        //TODO ? more precise verification?
        // LOGIC
        // nodeObject army:wolf doesn't exist
        //TODO where???
        if($hero->hasNodeObject(Wolves::getID()) === null) {
            Defeatwolves::onCompleted($hero, $quest);
        }
    }

    public static function onCompleted(Hero $hero, Quest $quest)
    {
        $quest->setState(Task::COMPLETED);
        $quest->save();
        //TODO reward etc.
    }
}