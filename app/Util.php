<?php

namespace App;

use App\DB\DB;

class Util
{
    public function __construct()
    {
    }

    public function userPermission($tipo)
    {
        session_start();
        $session_type = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
        
        if (!$session_type || $session_type !== $tipo) {
            header("Location: /");
        }
    }
}
