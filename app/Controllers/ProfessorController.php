<?php

namespace App\Controllers;

use App\DB\Storage\MateriaStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\ArquivoStorage;
use App\Enum;
use App\Templates;
use App\Util;
use DateTime;

class ProfessorController
{
    protected $template;
    protected $util;
    protected $materiaStorage;
    protected $professorStorage;
    protected $diarioDeClasseStorage;
    protected $arquivoStorage;
    protected $notaStorage;
    protected $links;

    public function __construct()
    {
        $this->template = new Templates();
        $this->util = new Util();
        $this->util->userPermission(Enum::TIPO_PROFESSOR);
        $this->links = $this->util->generateLinks();

        $this->materiaStorage = new MateriaStorage();
        $this->professorStorage = new ProfessorStorage();
        $this->diarioDeClasseStorage = new DiarioDeClasseStorage();
        $this->arquivoStorage = new ArquivoStorage();
        $this->notaStorage = new NotaStorage();
    }
    
    public function index()
    {
        $user = $_SESSION['user'];
        
        $args = [
            'LOGADO' => $user->nome
        ];

        $this->util->loadTemplate('professor/index.html', $args);
    }
    
    public function verTurmas()
    {
        $user = $_SESSION['user'];

        $disciplinas = $this->materiaStorage->verMateriaPorProfessorDoProfessor($user->professor);

        $turmas = '';
        
        foreach ($disciplinas as $disciplina) {
            $turmas .= $disciplina->nomeDisciplina.', '.$disciplina->serie.'º Série '.$disciplina->nome;
            $turmas .= "<p/><a href='turma/$disciplina->id' class='btn btn-sm btn-primary' id='btn_disciplina' '><span class='glyphicon glyphicon-eye-open'></span> Visualizar</a><br/>";

            $alunosQuery = $this->professorStorage->verMeusAlunos($disciplina->turma, $disciplina->disciplina, $user->professor);

            $text = '<br/>Sem alunos cadastrados nessa turma no momento';
            if (!empty($alunosQuery)) {
                $text = '';
                foreach ($alunosQuery as $aluno) {
                    $text .= $aluno->nome . '<br/>';
                }
            }
            
            $turmas .= $text;
        }
                    
        $args = [
            'LOGADO' => $user->nome,
            'TURMAS' => $turmas,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('professor/turmas.html', $args);
    }
    
    public function verTurma($id)
    {
        $this->links = $this->util->generateLinks('../');

        $user = $_SESSION['user'];
        $result = $this->materiaStorage->verMateriaPorProfessorSingle($id);

        $disciplina = $result->disciplina;
        $turma = $result->turma;

        $detalhes = '';
        
        $detalhes .= $result->nomeDisciplina.', '.$result->serie.'º Série '.$result->nome;
        $detalhes .= "<a href='../diariodeclasse/$turma"."_"."$disciplina' class='btn btn-sm btn-primary' id='btn_diario'><span class='glyphicon glyphicon-pencil'></span> Diário de classe</a><p/>";

        $alunosQuery = $this->notaStorage->verNotasPorAlunosDaDisciplinaETurma($disciplina, $turma);

        $detalhes .= "<table style='margin-left: auto; margin-right: auto; font-size: 13px;' class='table table-sm table-hover table-striped'>
        <thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2:</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th></tr></thead><tbody>";
        foreach ($alunosQuery as $aluno) {
            $detalhes .= "<tr><td>$aluno->nome</td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota1'>$aluno->nota1</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec1'>$aluno->rec1</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota2'>$aluno->nota2</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec2'>$aluno->rec2</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota3'>$aluno->nota3</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec3'>$aluno->rec3</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota4'>$aluno->nota4</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec4'>$aluno->rec4</a> </td>
            </tr>";
        }
        
        $detalhes .= '</tbody></table>';
        
        $args = [
            'LOGADO' => $user->nome,
            'DETALHES' => $detalhes,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('professor/turma.html', $args);
    }
    
    public function verDiarioDeClasse($id)
    {
        $this->links = $this->util->generateLinks('../');

        $user = $_SESSION['user'];
        
        $id = explode('_', $id);
        
        $args = [
            'LOGADO' => $user->nome,
            'DISCIPLINA' => $id[1],
            'TURMA' => $id[0],
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('professor/diariodeclasse.html', $args);
    }
}
