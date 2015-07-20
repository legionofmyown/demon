<?php
namespace Demon\Module\Quest;

use Demon\Core\Model\Hero;
use Demon\Exception\GameException;
use Demon\Module\Map\Model\NodeObject;
use Demon\Module\Module;
use Demon\Module\Quest\Model\Quest;

class QuestService extends Module {
    public static $ID = 'quest';

    public static function getHeroData(Hero $hero) {
        static::checkQuests($hero);
        //return quest data
        return Quest::toArrayAll($hero->getQuests());
    }

    public static function addQuest(Hero $hero, $task) {
        /** @var Quest $quest */
        $quest = Quest::create();
        $quest->setHero($hero);
        $quest->setState(Task::STARTED);
        $quest->setTask($task);

        if($hero->addQuest($quest) !== null) {
            $quest->save();
            return $quest;
        }

        return null;
    }

    public static function checkQuests(Hero $hero) {
        $quests = $hero->getQuests();
        foreach($quests as $quest) {
            /** @var Quest $quest */
            if($quest->getState() == 0) {
                $task = $quest->getTask();
                $task::checkTaskState($hero, $quest);
            }
        }
    }

    public static function speakNpc(Hero $hero, $objectID)
    {
        /** @var NodeObject $object */
        $object = NodeObject::get($objectID);

        if ($object->getHeroID() !== $hero->getID()) {
            throw new GameException('Node object ' . $object . ' doesn\'t belong to ' . $hero);
        }

        //TODO ? check if node is reachable?

        $type = $object->getObject();
        if ($type::getGroup() !== 'npc') {
            throw new GameException('Node object ' . $object . ' is not speakable');
        }

        $ret = [];

        if(method_exists($type, 'onSpeak')) {
            $ret = $type::onSpeak($hero, $objectID);
        }


        return $ret;
    }
}