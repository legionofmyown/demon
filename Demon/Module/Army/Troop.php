<?php
namespace Demon\Module\Army;

use Demon\Core\CoreObject;
use Demon\Core\Model\Character;
use Demon\Core\Model\Hero;
use Demon\Module\Army\Action;
use Demon\Module\Army\Effect;

abstract class Troop extends CoreObject {
    /** @var Hero|null  */
    protected $owner = null;
    /** @var Character|null  */
    protected $commander = null;
    protected $squadID = null;
    protected $curPpl = 100;
    protected $maxPpl = 100;
    protected $curEnergy = 0;
    /** @var  Combat */
    protected $combat;
    public $x = -1;
    public $y = -1;
    /** @var  Action */
    protected $action;
    protected $effects = [];
    protected $attack = 1;
    protected $defence = 1;
    protected $unitHP = 1;

    public static function getClass($name) {
        $class = __NAMESPACE__ . '\\Troop\\' . ucfirst($name);
        return static::loadClass($class, $name);
    }

    public function __construct(Hero $owner, Character $commander, $ppl, $squadID = null) {
        $this->owner = $owner;
        $this->commander = $commander;
        $this->squadID = $squadID;
        $this->curPpl = $ppl;
        $this->maxPpl = $ppl;
    }
    
    public function getAttack() {
        return $this->attack;
    }

    public function getDefence() {
        return $this->defence;
    }
    
    public function getUnitHP() {
        return $this->unitHP;
    }

    public function setCombat(Combat $combat) {
        $this->combat = $combat;
    }
    
    public function getCombat() {
        return $this->combat;
    }
    
    public function getOwner() {
        return $this->owner;
    }

    public function getCommander() {
        return $this->commander;
    }

    public function getSquadID() {
        return $this->squadID;
    }

    public function getCurPpl() {
        return $this->curPpl;
    }

    public static function getID() {
        return static::$ID;
    }
    
    public function addEffect(Effect $effect) {
        $this->effects[] = $effect;
    }

    public function removeEffect($k) {
        unset($this->effects[$k]);
    }

    public function getEffects() {
        return $this->effects;
    }

	public function getEnergy() {
        return $this->curEnergy;
    }

    public function modEnergy($n) {
        $this->curEnergy += $n;
        if($this->curEnergy < 0) $this->curEnergy = 0;
    }

    public function modCurPpl($n) {
        $this->curPpl += $n;
        if($this->curPpl < 0) $this->curPpl = 0;
    }


    public function consumeEnergy($n) {
		if($this->curEnergy >= $n) {
			$this->curEnergy -= $n;
			return true;
		} else return false;
	}

    public abstract function processStepEnergy();

    public abstract function processStepDecide();
    
    public abstract function processStepAct();

//    public abstract function processStepResolve();
    
    public function __toString() {
        return $this->owner . ' ' . $this->commander . ' ' . $this->getID() . ' (' . $this->getCurPpl() . ') at [' . $this->x . ',' . $this->y . ']';
    }

    public function toArray() {
        $family = $this->commander->getID() !== null ? $this->commander->getFamily() : null;

        return [
            'owner' => [
                $this->owner->getID(),
                $this->owner->getName(),
            ],
            'commander' => [
                'ID' => $this->commander->getID(),
                'name' => $this->commander->getName(),
                'family' => $this->commander->getID() !== null ? $family::$name : '',
            ],
            'troop' => static::getID(),
            'x' => $this->x,
            'y' => $this->y,
            'ppl' => $this->curPpl,
            'maxppl' => $this->maxPpl,
            'energy' => $this->curEnergy,
        ];
    }
}
