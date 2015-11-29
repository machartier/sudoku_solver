<?php

/**
 * Description of Square
 *
 * @author macha
 */
class Square extends BoxCollection {

    protected function init($box, $board) {
        $line = floor($box->position / $board->matrix_size);
        $col = $box->position - ($line * $board->matrix_size);

        $square_size = sqrt($board->matrix_size);

        $position_line = floor($line / $square_size);
        $position_column = floor($col / $square_size);

        $this->position = $square_size * ($position_line * $board->matrix_size + $position_column);

        for ($i = 0; $i < $square_size; $i++) {
            for ($j = 0; $j < $square_size; $j++) {
                $this->boxes[] = $board->getBox($this->position + $i * $board->matrix_size + $j);
            }
        }
    }

}
