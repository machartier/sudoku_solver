<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BoxCollection
 *
 * @author macha
 */
class BoxCollection {

    protected $boxes = array();
    public $position;
    protected $board;

    public function __construct(Box $box, Board $board) {
        $this->board = $board;
        $this->init($box, $board);
    }

    public function getMissing() {
        $values = array_map(function($box) {
            return $box->value;
        }, array_filter($this->boxes, function($box) {
                    return !$box->isEmpty();
                }));
        return array_diff($this->board->possibilities, $values);
    }
    
    public function isResolved(){
        return count($this->getMissing())==0;
    }

}
