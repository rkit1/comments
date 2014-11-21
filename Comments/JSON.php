<?php
namespace Comments;
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

    public static function ErrorHandler($errno, $errstr, $errfile, $errline)
    {
        switch ($errno){
            case E_NOTICE:
            case E_WARNING:
            break;
            default:
            JSON::outError("[$errfile:$errline] $errstr");
        }
    }

    /**
     * @param $exception \Exception
     */
    public static function ExceptionHandler($exception) {
        JSON::outError($exception->getMessage());
    }

    public static function Setup(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST");
        header("Access-Control-Max-Age: 1000");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-type: application/json");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') die();

        set_error_handler(array('JSON', 'ErrorHandler'));
        set_exception_handler(array('JSON', 'ExceptionHandler'));
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