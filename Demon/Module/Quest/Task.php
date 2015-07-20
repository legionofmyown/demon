<?php
namespace Demon\Module\Quest;

use Demon\Core\CoreObject;
use Demon\Core\Model\Hero;
use Demon\Exception\CoreObjectException;
use Demon\Module\Quest\Model\Quest;

abstract class Task extends CoreObject {
    const STARTED = 1;
    const COMPLETED = 100;

    public static function getClass($name) {
        $node = join('\\', array_map('ucfirst', explode('.', $name)));
        $class = __NAMESPACE__ . '\\Task\\' . $node;
        return static::loadClass($class, $name);
    }

    public static function getID() {
        return static::$ID;
    }

    public static function getGroup() {
        return static::$group;
    }

    public static function getName() {
        return static::$name;
    }

    public static function checkTaskState(Hero $hero, Quest $quest) {
        throw new CoreObjectException('Method "checkTaskState" isnot efined for object ' . static::getID());
    }
}