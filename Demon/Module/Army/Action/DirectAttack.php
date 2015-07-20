<?php
namespace Demon\Module\Army\Action;

use Demon\Module\Army\Effect\Damage;
use Demon\Module\Army\Action;
use Demon\Module\Army\Combat;
use Demon\Module\Army\Troop;

class DirectAttack extends Action {
    /** @var Troop|null  */
    protected $actor = null;
    /** @var Troop|null  */
    protected $target = null;
    protected $energy = 0;

    public function __construct(Troop $actor, Troop $target, $energy) {
        $this->actor = $actor;
        $this->target = $target;
        $this->energy = $energy;
    }

    public static function getAction(Combat $combat, Troop $actor, $energy) {
        if($actor->getEnergy() < $energy) return null;
        if($actor->getCurPpl() <= 0) return null;

        //get closest unprotected enemy
        $target = null;
        $mindist = 255;
        foreach($combat->getTroops() as $troop) {
            /** @var $troop Troop */

            if(!self::isEnemy($actor, $troop)) continue;

            //already dead
            if(self::isDead($troop)) continue;

            //is protected from direct attack
            if(!self::isDirectlyReachable($combat, $troop)) continue;

            //find closest
            $dist = abs($troop->x - $actor->x) * 1.5 + abs($troop->y - $actor->y);
            if($dist < $mindist) {
                $target = $troop;
                $mindist = $dist;
            }
        }

        if($target !== null) {
            return new self($actor, $target, $energy);
        } else {
            return null;
        }
    }

    public function execute() {
        if($this->actor->consumeEnergy($this->energy)) {

            $maxAttackingNum = $this->actor->getCombat()->getField()->getSize();
            $dmg = max(1, $this->actor->getAttack() - $this->target->getDefence()) * min($maxAttackingNum, $this->actor->getCurPpl());
            $dmgPpl = round(($dmg + round($dmg * rand(-20, 20) / 100)) / $this->target->getUnitHP());
            
            $this->target->addEffect(new Damage($this->target, $dmgPpl));

            $this->actor->getCombat()->addCombatLog(
                $this->actor,
                'execute',
                [
                    'energy' => -$this->energy,
                    'type' => 'directattack',
                    'target' => $this->target->toArray(),
                    'effect' => [
                        'type' => 'damage',
                        'ppl' => $dmgPpl,
                    ]
                ]
            );

        } else {
            //not enough energy for direct attack
            //TODO
        }

    }
}
