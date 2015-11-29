<?php

/**
 * Description of Line
 *
 * @author macha
 */
class Line extends BoxCollection {

    /**
     * initialize list of contained boxes
     * @param Box $box reference box
     * @param Board board
     * @return void
     */
    protected function init(Box $box, Board $board) {
        $this->position = floor($box->position / $board->matrix_size) * $board->matrix_size;
        for ($i = $this->position; $i < $this->position + $board->matrix_size; $i++) {
            $this->boxes[] = $board->getBox($i);
        }
    }

}
