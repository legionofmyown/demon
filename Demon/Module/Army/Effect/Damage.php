<?php
namespace Demon\Module\Army\Effect;

use Demon\Module\Army\Effect;
use Demon\Module\Army\Troop;

class Damage extends Effect {
    /** @var Troop|null  */
    protected $target = null;
    protected $ppl = 0;

    public function __construct(Troop $target, $ppl) {
        $this->target = $target;
        $this->ppl = $ppl;
    }

    public function execute() {
        $this->target->modCurPpl(-$this->ppl);
        return true;
    }
}
