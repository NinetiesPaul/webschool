<?php

namespace App\Controllers;

use App\DB\Storage\AlunoStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\TurmaStorage;
use App\Enum;
use App\Templates;
use App\Util;
use App\DB\Storage\ResponsavelStorage;
use App\DB\Storage\NotaStorage;

class ResponsavelController
{
    protected $template;
    protected $util;
    protected $responsavelStorage;
    protected $notaStorage;
    protected $alunoStorage;
    protected $materiaStorage;
    protected $turmaStorage;
    protected $links;

    public function __construct()
    {
        $this->template = new Templates();
        $this->util = new Util();
        $this->util->userPermission(Enum::TIPO_RESPONSAVEL);
        $this->links = $this->util->generateLinks();

        $this->responsavelStorage = new ResponsavelStorage();
        $this->notaStorage = new NotaStorage();
        $this->alunoStorage = new AlunoStorage();
        $this->materiaStorage = new MateriaStorage();
        $this->turmaStorage = new TurmaStorage();
    }
    
    public function index()
    {
        $user = $_SESSION['user'];
        
        $args = [
            'LOGADO' => $user->nome
        ];

        $this->util->loadTemplate('responsavel/index.html', $args);
    }
    
    public function verAlunos()
    {
        $user = $_SESSION['user'];
        
        $meusAlunosQuery = $this->responsavelStorage->verAlunosDoResponsavel($user->responsavel);
        
        $alunos = '';
        foreach ($meusAlunosQuery as $aluno) {
            $alunos .= "<a href='aluno/$aluno->aluno'>".$this->alunoStorage->pegarNomeDoAlunoPorAlunoId($aluno->aluno)."</a><br/>";
        }
        
        $args = [
            'LOGADO' => $user->nome,
            'ALUNOS' => $alunos,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('responsavel/alunos.html', $args);
    }
    
    public function verAluno($idAluno)
    {
        $this->links = $this->util->generateLinks('../');

        $user = $_SESSION['user'];
        
        $turmas = $this->notaStorage->verTurmasComNotaDoAluno($idAluno);

        $notas = '';
        
        foreach ($turmas as $turma) {
            $turmaAtual = ($this->alunoStorage->pegarIdDaTurmaDoAlunoPorAlunoId($idAluno) === $turma->turma) ? ' (<b>atual</b>) ' : '';

            $notas .= $turma->nome_turma . ' ' . $turmaAtual;
            
            $notas .=
                "<button class='btn btn-sm btn-info boletim' id='$idAluno.$turma->turma'>
                    <span class='glyphicon glyphicon-save-file'></span> Baixar boletim</a>
                </button><br/>";
            
            $notasQuery = $this->notaStorage->verNotasPorTruma($idAluno, $turma->turma);

            $notas .= "<table style='margin-left: auto; margin-right: auto; font-size: 13px;' class='table'>
            <thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th><th></th></tr></thead><tbody>";
            
            foreach ($notasQuery as $nota) {
                $notas .= "<tr><td>$nota->materia<br><small>$nota->nome_professor</small></td>
                <td>$nota->nota1</td>
                <td>$nota->rec1</td>
                <td>$nota->nota2</td>
                <td>$nota->rec2</td>
                <td>$nota->nota3</td>
                <td>$nota->rec3</td>
                <td>$nota->nota4</td>
                <td>$nota->rec4</td>
                <td>
                    <button class='btn btn-sm btn-info faltas' data-toggle='modal' data-target='#modalExemplo' id='$idAluno.$turma->turma.$nota->disciplina'>
                        <span class='glyphicon glyphicon-eye-open'></span> Diario de Classe
                    </button>
                </td></tr>";
            }
            
            $notas .= '</tbody></table>';
        }
        
        $args = [
            'LOGADO' => $user->nome,
            'ALUNOID_USERID' => $idAluno.'.'.$user->id,
            'NOTAS' => $notas,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('responsavel/aluno.html', $args);
    }
}
