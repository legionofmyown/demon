<?php
namespace Demon\Module\Family;

use Demon\Core\Model\Character;
use Demon\Core\Model\Hero;
use Demon\Module\Module;

class FamilyService extends Module {
    public static $ID = 'family';

    public static function getHeroData(Hero $hero) {
        //return characters
        $chars = Character::toArrayAll(Character::getAllBy('heroID', $hero->getID()));

        return $chars;
    }

    public static function addCharacter(Hero $hero, $ancestor, $name) {
        /** @var Character $char */
        $char = Character::create();
        $char->setHero($hero);
        $char->setFamily($ancestor);
        $char->setName($name);
        $char->save();
    }
}