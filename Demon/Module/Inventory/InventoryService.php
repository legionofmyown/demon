<?php
namespace Demon\Module\Inventory;

use Demon\Core\Model\Hero;
use Demon\Module\Inventory\Model\Inventory;
use Demon\Module\Module;

class InventoryService extends Module {
    public static $ID = 'inventory';

    public static function getHeroData(Hero $hero) {
        //return inventory data
        return Inventory::toArrayAll($hero->getInventory());
    }

    public static function modItemNumber(Hero $hero, $itemID, $number) {
        /** @var Inventory $inv */
        $inv = Inventory::create();
        $inv->setHero($hero);
        $inv->setItem($itemID);
        $inv->setNumber($number);

        $inv = $hero->addInventory($inv);

        if($inv !== null) {
            $inv->save();
        }

    }
}