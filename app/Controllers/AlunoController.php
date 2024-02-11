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
    protected $alunoStorage;
    protected $turmaStorage;
    protected $notaStorage;
    protected $materiaStorage;

    public function __construct()
    {
        Util::userPermission(Enum::TIPO_ALUNO);

        $this->alunoStorage = new AlunoStorage();
        $this->turmaStorage = new TurmaStorage();
        $this->notaStorage = new NotaStorage();
        $this->materiaStorage = new MateriaStorage();
    }
    
    public function index()
    {
        $nome = $_SESSION['user']->nome;
                
        new Templates('aluno/index.html', [
            'LOGADO' => $nome
        ]);
    }
    
    public function verTurmas()
    {
        $user = $_SESSION['user'];

        $turmas = $this->notaStorage->verTurmasComNotaDoAluno($user->aluno);

        $minhasTurmas = '';
        foreach ($turmas as $turma) {
            $minhasTurmas .= $turma->nome_turma;
            
            $minhasTurmas .=
                "<p><button class='btn btn-sm btn-info boletim' id='$user->aluno.$turma->turma'>
                    <span class='glyphicon glyphicon-save-file'></span> Baixar boletim</a>
                </button></p>";

            $notas = $this->notaStorage->verNotasPorTruma($user->aluno, $turma->turma);
            
            foreach ($notas as $nota) {
                $minhasTurmas .= "
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
                            <button class='btn btn-sm btn-info faltas' data-toggle='modal' data-target='#modalExemplo' id='$user->aluno.$turma->turma.$nota->disciplina'>
                                <span class='glyphicon glyphicon-eye-open'></span> Diario de Classe
                            </button>
                        </td>
                    </tr>
                ";
            }
        }
        
        $args = [
            'ALUNOID_USERID' => $user->aluno.'.'.$user->id,
            'LOGADO' => $user->nome,
            'TURMAS' => $minhasTurmas,
        ];

        new Templates('aluno/turmas.html', $args);
    }
}
