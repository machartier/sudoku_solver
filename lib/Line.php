<?php

/**
 * Description of Line
 *
 * @author macha
 */
class Line extends BoxCollection {

    protected function init($box, $board) {
        $this->position = floor($box->position/$board->matrix_size)*$board->matrix_size;
        for ($i = $this->position; $i < $this->position+$board->matrix_size; $i++) {
            $this->boxes[] = $board->getBox($i);
        }
    }

}
