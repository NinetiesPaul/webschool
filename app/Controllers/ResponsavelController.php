<?php

namespace App\Controllers;

use App\Templates;
use App\DB\DB;
use PDO;
use App\Util;

class ResponsavelController
{
    protected $template;
    
    protected $connection;
    
    protected $util;

    public function __construct()
    {
        $this->template = new Templates();
        $this->connection = new DB;
        $this->util = new Util();
        $this->util->userPermission('responsavel');
    }
    
    public function index()
    {
        $user = $_SESSION['user'];
        
        $args = [
            'LOGADO' => $user->nome
        ];
        
        $template 	= $this->template->getTemplate('responsavel/index.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verAlunos()
    {
        $user = $_SESSION['user'];
        
        $meusAlunosQuery = $this->connection->query("select * from responsavel_por_aluno where responsavel = $user->responsavel");
        $meusAlunosQuery = $meusAlunosQuery->fetchAll(PDO::FETCH_OBJ);
        
        $alunos = '';

        foreach ($meusAlunosQuery as $aluno) {
            $alunos .= "<a href='aluno/$aluno->aluno'>".$this->util->pegarNomeDoAlunoPorAlunoId($aluno->aluno)."</a><br/>";
        }
        
        $args = [
            'LOGADO' => $user->nome,
            'ALUNOS' => $alunos
        ];
        
        $template = $this->template->getTemplate('responsavel/alunos.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verAluno(int $idAluno)
    {
        $user = $_SESSION['user'];
        
        $turmasQuery = $this->connection->query("select turma from nota_por_aluno where aluno=$idAluno group by turma");
        $turmas = $turmasQuery->fetchAll(PDO::FETCH_OBJ);

        $notas = '';
        
        foreach ($turmas as $turma) {
            $turmaAtual = ($this->util->pegarIdDaTurmaDoAlunoPorAlunoId($idAluno) === $turma->turma) ? ' (<b>atual</b>) ' : '';

            $notas .= $this->util->pegarTurmaDoAlunoPorTurma($turma->turma) . ' ' . $turmaAtual;
            
            $notas .= 
                "<button class='btn btn-sm btn-info boletim' id='$idAluno.$turma->turma'>
                    <span class='glyphicon glyphicon-save-file'></span> Baixar boletim</a>
                </button><br/>";
            
            $notasQuery = $this->connection->query("select * from nota_por_aluno where aluno=$idAluno and turma=$turma->turma order by disciplina");
            $notasQuery = $notasQuery->fetchAll(PDO::FETCH_OBJ);

            $notas .= "<table style='margin-left: auto; margin-right: auto; font-size: 13;' class='table'>
            <thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th><th></th></tr></thead><tbody>";
            
            foreach ($notasQuery as $nota) {
                $notas .= "<tr><td>".$this->util->pegarNomeDaDisciplina($nota->disciplina)."</td>
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
            'NOTAS' => $notas
        ];
        
        $template = $this->template->getTemplate('responsavel/aluno.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;        
    }
}
