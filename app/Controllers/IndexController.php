<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use App\Templates;

/**
 * Description of IndexController
 *
 * @author Paul Richard
 */
class IndexController {
    //put your code here
    
    protected $template;

    public function __construct() {
        $this->template = new Templates();
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
        
        $templateFinal 	= $this->template->getTemplate('index.html');
        echo $templateFinal;
    }
}
