<?php

class Helper
{
    public static function createErrorMessage(bool $status, string $code, string $message, int $httpCode, array $data = [])
    {
        if (!is_array($data) || count($data) <= 0) {
            $data = array();
        }

        if (!isset($httpCode) || empty($httpCode)) {
            //* Teapot code
            $httpCode = 418;
        }

        http_response_code($httpCode);
        
        return json_encode(array(
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' =>  $data
        ));
    }

    public static function isAllNumber(array $numbers)
    {
        foreach ($numbers as $number) {
            if (!is_numeric($number)) {
                return false;
            }
        }
        return true;
    }

    public static function isAllEqualOrOver(array $numbers, int $value)
    {
        foreach ($numbers as $number) {
            if ($number <= $value) {
                return false;
            }
        }
        return true;
    }
}
