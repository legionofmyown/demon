<?php
namespace Demon\Module\Family;

use Demon\Core\CoreObject;
use Demon\Exception\CoreObjectException;

abstract class Ancestor extends CoreObject {

    public static function getClass($name) {
        $class = __NAMESPACE__ . '\\Ancestor\\' . ucfirst($name);
        if($class::getID() != $name) {
            throw new CoreObjectException('Core object class not found');
        }
        return $class;
    }

    public static function getID() {
        return static::$ID;
    }
}