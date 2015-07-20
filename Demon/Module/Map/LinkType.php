<?php
namespace Demon\Module\Map;

use Demon\Core\CoreObject;

abstract class LinkType extends CoreObject {

    public static function getClass($name) {
        $class = __NAMESPACE__ . '\\LinkType\\' . ucfirst($name);
        return static::loadClass($class, $name);
    }

    public static function getID() {
        return static::$ID;
    }
}