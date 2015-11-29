<?php

function __autoload($classname) {
    $filename = "./lib/" . $classname . ".php";
    if (file_exists($filename)) {
        include_once($filename);
    }
}

$resolution_log = array();

Events::listen('box.resolved', function($box) use (&$resolution_log) {

    array_push($resolution_log, array('what' => 'box', 'position' => $box->position, 'value' => $box->value));

    if ($box->getLine()->isResolved()) {
        array_push($resolution_log, array('what' => 'line', 'position' => $box->getLine()->position));
    }
    if ($box->getColumn()->isResolved()) {
        array_push($resolution_log, array('what' => 'column', 'position' => $box->getColumn()->position));
    }
    if ($box->getSquare()->isResolved()) {
        array_push($resolution_log, array('what' => 'square', 'position' => $box->getSquare()->position));
    }
});

if (isset($_POST['values']) && is_array($_POST['values'])) {
    $values = $_POST['values'];
}
else {
    throw new Exception('Invalid param : value');
}

$matrix_size = isset($_POST['matrix_size']) ? $_POST['matrix_size'] : null;

header('Content-Type: application/json');

try {

    $board = new Board($values, $matrix_size);
    if ($result = $board->resolve()) {
        echo json_encode(array(
            'log' => $resolution_log,
            'result' => $result
        ));
    }
    else {
        throw new SudokuException('Board can not be solved',400);
    }
}
catch (Exception $e) {

    if ($e instanceof SudokuException) {
        $message = $e->getMessage();
        $status_code = $e->getCode();
      
    }
    else {
        $message = 'Erreur inconnue';
        $status_code = 500;
    }
    
    header($_SERVER['SERVER_PROTOCOL'] . $status_code . ' ' . $message, true, $status_code);
    http_response_code($status_code);
    echo json_encode(array(
        'error' => true,
        'message' => $message,
        'log'=>$resolution_log
    ));
}
//TODO : error handler
