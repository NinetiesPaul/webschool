<?php

namespace App;

use App\DB\DB;

class Util
{
    protected $connection;
    protected $template;

    public function __construct()
    {
        $this->template = new Templates();
        $this->connection = new DB();
    }

    public function userPermission($tipo)
    {
        session_start();
        $session_type = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
        
        if (!$session_type || $session_type !== $tipo) {
            header('Location: /webschool/');
        }
    }

    public function loadTemplate($path, $args = null)
    {
        $template 	= $this->template->getTemplate($path);
        if ($args) {
            $template = $this->template->parseTemplate($template, $args);
        }
        echo $template;
    }

    public function generateLinks($nivel = '')
    {
        $links = '';
        if (isset($_SESSION['tipo'])) {
            $tipo = $_SESSION['tipo'];
            if ($tipo == Enum::TIPO_ADMIN) {
                $links = "
                    <a class='dropdown-item' href='".$nivel."home'>Home</a>
                    <a class='dropdown-item' href='".$nivel."turmas'>Turmas</a>
                    <a class='dropdown-item' href='".$nivel."disciplinas'>Disciplinas</a>
                    <a class='dropdown-item' href='".$nivel."professores'>Professores</a>
                    <a class='dropdown-item' href='".$nivel."alunos'>Alunos</a>
                    <a class='dropdown-item' href='".$nivel."responsaveis'>Responsáveis</a>
                ";
            }
            if ($tipo == Enum::TIPO_RESPONSAVEL) {
                $links = "
                    <a class='dropdown-item' href='".$nivel."home'>Home</a>
                    <a class='dropdown-item' href='".$nivel."alunos'>Alunos</a>
                ";
            }
            if ($tipo == Enum::TIPO_PROFESSOR) {
                $links = "
                    <a class='dropdown-item' href='".$nivel."home'>Home</a>
                    <a class='dropdown-item' href='".$nivel."turmas'>Turmas</a>
                ";
            }
            if ($tipo == Enum::TIPO_ALUNO) {
                $links = "
                    <a class='dropdown-item' href='".$nivel."home'>Home</a>
                    <a class='dropdown-item' href='".$nivel."turmas'>Turmas</a>
                ";
            }
        }
        return $links;
    }
}
