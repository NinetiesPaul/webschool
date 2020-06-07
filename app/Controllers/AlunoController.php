<?php

namespace App\Controllers;

use App\DB\Storage\AlunoStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\TurmaStorage;
use App\Templates;
use App\Util;
use App\Enum;

class AlunoController
{
    protected $template;
    protected $util;
    protected $alunoStorage;
    protected $turmaStorage;
    protected $notaStorage;
    protected $materiaStorage;
    protected $links;

    public function __construct()
    {
        $this->template = new Templates();
        $this->util = new Util();
        $this->util->userPermission(Enum::TIPO_ALUNO);
        $this->links = $this->util->generateLinks();

        $this->alunoStorage = new AlunoStorage();
        $this->turmaStorage = new TurmaStorage();
        $this->notaStorage = new NotaStorage();
        $this->materiaStorage = new MateriaStorage();
    }
    
    public function index()
    {
        $nome = $_SESSION['user']->nome;
        
        $args = [
            'LOGADO' => $nome
        ];
        
        $this->util->loadTemplate('aluno/index.html', $args);
    }
    
    public function verTurmas()
    {
        $user = $_SESSION['user'];

        $turmas = $this->notaStorage->verTurmasComNotaDoAluno($user->aluno);

        $minhasTurmas = '';
        foreach ($turmas as $turma) {
            $turmaAtual = ($user->turma_atual === $turma->turma) ? ' (<b>atual</b>) ' : '';

            $minhasTurmas .= $turma->nome_turma . ' ' . $turmaAtual;
            
            $minhasTurmas .=
                "<br/><button class='btn btn-sm btn-info boletim' id='$user->aluno.$turma->turma'>
                    <span class='glyphicon glyphicon-save-file'></span> Baixar boletim</a>
                </button><br/>";

            $notas = $this->notaStorage->verNotasPorTruma($user->aluno, $turma->turma);

            $minhasTurmas .= "<table style='margin-left: auto; margin-right: auto; font-size: 13px;' class='table'>
            <thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th><th></th></tr></thead><tbody>";
            
            foreach ($notas as $nota) {
                $minhasTurmas .= "<tr><td>$nota->materia<br><small>$nota->nome_professor</small></td>
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
                        <span class='glyphicon glyphicon-eye-open'></span> Diario de Classe
                    </button>
                </td>
                </tr>";
            }
            $minhasTurmas .= '</tbody></table>';
        }
        
        $args = [
            'ALUNOID_USERID' => $user->aluno.'.'.$user->id,
            'LOGADO' => $user->nome,
            'TURMAS' => $minhasTurmas,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('aluno/turmas.html', $args);
    }
}
