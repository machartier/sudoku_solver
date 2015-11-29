<?php

/**
 * Description of BoxCollection
 *
 * @author macha
 */
abstract class BoxCollection {

    /**
     * Contained boxes
     *
     * @var array of Boxes
     */
    protected $boxes = array();

    /**
     * Initial position of Container in board
     *
     * @var int
     */
    public $position;

    /**
     * Board container
     *
     * @var Board
     */
    protected $board;

    /**
     * Constructor
     * @param Box $box
     * @param Board $board
     */
    public function __construct(Box $box, Board $board) {
        $this->board = $board;
        $this->init($box, $board);
    }

    /**
     * return remaining possibilities for container
     * @return array of int
     */
    public function getMissing() {
        $values = array_map(function($box) {
            return $box->value;
        }, array_filter($this->boxes, function($box) {
                    return !$box->isEmpty();
                }));
        return array_diff($this->board->possibilities, $values);
    }

    /**
     * return if container is resolved
     * @return boolean
     */
    public function isResolved() {
        return count($this->getMissing()) == 0;
    }

    /**
     * initialize list of contained boxes
     * @param Box $box reference box
     * @param Board board
     * @return void
     */
    abstract protected function init(Box $box, Board $board);
}
