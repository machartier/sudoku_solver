<?php

/**
 * Description of Box
 *
 * @author macha
 */
class Box {

    public $position;
    public $board;
    public $value = null;
    protected $possibilities = array();
    protected $line;
    protected $column;
    protected $square;

    public function __construct($position, $value, $board) {
        $this->position = $position;
        if ($value) {
            $this->value = $value;
        }
        $this->board = $board;
    }

    public function isEmpty() {
        return is_null($this->value);
    }

    public function getLine() {
        if (!isset($this->line)) {
            $this->line = new Line($this, $this->board);
        }
        return $this->line;
    }

    public function getColumn() {
        if (!isset($this->column)) {
            $this->column = new Column($this, $this->board);
        }
        return $this->column;
    }

    public function getSquare() {
        if (!isset($this->square)) {
            $this->square = new Square($this, $this->board);
        }
        return $this->square;
    }

    public function try_resolve(Board $board) {

        $line_missing = $this->getLine()->getMissing();
        $col_missing = $this->getColumn()->getMissing();
        $sq_missing = $this->getSquare()->getMissing();
        
        $this->possibilities = array_intersect($line_missing, $col_missing, $sq_missing);

        if (count($this->possibilities) == 1) {
            $this->value = current($this->possibilities);
            Events::fire('box.resolved', array($this));
            return true;
        }
        return false;
    }

}
