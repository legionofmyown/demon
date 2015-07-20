<?php
namespace Demon\Service;

class Astar extends SplMinHeap {

    private $graphs = array();
    private $best = false;
    public $steps = false;
    public $cost = false;

    public function __construct($graphs, $funcName) {
        $this->graphs = $graphs;
        $this->funcName = $funcName;
    }

    public function findRoute($start, $finish, $funcMod) {
        $this->cost = false;
        $this->heap = array();
        $this->best = false;
        $this->steps = 0;
        
        if(!isset($this->graphs[$start]) || !isset($this->graphs[$finish])) {
            return array();
        }

        $funcName = $this->funcName;
        $open = array($start => 1);
        $closed = array();

        $point = array();
        $point['point'] = $start;
        $point['weight'] = call_user_func($funcName, $start, $finish, $funcMod);
        $point['pathCost'] = 0;
        $point['path'] = array();
        $this->insert($point);
        $cnt = 0;

        while ($testPoint = $this->extract()) {
            $cnt++;

            if (isset($this->graphs[$testPoint['point']])) {
                foreach ($this->graphs[$testPoint['point']] as $newPoint => $newPointCost) {
                    if (!isset($open[$newPoint]) && !isset($closed[$newPoint])) {
                        $point['point'] = $newPoint;
                        $point['weight'] = call_user_func($funcName, $newPoint, $finish, $funcMod) + $testPoint['pathCost'] + $newPointCost;
                        $point['pathCost'] = $testPoint['pathCost'] + $newPointCost;
                        $point['path'] = $testPoint['path'];
                        $point['path'][] = $testPoint['point'];
                        $this->insert($point);
                        $open[$point['point']] = 1;
                    }
                    if ($newPoint === $finish) {
                        $path = $point['path'];
                        $path[] = $finish;
                        $this->steps = $cnt;
                        $this->cost = $testPoint['pathCost'] + $newPointCost;
                        return $path;
                    }
                }
            }
            $closed[$testPoint['point']] = 1;
            unset($open[$testPoint['point']]);
        }

        return array();
    }
	
	protected function compare($a, $b) {
		if($a['weight'] === $b['weight']) {
			return 0;
		} else {
			return $a['weight'] < $b['weight'] ? 1 : -1;
		}
	}
}