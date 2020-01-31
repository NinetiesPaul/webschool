<?php

namespace App\Controllers;

use App\Templates;
use PDO;
use App\Util;

use App\DB\Storage\TurmaStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\AlunoStorage;
use App\DB\Storage\UsuarioStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\ResponsavelStorage;

class AdminController
{
    protected $template;
        
    protected $util;
    
    protected $turmaStorage;
    
    protected $materiaStorage;
    
    protected $alunoStorage;
    
    protected $enderecoStorage;
    
    protected $usuarioStorage;
    
    protected $professorStorage;
    
    protected $responsavelStorage;

    public function __construct()
    {
        $this->template = new Templates();
        $this->util = new Util();
        $this->util->userPermission('admin');
        
        $this->turmaStorage = new TurmaStorage();
        $this->materiaStorage = new MateriaStorage();
        $this->alunoStorage = new AlunoStorage();
        $this->enderecoStorage = new EnderecoStorage();
        $this->usuarioStorage = new UsuarioStorage();
        $this->professorStorage = new ProfessorStorage();
        $this->responsavelStorage = new ResponsavelStorage();
    }
    
    public function index()
    {
        $templateFinal 	= $this->template->getTemplate('admin/index.html');
        echo $templateFinal;
    }
    
    public function verAlunos()
    {
        $turmaQuery = $this->turmaStorage->verTurmas();
        
        $turmas = '';
        
        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
        }
        
        $alunoQuery = $this->alunoStorage->verAlunos();
        
        $alunos = '';
        
        foreach ($alunoQuery as $aluno) {
            $is_deleted = ($aluno->is_deleted) ? "<span class='glyphicon glyphicon-remove'></span> " : "<span class='glyphicon glyphicon-ok'></span> ";
            $alunos .=
            "<tr id='row-$aluno->id'><td>$aluno->nome </td>
            <td>".$this->util->pegarTurmaDoAlunoPorUsuario($aluno->id)."</td>
            <td>$is_deleted</td><td><a href='aluno/$aluno->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a> </td>
            <td><button class='btn btn-danger btn-sm' id='deletar' value='$aluno->id'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
            'TURMAS' => $turmas,
            'ALUNOS' => $alunos
        ];
        
