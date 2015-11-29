<?php

/**
 * Description of Column
 *
 * @author macha
 */
class Column extends BoxCollection{
   protected function init($box, $board) {
        $this->position = $box->position % $board->matrix_size;
        for ($i = 0; $i < $board->matrix_size; $i++) {
            $this->boxes[] = $board->getBox($this->position + $i * $board->matrix_size);
        }
    }
}
