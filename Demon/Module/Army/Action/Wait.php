<?php
namespace Demon\Module\Army\Action;


use Demon\Module\Army\Action;
use Demon\Module\Army\Combat;
use Demon\Module\Army\Troop;
use Demon\Service\Log;

class Wait extends Action {
    /** @var Troop|null  */
    protected $actor = null;

    public function __construct(Troop $actor) {
        $this->actor = $actor;
    }

    public static function getAction(Combat $combat, Troop $actor, $energy) {
        return new self($actor);
    }

    public function execute() {
        $this->actor->getCombat()->addCombatLog(
            $this->actor,
            'execute',
            [
                'type' => 'wait',
            ]
        );

        return true;
    }

} 