        $template 	= $this->template->getTemplate('admin/alunos.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verAluno(int $idAluno)
    {
        $aluno = $this->alunoStorage->verAluno($idAluno);
        $turmaAtual = $this->util->pegarIdDaTurmaDoAlunoPorAlunoId($aluno->aluno);
        $turmaQuery = $this->turmaStorage->verTurmas();
        
        $turmas = '';
        
        foreach ($turmaQuery as $turma) {
            $selected = ($turmaAtual == $turma->id)? 'selected' : '';
            $turmas .= "<option value='$turma->id' $selected>$turma->serie º Série $turma->nome</option>";
        }
        
        
        $args = [
            'ID' => $aluno->id,
            'IDALUNO' => $aluno->aluno,
            'NOME' => $aluno->nome,
            'SALT' => $aluno->salt,
            'TURMAATUAL' =>  $turmaAtual,
            'EMAIL' => $aluno->email,
            'TURMAS' => $turmas
        ];
        
        $template 	= $this->template->getTemplate('admin/aluno.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function adicionarAluno()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $email = $data['email'];
        $nome = $data['nome'];
        $salt = time() + rand(100, 1000);
        $password = $data['password'];
        $password = md5($password . $salt);
        $turma = $data['turma'];
        
        $this->alunoStorage->adicionarAluno($email, $nome, $password, $salt, $turma);
        header('Location: /webschool/admin/alunos');
    }
    
    public function atualizarAluno()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $userId = $data['id'];
        $idAluno = $data['idAluno'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];
        $turma = $data['turma']; 
        
        $this->alunoStorage->alterarAluno($userId, $idAluno, $nome, $email, $password, $salt, $turma);
        header('Location: /webschool/admin/alunos');
    }
    
    public function removerAluno($idAluno)
    {   
        $this->alunoStorage->removerAluno($idAluno);
    }
    
    public function verProfessores()
    {
        $professorQuery = $this->professorStorage->verProfessores();
        
        $professores = '';
        $professores_select = '';
        $professor_array = [];
        
        foreach ($professorQuery as $professor) {
            $is_deleted = ($professor->is_deleted) ? "<span class='glyphicon glyphicon-remove'></span> " : "<span class='glyphicon glyphicon-ok'></span> ";
            $professores .=
            "<tr id='row-$professor->id'><td>$professor->nome </td>
            <td>$is_deleted</td><td><a href='professor/$professor->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a> </td>
            <td><button class='btn btn-danger btn-sm' id='deletar' value='$professor->id'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
            $professores_select .= "<option value='$professor->professor'>$professor->nome</option>";
            $professor_array[$professor->professor] = $professor->nome;
        }
        
        $turmaQuery = $this->turmaStorage->verTurmas();
        
        $turmas = '';
        $turma_array = [];
        
        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
            $turma_array[$turma->id] = "$turma->serie º Série $turma->nome";
        }

        $disciplinaQuery = $this->materiaStorage->verMaterias();
        
        $disciplinas = '';
        $disciplina_array = [];
        
        foreach ($disciplinaQuery as $disciplina) {
            $disciplinas .= "<option value='$disciplina->id'>$disciplina->nome</option>";
            $disciplina_array[$disciplina->id] = $disciplina->nome;
        }

        $disciplinaPorProfessorQuery = $this->materiaStorage->verMateriaPorProfessor();
        
        $disciplinasProProfessor = '';
        
        foreach ($disciplinaPorProfessorQuery as $disciplinaProProfessor) {
            $dpp = $disciplinaProProfessor->professor;
            $dpd = $disciplinaProProfessor->disciplina;
            $dpt = $disciplinaProProfessor->turma;
            $disciplinasProProfessor .=
            "<tr id='row-dpp-$disciplinaProProfessor->id'><td> $professor_array[$dpp] </td>
            <td>$disciplina_array[$dpd]</td><td>$turma_array[$dpt]</td>
            <td><button class='btn btn-danger btn-sm' id='deletar-dpp' value='$disciplinaProProfessor->id'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
            'PROFESSORES' => $professores,
            'PROFESSORES_SELECT' => $professores_select,
            'TURMAS_SELECT' => $turmas,
            'DISCIPLINAS_SELECT' => $disciplinas,
            'DISCIPLINAS_POR_PROFESSOR' => $disciplinasProProfessor
        ];
        
        $template 	= $this->template->getTemplate('admin/professores.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verProfessor(int $idProfessor)
    {
        $professor = $this->professorStorage->verProfessor($idProfessor);
        
        $args = [
            'ID' => $professor->id,
            'NOME' => $professor->nome,
            'SALT' => $professor->salt,
            'EMAIL' => $professor->email
        ];
        
        $template = $this->template->getTemplate('admin/professor.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function adicionarProfessor()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $email = $data['email'];
        $nome = $data['nome'];
        $salt = time() + rand(100, 1000);
        $password = $data['password'];
        $password = md5($password . $salt);
        
        $this->professorStorage->adicionarProfessor($email, $nome, $password, $salt);
        header('Location: /webschool/admin/professores');
    }
    
    public function atualizarProfessor()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $userId = $data['id'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];
        
        $this->professorStorage->alterarProfessor($userId, $nome, $email, $password, $salt);
        header('Location: /webschool/admin/professores');
    }
    
    public function removerProfessor(int $idProfessor)
    {
        $this->professorStorage->removerProfessor($idProfessor);
    }
    
    public function adicionarProfessorPorMateria()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $disciplina = $data['disciplina'];
        $turma = $data['turma'];
        $professor = $data['professor'];
        
        $this->professorStorage->adicionarMateriaPorProfessor($disciplina, $turma, $professor);
        header("Location: /webschool/admin/professores");
    }
    
    public function removerProfessorPorMateria(int $id)
    {
        $this->professorStorage->removerProfessorPorMateria($id);
    }
    
    public function verResponsaveis()
    {   
        $responsavelQuery = $this->responsavelStorage->verResponsaveis();
        
        $responsaveis = '';
        
        foreach ($responsavelQuery as $responsavel) {
            $is_deleted = ($responsavel->is_deleted) ? "<span class='glyphicon glyphicon-remove'></span> " : "<span class='glyphicon glyphicon-ok'></span> ";
            $responsaveis .=
             "<tr id='row-$responsavel->id'><td>$responsavel->nome </td>
             <td>$is_deleted</td><td><a href='responsavel/$responsavel->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a> </td>
             <td><button class='btn btn-danger btn-sm' id='deletar' value='$responsavel->id'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
          'RESPONSAVEIS' => $responsaveis
        ];
        
        $template 	= $this->template->getTemplate('admin/responsaveis.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verResponsavel(int $idResponsavel)
    {
        $responsavel = $this->responsavelStorage->verResponsavel($idResponsavel);
        
        $alunosQuery = $this->alunoStorage->verAlunos();

        $alunos = '';
        foreach ($alunosQuery as $aluno) {
            $alunos .= "<option value='$aluno->aluno'>$aluno->nome (".$this->util->pegarTurmaDoAlunoPorUsuario($aluno->id).")</option>";
        }
        
        $alunosDoResponsavel = $this->alunoStorage->verAlunosDoResponsavel($responsavel->responsavel);

        $filhos = '';
        foreach ($alunosDoResponsavel as $user) {
            $filhos .= "<tr id='row-$user->rpa'><td>$user->nome</td><td>".$this->util->pegarTurmaDoAlunoPorUsuario($user->id)."</td>
            <td><button class='btn btn-danger btn-sm' id='deletar' value='$user->rpa'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
            'ID' => $responsavel->id,
            'NOME' => $responsavel->nome,
            'SALT' => $responsavel->salt,
            'RESPONSAVEL' => $responsavel->responsavel,
            'EMAIL' => $responsavel->email,
            'ALUNOS' => $alunos,
            'FILHOS' => $filhos
        ];
        
        $template = $this->template->getTemplate('admin/responsavel.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function adicionarResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $email = $data['email'];
        $nome = $data['nome'];
        $password = $data['password'];
        $salt = time() + rand(100, 1000);
        $password = md5($password . $salt);
        
        $this->responsavelStorage->adicionarResponsavel($email, $nome, $password, $salt);
        header('Location: /webschool/admin/responsaveis');
    }
    
    public function atualizarResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $userId = $data['id'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];
        
        $this->responsavelStorage->alterarResponsavel($userId, $nome, $email, $password, $salt);
        header('Location: /webschool/admin/responsaveis');
    }
    
    public function removerResponsavel(int $idResponsavel)
    {
        $this->responsavelStorage->removerResponsavel($idResponsavel);
    }
    
    public function adicionarAlunoPorResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $responsavel = $data['responsavel'];
        $aluno = $data['aluno'];
        $id = $data['id'];
        
        $responsavel = $this->responsavelStorage->adicionarAlunoPorResponsavel($responsavel, $aluno, $id);
        header("Location: /webschool/admin/responsavel/$responsavel");
    }
    
    public function removerAlunoPorResponsavel(int $id)
    {
        $this->responsavelStorage->removerAlunoPorResponsavel($id);
    }
    
    public function verTurmas()
    {
        $turmasQuery = $this->turmaStorage->verTurmas();
        
        $turmas = '';
        
        foreach ($turmasQuery as $turma) {
            $turmas .=
             "<tr id='row-$turma->id'><td>$turma->serie º Série $turma->nome</td>
             <td><a href='turma/$turma->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>
             <td><button class='btn btn-danger btn-sm' id='deletar' value='$turma->id'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
          'ALUNOS' => $turmas
        ];
        
        $template = $this->template->getTemplate('admin/turmas.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verTurma(int $turma)
    {
        $turma = $this->turmaStorage->verTurma($turma);
        
        $args = [
            'ID' => $turma->id,
            'TURMA' => $turma->serie,
            'LETRA' => $turma->nome,
        ];
        
        $template 	= $this->template->getTemplate('admin/turma.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function adicionarTurma()
    {   
        $data = json_decode(json_encode($_POST), true);
        
        $nome = $data['nome'];
        $serie = $data['serie'];
        
        $this->turmaStorage->adicionarTurma($nome, $serie);
        header('Location: /webschool/admin/turmas');
    }
    
    public function atualizarTurma()
    {  
        $data = json_decode(json_encode($_POST), true);
        
        $nome = $data['nome'];
        $serie = $data['serie'];
        $turma = $data['turma'];
        
        
        $this->turmaStorage->alterarTurma($nome, $serie, $turma);
        header('Location: /webschool/admin/turmas');
    }
    
    public function removerTurma(int $turma)
    {
        $this->turmaStorage->removerTurma($turma);
    }
    
    public function verMaterias()
    {
        $disciplinaQuery = $this->materiaStorage->verMaterias();
        
        $disciplinas = '';
        
        foreach ($disciplinaQuery as $disciplina) {
            $disciplinas .=
             "<tr id='row-$disciplina->id'><td>$disciplina->nome</td>
             <td><a href='disciplina/$disciplina->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>
             <td><button class='btn btn-danger btn-sm' id='deletar' value='$disciplina->id'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
          'DISCIPLINAS' => $disciplinas
        ];
        
        $template 	= $this->template->getTemplate('admin/disciplinas.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verMateria(int $materia)
    {
        $disciplina = $this->materiaStorage->verMateria($materia);
        
        $args = [
            'ID' => $disciplina->id,
            'NOME' => $disciplina->nome,
        ];
        
        $template 	= $this->template->getTemplate('admin/disciplina.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function adicionarMateria()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $nome = $data['nome'];
        
        $this->materiaStorage->adicionarMateria($nome);
        header('Location: /webschool/admin/disciplinas');
    }
    
    public function atualizarMateria()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $nome = $data['nome'];
        $id = $data['id'];
        
        $this->materiaStorage->alterarMateria($nome, $id);           
        header('Location: /webschool/admin/disciplinas');
    }
    
    public function removerMateria(int $disciplina)
    {
        $this->materiaStorage->removerMateria($disciplina);
    }
}
