<?php
namespace Demon\Module\Map;

use Demon\Core\CoreObject;
use Demon\Exception\CoreObjectException;

abstract class Node extends CoreObject {

    public static function getClass($name) {
        $node = join('\\', array_map('ucfirst', explode('.', $name)));
        $class = __NAMESPACE__ . '\\Node\\' . $node;
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

}