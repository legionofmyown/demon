<?php
namespace Demon\Module\Map\MapObject\Item;

use Demon\Core\Model\Hero;
use Demon\Module\Inventory\InventoryService;
use Demon\Module\Map\MapObject;
use Demon\Module\Map\MapService;

class Introbrigands extends MapObject {
    public static $ID = 'item.introbrigands';
    public static $name = 'Brigand';
    public static $group = 'item';

    public static function onUse(Hero $hero, $objectID) {
        $ret = [];

        InventoryService::modItemNumber($hero, 'unit.brigand', 50);

        $ret['modules'][InventoryService::getID()] = InventoryService::getHeroData($hero);

        MapService::removeNodeObject($hero, $objectID);

        $ret['modules'][MapService::getID()] = MapService::getHeroData($hero);

        return $ret;
    }
}