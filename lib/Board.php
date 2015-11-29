<?php

/**
 * Description of Board
 *
 * @author macha
 */
class Board {

    /**
     * matrix size
     *
     * @var int
     */
    public $matrix_size;

    /**
     * possibible values for Boxes
     *
     * @var int
     */
    public $possibilities = array();

    /**
     * indexed array of Boxes
     *
     * @var array of Boxes
     */
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

        if (!$this->check()) {
            throw new SudokuException('board is not valid');
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
     * execute check
     * @return boolean
     */
    protected function check() {
        $checked = true;
        for ($i = 0; $i < $this->matrix_size; $i++) {
            $checked = $checked && $this->spots[$i]->getColumn()->check();
            if (!$checked) {
                return false;
            }
        }

        for ($i = 0; $i < $this->matrix_size; $i++) {
            $checked = $checked && $this->spots[$i * $this->matrix_size]->getLine()->check();
            if (!$checked) {
                return false;
            }
        }

        for ($i = 0; $i < sqrt($this->matrix_size); $i++) {
            for ($j = 0; $j < sqrt($this->matrix_size); $j++) {
                $checked = $checked && $this->spots[$i * sqrt($this->matrix_size) + $j * $this->matrix_size * sqrt($this->matrix_size)]->getSquare()->check();
                if (!$checked) {
                    return false;
                }
            }
        }
        return true;
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
                if ($box->tryResolve($this))
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
