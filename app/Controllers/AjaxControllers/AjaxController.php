<?php

namespace App\Controllers\AjaxControllers;

use App\DB\Storage\LogStorage;

class AjaxController
{
    protected $response;

    public function __construct()
    {
        new LogStorage();

        $this->response = [
            'error' => false,
            'msg' => null
        ];
    }

    public function throwError(\Exception $ex, $entidade = 'entidade')
    {
        $errorMsg = $ex->getMessage();

        switch ($ex->getCode()) {
            case 23000:
                $errorMsg = "Erro ao deletar: $entidade em uso";
                break;
        }

        $this->response($errorMsg, true);
    }

    public function response($msg = null, $isError = false)
    {
        $this->response['error'] = $isError;
        $this->response['msg'] = $msg;

        echo json_encode($this->response);
        exit();
    }
}
