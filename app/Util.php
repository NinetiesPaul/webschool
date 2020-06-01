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

    public function generateLinks($nivel = '', $perfil = false)
    {
        session_start();
        $links = '';

        if (isset($_SESSION['tipo'])) {
            $tipo = $_SESSION['tipo'];

            $path = ($perfil) ? $tipo . "/" . $nivel : $nivel;


            if ($tipo == Enum::TIPO_ADMIN) {
                $links = "
                    <a class='dropdown-item' href='".$path."home'>Home</a>
                    <a class='dropdown-item' href='".$path."turmas'>Turmas</a>
                    <a class='dropdown-item' href='".$path."disciplinas'>Disciplinas</a>
                    <a class='dropdown-item' href='".$path."professores'>Professores</a>
                    <a class='dropdown-item' href='".$path."alunos'>Alunos</a>
                    <a class='dropdown-item' href='".$path."responsaveis'>Responsáveis</a>
                ";
            }
            if ($tipo == Enum::TIPO_RESPONSAVEL) {
                $links = "
                    <a class='dropdown-item' href='".$path."home'>Home</a>
                    <a class='dropdown-item' href='".$path."alunos'>Alunos</a>
                ";
            }
            if ($tipo == Enum::TIPO_PROFESSOR) {
                $links = "
                    <a class='dropdown-item' href='".$path."home'>Home</a>
                    <a class='dropdown-item' href='".$path."turmas'>Turmas</a>
                ";
            }
            if ($tipo == Enum::TIPO_ALUNO) {
                $links = "
                    <a class='dropdown-item' href='".$path."home'>Home</a>
                    <a class='dropdown-item' href='".$path."turmas'>Turmas</a>
                ";
            }
        }

        return $links;
    }
}
