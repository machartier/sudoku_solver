<?php

/**
 * Description of Column
 *
 * @author macha
 */
class Column extends BoxCollection {

    /**
     * initialize list of contained boxes
     * @param Box $box reference box
     * @param Board board
     * @return void
     */
    protected function init(Box $box, Board $board) {
        $this->position = $box->position % $board->matrix_size;

        for ($i = 0; $i < $board->matrix_size; $i++) {
            $this->boxes[] = $board->getBox($this->position + $i * $board->matrix_size);
        }
    }

}
