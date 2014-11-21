<?php
namespace Comments;
/**
 * @param $exception \Exception
 */
function JSON_exception_handler($exception) {
    JSON::outError($exception->getMessage());
}
function jsonErrorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno){
        case E_NOTICE:
            break;
        default:
            JSON::outError("[$errfile:$errline] $errstr");
    }
}
class JSON {
    /**
     * @param string $msg
     * @param int $code
     */
    public static function outError($msg, $code=500)
    {
        $out = new \stdClass();
        $out->result = "error";
        $out->message = $msg;
        header(':', true, $code); // needs to be fixed to http_response_code in php 5.4
        echo json_encode($out);
        die();
    }

    public static function Setup(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST");
        header("Access-Control-Max-Age: 1000");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-type: application/json");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') die();
        set_error_handler("jsonErrorHandler");
        set_exception_handler('JSON_exception_handler');
    }

    /**
     * @return \STDClass
     */
    public static function ReadInput(){
        $data = json_decode(file_get_contents('php://input'));
        if ($data == null) JSON::outError("No input", 400);
        return $data;
    }
}