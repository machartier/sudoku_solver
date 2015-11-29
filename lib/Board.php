<?php

/**
 * Description of Board
 *
 * @author macha
 */
class Board {

    public $matrix_size;
    public $possibilities = array();
    protected $spots = array();

    /*
     * constructor
     * @param array of int $input
     * @param int $matrix_size
     * @return Box
     */
    public function __construct(array $input, $matrix_size = 9) {

        if ((sqrt($matrix_size) % 1) != 0) {
            throw new SudokuException('matrix size ' . $matrix_size . ' invalid : square root must be an integer');
        }

        $this->matrix_size = $matrix_size;
        $this->possibilities = range(1, $this->matrix_size);

        for ($i = 0; $i < $this->matrix_size * $this->matrix_size; $i++) {
            $this->spots[$i] = new Box($i, isset($input[$i]) ? $input[$i] : null, $this);
        }
    }

    /*
     * get the Box of given position
     * @param int $index
     * @return Box
     */
    public function getBox($index) {
        if (isset($this->spots[$index])) {
            return $this->spots[$index];
        }
        throw new SudokuException('index ' . $index . ' unavailable');
    }

    /**
     * returns not resolved Boxes
     * @return array of Boxes
     */
    protected function getEmpties() {

        return array_filter($this->spots, function($box) {
            return $box->isEmpty();
        });
    }

    /**
     * execute resolving process
     * @return boolean true if board is resolved
     */
    protected function processResolve() {

        // resolve resolvable
        $resolved = 1;
        while ($resolved) {
            $resolved = 0;
            foreach ($this->getEmpties() as $box) {
                if ($box->try_resolve($this))
                    $resolved ++;
            }
        }
        // TODO if remaining empties, try hypothesis, with a stack

        if (count($this->getEmpties()))
            return false;

        return true;
    }

    /**
     * resolve and return resolved spots
     * @return array of spots with resolved values
     */
    public function resolve() {

        $resolve = $this->processResolve();

        if (!$resolve) {
            return false;
        }
        return array_map(function($box) {
            return $box->value;
        }, $this->spots);
    }

}
