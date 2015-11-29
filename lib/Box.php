<?php

/**
 * Description of Box
 *
 * @author macha
 */
class Box {

    /**
     * Board container for box
     *
     * @var Board
     */
    public $board;

    /**
     * index of the Box in board
     *
     * @var int
     */
    public $position;

    /**
     * Value of the box
     *
     * @var int
     */
    public $value = null;

    /**
     * Line container of the Box
     *
     * @var Line
     */
    protected $line;

    /**
     * Column container of the Box
     *
     * @var Column
     */
    protected $column;

    /**
     * Square container of the Box
     *
     * @var Square
     */
    protected $square;

    /*
     * constructor
     * @param int $position
     * @param mixed $value
     * @param Board $board
     * @return Box
     */
    public function __construct($position, $value, Board $board) {
        $this->position = $position;
        if ($value) {
            $this->value = $value;
        }
        $this->board = $board;
    }

    /**
     * test if Box is resolved
     * @return boolean
     */
    public function isEmpty() {
        return is_null($this->value);
    }

    /**
     * return Line container for box
     * @return Line
     */
    public function getLine() {
        if (!isset($this->line)) {
            $this->line = new Line($this, $this->board);
        }
        return $this->line;
    }

    /**
     * return Column container for box
     * @return Column
     */
    public function getColumn() {
        if (!isset($this->column)) {
            $this->column = new Column($this, $this->board);
        }
        return $this->column;
    }

    /**
     * return Square container for box
     * @return Square
     */
    public function getSquare() {
        if (!isset($this->square)) {
            $this->square = new Square($this, $this->board);
        }
        return $this->square;
    }

    /**
     * test if box can be resolved, an resolve il yes
     * @return boolean
     */
    public function tryResolve() {

        $line_missing = $this->getLine()->getMissing();
        $col_missing = $this->getColumn()->getMissing();
        $sq_missing = $this->getSquare()->getMissing();

        $possibilities = array_intersect($line_missing, $col_missing, $sq_missing);

        if (count($possibilities) == 1) {
            $this->value = current($possibilities);
            Events::fire('box.resolved', array($this));
            return true;
        }
        return false;
    }

}
