<?php
namespace Demon\Module\Army\Action;


use Demon\Module\Army\Action;
use Demon\Module\Army\Combat;
use Demon\Module\Army\Troop;
use Demon\Service\Log;

class Prepare extends Action {
    /** @var Troop|null  */
    protected $actor = null;
    protected $energy = 0;

    public function __construct(Troop $actor, $energy) {
        $this->actor = $actor;
        $this->energy = $energy;
    }

    public static function getAction(Combat $combat, Troop $actor, $energy) {
        return new self($actor, $energy);
    }

    public function execute() {
        $this->actor->modEnergy($this->energy);

        $this->actor->getCombat()->addCombatLog(
            $this->actor,
            'execute',
            [
                'energy' => $this->energy,
                'type' => 'prepare',
            ]
        );
        return true;
    }

} 