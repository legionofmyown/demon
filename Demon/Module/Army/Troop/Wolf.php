<?php

namespace Demon\Module\Army\Troop;

use Demon\Service\Log;
use Demon\Module\Army\Troop;
use Demon\Module\Army\Action;

class Wolf extends Troop {
    public static $ID = 'wolf';
    public static $itemType = 'unit.wolf';

    protected $attack = 8;
    protected $defence = 3;
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