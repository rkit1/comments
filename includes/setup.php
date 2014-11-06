<?php
set_include_path(dirname(__FILE__).'/../');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS, POST");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-type: application/json");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') die();

function outError($msg, $code=500)
{
    $out = new stdClass();
    $out->result = "error";
    $out->message = $msg;
    header(':', true, $code); // needs to be fixed to http_response_code in php 5.4
    echo json_encode($out);
    die();
}

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno){
        case E_NOTICE:
        case E_WARNING:
        break;
        default:
            outError("[$errfile:$errline] $errstr");
            die();
    }
}
set_error_handler("myErrorHandler");

function exception_handler($exception) {
    if (get_class($exception) == 'ActiveRecord\DatabaseException')
    {
        outError('Ошибка при обращении к базе данных:' . $exception->getMessage());
    }
    else outError($exception->getMessage());
}
set_exception_handler('exception_handler');