<?php

namespace App\Controllers;

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
            if ($tipo == "admin") {
                header('Location: admin/home');
            }
            if ($tipo == "responsavel") {
                header('Location: responsavel/home');
            }
            if ($tipo == "professor") {
                header('Location: professor/home');
            }
            if ($tipo == "aluno") {
                header('Location: aluno/home');
            }
        }

        $this->util->loadTemplate('index.html');
    }
}
