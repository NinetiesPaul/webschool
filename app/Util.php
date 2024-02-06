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

    public static function generateLinks($nivel = '', $perfil = false)
    {
        session_start();
        $links = '';

        if (isset($_SESSION['tipo'])) {
            $tipo = $_SESSION['tipo'];

            $path = ($perfil) ? $tipo . "/" . $nivel : $nivel;


            if ($tipo == Enum::TIPO_ADMIN) {
                $links = "
                    <a class='btn btn-light btn btn-block' href='".$path."turmas'>Turmas</a>
                    <a class='btn btn-light btn btn-block' href='".$path."disciplinas'>Disciplinas</a>
                    <a class='btn btn-light btn btn-block' href='".$path."professores'>Professores</a>
                    <a class='btn btn-light btn btn-block' href='".$path."alunos'>Alunos</a>
                    <a class='btn btn-light btn btn-block' href='".$path."responsaveis'>Responsáveis</a>
                ";
            }
            if ($tipo == Enum::TIPO_RESPONSAVEL) {
                $links = "
                    <a class='btn btn-light btn btn-block' href='".$path."alunos'>Alunos</a>
                ";
            }
            if ($tipo == Enum::TIPO_PROFESSOR) {
                $links = "
                    <a class='btn btn-light btn btn-block' href='".$path."turmas'>Turmas</a>
                ";
            }
            if ($tipo == Enum::TIPO_ALUNO) {
                $links = "
                    <a class='btn btn-light btn btn-block' href='".$path."turmas'>Turmas</a>
                ";
            }
        }

        return $links;
    }
}
