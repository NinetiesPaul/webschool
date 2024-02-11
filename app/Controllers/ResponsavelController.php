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

        $aluno = '';
        foreach ($turmas as $turma) {
            $aluno = $turma->nome;
            $notas = $turma->nome_turma;
            
            $notas .=
                "<p>
                    <button class='btn btn-sm btn-info boletim' id='$idAluno.$turma->turma'>
                        <span class='glyphicon glyphicon-save-file'></span> Baixar boletim</a>
                    </button>
                </p>";
            
            $notasQuery = $this->notaStorage->verNotasPorTruma($idAluno, $turma->turma);
          
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
                            <button class='btn btn-sm btn-info faltas' data-toggle='modal' data-target='#modalExemplo' id='$idAluno.$turma->turma.$nota->disciplina'>
                                <span class='glyphicon glyphicon-eye-open'></span> Diario de Classe
                            </button>
                        </td>
                    </tr>
                ";
            }
        }
        
        $args = [
            'LOGADO' => $user->nome,
            'ALUNOID_USERID' => $idAluno.'.'.$user->id,
            'ALUNO' => $aluno,
            'NOTAS' => $notas,
        ];

        new Templates('responsavel/aluno.html', $args, '../');
    }
}
