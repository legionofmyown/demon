<?php
namespace Demon\Core;

use Demon\Exception\CoreObjectException;

abstract class CoreObject {

    public static function getID() {
        throw new CoreObjectException('Class ID getter not defined');
    }

    public static function getClass($name) {
        throw new CoreObjectException('Class getter not defined');
    }

    protected static function loadClass($class, $name) {
        if(!class_exists($class) || $class::getID() != $name) {
            throw new CoreObjectException('Core object class "' . $name . '" not found');
        }
        return $class;
    }
}