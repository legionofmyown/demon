<?php
namespace Demon\Module\Inventory\Item;


use Demon\Module\Inventory\Item;

abstract class Unit extends Item {

    public static function getTroop() {
        return static::$troop;
    }

}