<?php

namespace Demon\Module\Army\Troop;

use Demon\Service\Log;
use Demon\Module\Army\Troop;
use Demon\Module\Army\Action;

class Swordsman extends Troop {
    public static $ID = 'swordsman';
    public static $itemType = 'unit.swordsman';
    protected $attack = 10;
    protected $defence = 5;
    protected $unitHP = 50;

    public function processStepEnergy() {
        $this->modEnergy(100);
        $this->combat->addCombatLog($this, 'energy', ['energy' => 100]);
    }

    public function processStepDecide() {
        $act = Action\DirectAttack::getAction($this->combat, $this, 100);

        $this->action = ($act !== null) ? $act : Action\Wait::getAction($this->combat, $this, 0);
    }

    public function processStepAct() {
        $this->action->execute();
    }

}