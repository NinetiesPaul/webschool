<?php

namespace App\Controllers;

use App\Templates;
use App\DB\DB;
use PDO;
use App\Util;

class AlunoController {
    
    protected $template;
    
    protected $connection;
    
    protected $util;

    public function __construct()
    {
        $this->template = new Templates();
        $this->connection = new DB;
        $this->util = new Util();
        $this->util->userPermission('aluno');
    }
    
    public function index()
    {
        $nome = $_SESSION['user']->nome;
        
        $args = [
            'LOGADO' => $nome
        ];
        
        $template 	= $this->template->getTemplate('aluno/index.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verTurmas()
    {
        $user = $_SESSION['user'];
        
        $usuarioQuery = $this->connection->query("select turma from aluno where usuario = $user->id");
        $usuario = $usuarioQuery->fetch(PDO::FETCH_OBJ);

        $turmasQuery = $this->connection->query("select turma from nota_por_aluno where aluno=$user->aluno group by turma");
        $turmas = $turmasQuery->fetchAll(PDO::FETCH_OBJ);

        $minhasTurmas = '';
        foreach ($turmas as $turma) {
            $turmaAtual = ($usuario->turma === $turma->turma) ? ' (<b>atual</b>) ' : '';

            $minhasTurmas .= $this->util->pegarTurmaDoAlunoPorTurma($turma->turma) . ' ' . $turmaAtual;
            
            $minhasTurmas .=
                "<button class='btn btn-sm btn-info boletim' id='$user->aluno.$turma->turma'>
                    <span class='glyphicon glyphicon-save-file'></span> Baixar boletim</a>
                </button><br/>";

            $notasQuery = $this->connection->query("select * from nota_por_aluno where aluno=$user->aluno and turma=$turma->turma order by disciplina");
            $notas = $notasQuery->fetchAll(PDO::FETCH_OBJ);

            $minhasTurmas .= "<table style='margin-left: auto; margin-right: auto; font-size: 13;' class='table'>
            <thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th><th></th></tr></thead><tbody>";
            
            foreach ($notas as $nota) {
                $minhasTurmas .= "<tr><td>".$this->util->pegarNomeDaDisciplina($nota->disciplina)."</td>
                <td>$nota->nota1</td>
                <td>$nota->rec1</td>
                <td>$nota->nota2</td>
                <td>$nota->rec2</td>
                <td>$nota->nota3</td>
                <td>$nota->rec3</td>
                <td>$nota->nota4</td>
                <td>$nota->rec4</td>
                <td>
                    <button class='btn btn-sm btn-info faltas' data-toggle='modal' data-target='#modalExemplo' id='$user->aluno.$turma->turma.$nota->disciplina'>
                        <span class='glyphicon glyphicon-eye-open'></span> Visualizar faltas
                    </button>
                </td>
                </tr>";
            }
            $minhasTurmas .= '</tbody></table>';
        }
        
        $args = [
            'ALUNOID_USERID' => $user->aluno.'.'.$user->id,
            'LOGADO' => $user->nome,
            'TURMAS' => $minhasTurmas
        ];
        
        $template 	= $this->template->getTemplate('aluno/turmas.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
}
