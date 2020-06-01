<?php

namespace App\Controllers;

use App\Enum;
use App\Templates;
use App\Util;
use App\DB\Storage\TurmaStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\AvatarStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\ArquivoStorage;
use App\DB\Storage\AlunoStorage;
use App\DB\Storage\UsuarioStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\ResponsavelStorage;
use App\DB\Storage\LogStorage;

class AdminController
{
    protected $template;
    protected $util;
    protected $turmaStorage;
    protected $materiaStorage;
    protected $alunoStorage;
    protected $notaStorage;
    protected $diarioStorage;
    protected $enderecoStorage;
    protected $avatarStorage;
    protected $arquivoStorage;
    protected $usuarioStorage;
    protected $professorStorage;
    protected $responsavelStorage;
    protected $links;

    public function __construct()
    {
        $this->template = new Templates();
        $this->util = new Util();
        $this->util->userPermission(Enum::TIPO_ADMIN);
        $this->links = $this->util->generateLinks();
        new LogStorage();
        
        $this->turmaStorage = new TurmaStorage();
        $this->materiaStorage = new MateriaStorage();
        $this->alunoStorage = new AlunoStorage();
        $this->enderecoStorage = new EnderecoStorage();
        $this->avatarStorage = new AvatarStorage();
        $this->notaStorage = new NotaStorage();
        $this->diarioStorage = new DiarioDeClasseStorage();
        $this->arquivoStorage = new ArquivoStorage();
        $this->usuarioStorage = new UsuarioStorage();
        $this->professorStorage = new ProfessorStorage();
        $this->responsavelStorage = new ResponsavelStorage();
    }
    
    public function index()
    {
        $this->util->loadTemplate('admin/index.html');
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
            $is_deleted = ($aluno->is_deleted) ? "<span class='label-status_$aluno->id label-success'>Ativo</span>" : "<span class='label-status_$aluno->id label-danger'>Inativo</span>";
            $alunos .=
            "<tr id='row-$aluno->id'><td>$aluno->nome </td>
            <td>".$this->turmaStorage->pegarTurmaDoAlunoPorUsuario($aluno->id)."</td>
            <td>$is_deleted</td>
            <td><a href='aluno/$aluno->id' class='btn'><span class='glyphicon glyphicon-edit'></span></a>
            <a href='#' class='btn desativar' id='$aluno->id'><span class='glyphicon glyphicon-ban-circle'></span> </a></td></tr>";
        }
        
        $args = [
            'TURMAS' => $turmas,
            'ALUNOS' => $alunos,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/alunos.html', $args);
    }
    
