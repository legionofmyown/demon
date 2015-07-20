<?php
namespace Demon\Module\Army;

use Demon\Exception\GameException;
use Demon\Module\Army\Troop;

class BattleField {
	private $width = 0;
	private $depth = 0;
	public $map = [];
	public $sides = [];
    private $size = 1000;
	
	public function __construct($w, $d, $s) {
		$this->width = $w;
		$this->depth = $d;
        $this->size = $s;
	}
    
    public function getSize() {
        return $this->size;
    }

	public function getWidth() {
		return $this->width;
	}
	
	private function _makeMap($o) {
		if(!isset($this->map[$o])) {
			$this->map[$o] = [];
			for($x = 0; $x < $this->width; $x++) {
				$this->map[$o][$x] = [];
				for($y = 0; $y < $this->depth; $y++) {
					$this->map[$o][$x][$y] = null;
				}
			}
		}
	}
	
	public function addTroop(Troop $troop, $x, $y) {
		//TODO ? check if troop was already added
		if($this->isOccupied($troop->getOwner()->getID(), $x, $y)) {
			throw new GameException('Point [' . $x. ' , ' . $y . '] is already occupied');
		}
		$troop->x = $x;
		$troop->y = $y;
		$this->map[$troop->getOwner()->getID()][$x][$y] = $troop;
	}
	
    public function isOccupied($owner, $x, $y) {
        return isset($this->map[$owner][$x][$y]);
    }
}
