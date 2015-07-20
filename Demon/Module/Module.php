<?php
namespace Demon\Module;

use Demon\Core\CoreObject;
use Demon\Core\Model\Hero;
use Demon\Exception\CoreObjectException;

abstract class Module extends CoreObject {

    public static function getClass($name) {
        $class = __NAMESPACE__ . '\\' . ucfirst($name) . '\\' . ucfirst($name) . 'Service';
        return static::loadClass($class, $name);
    }

    public static function getHeroData(Hero $hero) {
        throw new CoreObjectException('Module [' . static::getID() . '] method "getHeroData" is not defined');
    }

    public static function getID() {
        return static::$ID;
    }
}