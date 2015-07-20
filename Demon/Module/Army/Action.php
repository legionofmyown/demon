<?php

namespace Demon\Module\Army;

use Demon\Module\Army\Troop;
use Demon\Module\Army\Combat;
use Demon\Core\GameException;

abstract class Action {

    public static function getAction(Combat $combat, Troop $actor, $energy) {
        throw new GameException('Method "getAction" not implemented');
    }

    public abstract function execute();

    /**
     * @param Combat $combat
     * @param Troop $troop
     * @return bool
     */
    public static function isDirectlyReachable(Combat $combat, Troop $troop) {
        $field = $combat->getField();

        if($troop->y == 0) return true;
        for($y = $troop->y - 1; $y >= 0; $y--) {
            if($field->isOccupied($troop->getOwner()->ID, $troop->x, $y)) return false;
        }

        return true;
    }

    /**
     * @param Combat $combat
     * @param Troop $troop
     * @return bool
     */
    public static function isSidewaysReachable(Combat $combat, Troop $troop) {
        $field = $combat->getField();
        $width = $field->getWidth();

        if($troop->x == 0 || $troop->x == $width - 1) return true;
        for($x = 0; $x < $troop->x; $x++) {
            if($field->isOccupied($troop->getOwner()->ID, $x, $troop->y)) return false;
        }
        for($x = $troop->x + 1; $x < $width; $x++) {
            if($field->isOccupied($troop->getOwner()->ID, $x, $troop->y)) return false;
        }

        return true;
    }

    /**
     * @param Troop $troop1
     * @param Troop $troop2
     * @return bool
     */
    public static function isEnemy(Troop $troop1, Troop $troop2) {
        return ($troop1->getOwner() !== $troop2->getOwner());
    }

    public static function isDead(Troop $troop) {
        return ($troop->getCurPpl() <= 0);
    }
}