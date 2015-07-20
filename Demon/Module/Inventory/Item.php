<?php
namespace Demon\Module\Inventory;

use Demon\Core\CoreObject;

abstract class Item extends CoreObject {

    public static function getClass($name) {
        $node = join('\\', array_map('ucfirst', explode('.', $name)));
        $class = __NAMESPACE__ . '\\Item\\' . $node;
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