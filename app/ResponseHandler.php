<?php

namespace App;

class ResponseHandler
{

    public static function throwError(\Exception $ex, $entidade = 'entidade')
    {
        $errorMsg = $ex->getMessage();

        switch ($ex->getCode()) {
            case 23000:
                $errorMsg = "<b>Erro</b>: $entidade em uso";
                break;
        }

        self::response($errorMsg, true);
    }

    public static function response($msg = null, $isError = false)
    {
        $response = [
            'error' => $isError,
            'msg' => $msg
        ];

        echo json_encode($response);
        exit();
    }
}