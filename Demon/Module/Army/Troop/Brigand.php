<?php

namespace Demon\Module\Army\Troop;

use Demon\Module\Army\Troop;
use Demon\Module\Army\Action;

class Brigand extends Troop {
    public static $ID = 'brigand';
    public static $itemType = 'unit.brigand';

    protected $attack = 8;
    protected $defence = 5;
    protected $unitHP = 40;

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