    public function verAluno($idAluno)
    {
        $this->links = $this->util->generateLinks('../');

        $aluno = $this->alunoStorage->verAluno($idAluno);
        $turmaAtual = $this->alunoStorage->pegarIdDaTurmaDoAlunoPorAlunoId($aluno->aluno);
        $turmaQuery = $this->turmaStorage->verTurmas();
        
        $turmas = '';
        
        foreach ($turmaQuery as $turma) {
            $selected = ($turmaAtual == $turma->id)? 'selected' : '';
            $turmas .= "<option value='$turma->id' $selected>$turma->serie º Série $turma->nome</option>";
        }
        
        $endereco = $this->enderecoStorage->verEndereco($aluno->endereco);
        $avatar = $this->avatarStorage->verAvatar($aluno->id);
        $diario_de_classe = $this->diarioStorage->verDiarioDeClassePorAluno($aluno->aluno);
        $nota_por_aluno = $this->notaStorage->verNotasPorAluno($aluno->aluno);
        $arquivos_do_diario = $this->arquivoStorage->verArquivoPorAluno($aluno->aluno);
        $filho_de = $this->responsavelStorage->verAlunosDoResponsavelPorAluno($aluno->aluno);
        
        $footprint = [
            'usuario' => $aluno,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'diario_de_classe' => $diario_de_classe,
            'nota_por_aluno' => $nota_por_aluno,
            'arquivos' => $arquivos_do_diario,
            'filho_de' => $filho_de
        ];
        
        $deletar = "<button class='btn btn-danger btn-sm' id='deletar' value='$aluno->id'><span class='glyphicon glyphicon-trash'></span> Deletar aluno</button>";
        
        $args = [
            'ID' => $aluno->id,
            'IDALUNO' => $aluno->aluno,
            'NOME' => $aluno->nome,
            'SALT' => $aluno->salt,
            'TURMAATUAL' =>  $turmaAtual,
            'EMAIL' => $aluno->email,
            'TURMAS' => $turmas,
            'FOOTPRINT' => json_encode($footprint),
            'BOTAO_DELETAR' => $deletar,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('admin/aluno.html', $args);
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
    
    public function verProfessores()
    {
        $professorQuery = $this->professorStorage->verProfessores();
        
        $professores = '';
        $professores_select = '';
        $professor_array = [];
        
        foreach ($professorQuery as $professor) {
            $data_token = strtolower($professor->nome);
            $is_deleted = ($professor->is_deleted) ? "<span class='label-status_$professor->id label-success'>Ativo</span>" : "<span class='label-status_$professor->id label-danger'>Inativo</span>";
            $professores .=
            "<tr id='row-$professor->id'><td>$professor->nome </td>
            <td>$is_deleted</td><td><a href='professor/$professor->id' class='btn'><span class='glyphicon glyphicon-edit'></span></a>
            <a href='#' class='btn desativar' id='$professor->id'><span class='glyphicon glyphicon-ban-circle'></span></a></td></tr>";
            $professores_select .= "<option data-tokens='$data_token' value='$professor->professor'>$professor->nome</option>";
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
            <td><button class='btn btn-danger btn-sm' id='deletar-dpp' value='$disciplinaProProfessor->id'><span class='glyphicon glyphicon-trash'></span> Deletar</button></td></tr>";
        }
        
        $args = [
            'PROFESSORES' => $professores,
            'PROFESSORES_SELECT' => $professores_select,
            'TURMAS_SELECT' => $turmas,
            'DISCIPLINAS_SELECT' => $disciplinas,
            'DISCIPLINAS_POR_PROFESSOR' => $disciplinasProProfessor,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/professores.html', $args);
    }
    
    public function verProfessor($idProfessor)
    {
        $this->links = $this->util->generateLinks('../');

        $professor = $this->professorStorage->verProfessor($idProfessor);
        
        $endereco = $this->enderecoStorage->verEndereco($professor->endereco);
        $avatar = $this->avatarStorage->verAvatar($professor->id);
        $diario_de_classe = $this->diarioStorage->verDiarioDeClassePorProfessor($professor->professor);
        $disciplina = $this->professorStorage->verProfessorPorMateria($professor->professor);
        $arquivos_do_diario = $this->arquivoStorage->verArquivoPorProfessor($professor->professor);
        
        $footprint = [
            'usuario' => $professor,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'diario_de_classe' => $diario_de_classe,
            'disciplina' => $disciplina,
            'arquivos' => $arquivos_do_diario
        ];
        
        $deletar = "<button class='btn btn-danger btn-sm' id='deletar' value='$professor->id'><span class='glyphicon glyphicon-trash'></span> Deletar professor</button>";
        
        $args = [
            'ID' => $professor->id,
            'NOME' => $professor->nome,
            'SALT' => $professor->salt,
            'EMAIL' => $professor->email,
            'FOOTPRINT' => json_encode($footprint),
            'BOTAO_DELETAR' => $deletar,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/professor.html', $args);
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
    
    public function adicionarProfessorPorMateria()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $disciplina = $data['disciplina'];
        $turma = $data['turma'];
        $professor = $data['professor'];

        $disponivel = $this->professorStorage->verificarMateriaPorProfessor($disciplina, $turma, $professor);

        if (count($disponivel) === 0) {
            $this->professorStorage->adicionarMateriaPorProfessor($disciplina, $turma, $professor);
        }

        header("Location: /webschool/admin/professores");
    }
    
    public function verResponsaveis()
    {
        $responsavelQuery = $this->responsavelStorage->verResponsaveis();
        
        $responsaveis = '';
        
        foreach ($responsavelQuery as $responsavel) {
            $is_deleted = ($responsavel->is_deleted) ? "<span class='label-status_$responsavel->id label-success'>Ativo</span>" : "<span class='label-status_$responsavel->id label-danger'>Inativo</span>";
            $responsaveis .=
             "<tr id='row-$responsavel->id'><td>$responsavel->nome </td>
             <td>$is_deleted</td><td><a href='responsavel/$responsavel->id' class='btn'><span class='glyphicon glyphicon-edit'></span></a>
             <a href='#' class='btn desativar' id='$responsavel->id' ><span class='glyphicon glyphicon-ban-circle'></span></a></td></tr>";
        }
        
        $args = [
            'RESPONSAVEIS' => $responsaveis,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/responsaveis.html', $args);
    }
    
    public function verResponsavel($idResponsavel)
    {
        $this->links = $this->util->generateLinks('../');

        $responsavel = $this->responsavelStorage->verResponsavel($idResponsavel);
        
        $alunosQuery = $this->alunoStorage->verAlunos();

        $alunos = '';
        foreach ($alunosQuery as $aluno) {
            $nome = $aluno->nome .$this->turmaStorage->pegarTurmaDoAlunoPorUsuario($aluno->id);
            $alunos .= "<tr><td>$nome</td><td><button class='btn badge badge-primary escolher_aluno' style='padding: 2px 8px; color: white;' value='$aluno->aluno' id='$nome' data-dismiss='modal' >Escolher</button></td></tr>";
        }
        
        $alunosDoResponsavel = $this->alunoStorage->verAlunosDoResponsavel($responsavel->responsavel);

        $filhos = '';
        foreach ($alunosDoResponsavel as $user) {
            $filhos .= "<tr id='row-$user->rpa'><td>$user->nome</td><td>".$this->turmaStorage->pegarTurmaDoAlunoPorUsuario($user->id)."</td>
            <td><button class='btn btn-danger btn-sm' id='deletar' value='$user->rpa'><span class='glyphicon glyphicon-trash'></span> Deletar</button></td></tr>";
        }
        
        $endereco = $this->enderecoStorage->verEndereco($responsavel->endereco);
        $avatar = $this->avatarStorage->verAvatar($responsavel->id);
        $responsavel_por = $this->responsavelStorage->verAlunosDoResponsavel($responsavel->responsavel);
        
        $footprint = [
            'usuario' => $responsavel,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'responsavel_por' => $responsavel_por
        ];
        
        $deletar = "<button class='btn btn-danger btn-sm' id='deletar-responsavel' value='$responsavel->id'><span class='glyphicon glyphicon-trash'></span> Deletar responsável</button>";
        
        $args = [
            'ID' => $responsavel->id,
            'NOME' => $responsavel->nome,
            'SALT' => $responsavel->salt,
            'RESPONSAVEL' => $responsavel->responsavel,
            'EMAIL' => $responsavel->email,
            'ALUNOS' => $alunos,
            'FILHOS' => $filhos,
            'FOOTPRINT' => json_encode($footprint),
            'BOTAO_DELETAR' => $deletar,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/responsavel.html', $args);
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
    
    public function adicionarAlunoPorResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $responsavel = $data['responsavel'];
        $aluno = $data['aluno'];
        $id = $data['id'];
        
        $responsavel = $this->responsavelStorage->adicionarAlunoPorResponsavel($responsavel, $aluno, $id);
        header("Location: /webschool/admin/responsavel/$responsavel");
    }
    
    public function verTurmas()
    {
        $turmasQuery = $this->turmaStorage->verTurmas();
        
        $turmas = '';
        
        foreach ($turmasQuery as $turma) {
            $turmas .=
             "<tr id='row-$turma->id'><td>$turma->serie º Série $turma->nome</td>
             <td><a href='turma/$turma->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>
             <td><button class='btn btn-danger btn-sm' id='deletar' value='$turma->id'><span class='glyphicon glyphicon-trash'></span> Deletar</button></td></tr>";
        }
        
        $args = [
            'ALUNOS' => $turmas,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/turmas.html', $args);
    }
    
    public function verTurma($turma)
    {
        $this->links = $this->util->generateLinks('../');

        $turma = $this->turmaStorage->verTurma($turma);
        
        $args = [
            'ID' => $turma->id,
            'TURMA' => $turma->serie,
            'LETRA' => $turma->nome,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/turma.html', $args);
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
        $turma = $data['id'];
        
        
        $this->turmaStorage->alterarTurma($nome, $serie, $turma);
        header('Location: /webschool/admin/turmas');
    }

    public function verMaterias()
    {
        $disciplinaQuery = $this->materiaStorage->verMaterias();
        
        $disciplinas = '';
        
        foreach ($disciplinaQuery as $disciplina) {
            $disciplinas .=
             "<tr id='row-$disciplina->id'><td>$disciplina->nome</td>
             <td><a href='disciplina/$disciplina->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>
             <td><button class='btn btn-danger btn-sm' id='deletar' value='$disciplina->id'><span class='glyphicon glyphicon-trash'></span> Deletar</button></td></tr>";
        }
        
        $args = [
            'DISCIPLINAS' => $disciplinas,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('admin/disciplinas.html', $args);
    }
    
    public function verMateria($materia)
    {
        $this->links = $this->util->generateLinks('../');

        $disciplina = $this->materiaStorage->verMateria($materia);
        
        $args = [
            'ID' => $disciplina->id,
            'NOME' => $disciplina->nome,
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('admin/disciplina.html', $args);
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
}
