<?php
namespace Demon\Module\Army;

use Demon\Exception\GameException;
use Demon\Module\Army\Troop;
use Demon\Module\Army\Effect;

class Combat {
    /** @var BattleField|null */
	private $field = null;
    /** @var array */
	private $troops = [];
    private $combatLog = [];
    private $winner = null;
	
	public function __construct() {
		$this->field = new BattleField(5, 3, 200);
	}
    
    public function getTroops() {
        return $this->troops;
    }
	
    public function getField() {
        return $this->field;
    }

    public function getCombatLog() {
        return $this->combatLog;
    }

    public function addCombatLog(Troop $troop, $step, $action) {
        $this->combatLog[] = [
            'unit' => $troop->toArray(),
            'step' => $step,
            'action' => $action,
        ];
    }
	
	public function makeTurn() {
        $this->combatLog = [];
		$this->stepEnergy();
        $this->stepDecide();
        $this->stepAct();
        $this->stepResolve();

        return $this->getWinner();
	}

    public function getWinner() {
        if($this->winner === null) {
            $sides = [];
            foreach ($this->troops as $troop) {
                /** @var $troop Troop */
                if ($troop->getCurPpl() > 0) {
                    $sides[$troop->getOwner()->getID()] = 1;
                }
            }

            $winner = null;
            if (count($sides) == 1) {
                $ids = array_keys($sides);
                $winner = reset($ids);
                $this->combatLog[] = ['winner' => $winner];
                $this->winner = $winner;
            }
        }

        return $this->winner;
    }
	
	private function stepEnergy() {
		foreach($this->troops as $troop) {
            /** @var $troop Troop */
			$troop->processStepEnergy();
		}
	}

    private function stepDecide() {
        foreach($this->troops as $troop) {
            /** @var $troop Troop */
            $troop->processStepDecide();
        }
    }

    private function stepAct() {
        foreach($this->troops as $troop) {
            /** @var $troop Troop */
            $troop->processStepAct();
        }
    }

    private function stepResolve() {
        foreach($this->troops as $troop) {
            /** @var $troop Troop */
            //$troop->processStepResolve();
            foreach($troop->getEffects() as $k => $effect) {
                /** @var $effect Effect */
                if($effect->execute()) $troop->removeEffect($k);
            }
        }
    }

    public function addTroop(Troop $troop, $x, $y) {
        if(in_array($troop, $this->troops)) {
            throw new GameException('This troop was alread added to this combat');
        }
        $this->troops[] = $troop;
        $troop->setCombat($this);
        $this->field->addTroop($troop, $x, $y);
    }

}
