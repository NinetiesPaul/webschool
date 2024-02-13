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
    protected $responsavelStorage;
    protected $notaStorage;
    protected $alunoStorage;
    protected $materiaStorage;
    protected $turmaStorage;

    public function __construct()
    {
        Util::userPermission(Enum::TIPO_RESPONSAVEL);

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

        new Templates('responsavel/index.html', $args);
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
            'ALUNOS' => $alunos
        ];

        new Templates('responsavel/alunos.html', $args);
    }
    
    public function verAluno($idAluno)
    {
        $user = $_SESSION['user'];

        $turmas = $this->notaStorage->verTurmasComNotaDoAluno($idAluno);

        $minhasTurmas = '';
        foreach ($turmas as $turma) {
            $minhasTurmas .= "<p><a href='$idAluno/turma/$turma->turma' class='btn btn-sm btn-primary'>$turma->nome_turma</a></p>";
        }
        
        $args = [
            'LOGADO' => $user->nome,
            'ALUNO' => $idAluno,
            'TURMAS' => $minhasTurmas
        ];

        new Templates('responsavel/aluno.html', $args, '../');
    }
    
    public function verTurmaDoAluno($idAluno, $idTurma)
    {
        $user = $_SESSION['user'];

        $aluno = $this->alunoStorage->pegarNomeDoAlunoPorAlunoId($idAluno);

        $turma = $this->turmaStorage->verTurma($idTurma);
        
        $notasQuery = $this->notaStorage->verNotasPorTruma($idAluno, $idTurma);
        
        $notas = '';
        foreach ($notasQuery as $nota) {
            $notas .= "
                <tr>
                    <td>$nota->materia<br><small>$nota->nome_professor</small></td>
                    <td>$nota->nota1</td>
                    <td>$nota->rec1</td>
                    <td>$nota->nota2</td>
                    <td>$nota->rec2</td>
                    <td>$nota->nota3</td>
                    <td>$nota->rec3</td>
                    <td>$nota->nota4</td>
                    <td>$nota->rec4</td>
                    <td>
                        <button class='btn btn-sm btn-info faltas' data-toggle='modal' data-target='#modalExemplo' data-aluno='$idAluno' data-turma='$idTurma' data-disciplina='$nota->disciplina'>
                            <span class='glyphicon glyphicon-eye-open'></span> Diario de Classe
                        </button>
                    </td>
                </tr>
            ";
        }
        
        $args = [
            'LOGADO' => $user->nome,
            'ALUNO' => $aluno,
            'TURMA' => "$turma->nome ($turma->ano)",
            'NOTAS' => $notas,
            'ALUNO_PDF' => $idAluno,
            'TURMA_PDF' => $idTurma
        ];

        new Templates('responsavel/turma_aluno.html', $args,);
    }
}
