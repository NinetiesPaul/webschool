<?php

namespace App\Controllers;

use App\Enum;
use App\Util;

class IndexController
{
    protected $util;

    public function __construct()
    {
        $this->util = new Util();
    }
    
    public function index()
    {
        session_start();
        if (isset($_SESSION['tipo'])) {
            $tipo = $_SESSION['tipo'];
            if ($tipo == Enum::TIPO_ADMIN) {
                header('Location: admin/home');
            }
            if ($tipo == Enum::TIPO_RESPONSAVEL) {
                header('Location: responsavel/home');
            }
            if ($tipo == Enum::TIPO_PROFESSOR) {
                header('Location: professor/home');
            }
            if ($tipo == Enum::TIPO_ALUNO) {
                header('Location: aluno/home');
            }
        }

        $this->util->loadTemplate('index.html');
    }
}
