<?php

namespace Demon\Module\Army\Troop;

use Demon\Service\Log;
use Demon\Module\Army\Troop;
use Demon\Module\Army\Action;

class Knight extends Troop {
    public static $ID = 'knight';
    public static $itemType = 'unit.knight';
    protected $attack = 20;
    protected $defence = 15;
    protected $unitHP = 70;

    public function processStepEnergy() {
        $this->modEnergy(100);
        $this->combat->addCombatLog($this, 'energy', ['energy' => 100]);
    }

    public function processStepDecide() {
        $act = Action\SideAttack::getAction($this->combat, $this, 150);

        if($act === null) {
            if($this->getEnergy() < 150) {
                $act = Action\Prepare::getAction($this->combat, $this, 50);
            } else {
                $act = Action\DirectAttack::getAction($this->combat, $this, 100);
            }
        }

        $this->action = ($act !== null) ? $act : Action\Wait::getAction($this->combat, $this, 0);
    }

    public function processStepAct() {
        $this->action->execute();
    }

}