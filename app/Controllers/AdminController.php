<?php

namespace App\Controllers;

use App\Templates;
use App\DB\DB;
use PDO;
use App\Util;

class AdminController {
    
    protected $template;
    
    protected $connection;
    
    protected $util;

    public function __construct()
    {
        $this->template = new Templates();
        $this->connection = new DB;
        $this->util = new Util();
        $this->util->userPermission('admin');
    }
    
    public function index()
    {
        $templateFinal 	= $this->template->getTemplate('admin/index.html');
        echo $templateFinal;
    }
    
    public function alunos()
    {
        $templateFinal 	= $this->template->getTemplate('admin/alunos.html');
        echo $templateFinal;
    }
    
    public function professores()
    {
        $templateFinal 	= $this->template->getTemplate('admin/professores.html');
        echo $templateFinal;
    }
    
    public function verResponsaveis()
    {
        $responsavelQuery = $this->connection->query("select usuario.* from usuario, responsavel where usuario.id=responsavel.usuario");
        $responsavelQuery = $responsavelQuery->fetchAll(PDO::FETCH_OBJ);
        
        $responsaveis = ''; 
        
        foreach ($responsavelQuery as $responsavel) {
            $is_deleted = ($responsavel->is_deleted) ? "<span class='glyphicon glyphicon-remove'></span> " : "<span class='glyphicon glyphicon-ok'></span> ";
            $responsaveis .= 
             "<tr id='row-$responsavel->id'><td>$responsavel->nome </td>
             <td>$is_deleted<a href='responsavel/$responsavel->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a> </td>
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
        $responsavelQuery = $this->connection->query("select usuario.*,responsavel.id as responsavel from usuario, responsavel where usuario.id=responsavel.usuario and responsavel.usuario = $idResponsavel");
        $responsavel = $responsavelQuery->fetch(PDO::FETCH_OBJ);

        $alunosQuery = $this->connection->query("select usuario.* from usuario, aluno where usuario.id = aluno.usuario");
        $alunosQuery = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

        $alunos = '';
        foreach ($alunosQuery as $aluno) {
            $alunos .= "<option value='$aluno->id'>$aluno->nome (".$this->util->pegarTurmaDoAlunoPorUsuario($aluno->id).")</option>";
        }

        $usersQuery = $this->connection->query("
            select distinct usuario.*, responsavel_por_aluno.id as rpa from usuario, aluno, responsavel_por_aluno
            where aluno.usuario = usuario.id
            and aluno.id = responsavel_por_aluno.aluno
            and responsavel_por_aluno.responsavel = $responsavel->responsavel
        ");
        $usersQuery = $usersQuery->fetchAll(PDO::FETCH_OBJ);

        $filhos = '';
        foreach ($usersQuery as $user) {
            $filhos .= "<tr id='row-$user->rpa'><td>$user->nome.'</td><td>".$this->util->pegarTurmaDoAlunoPorUsuario($user->id)."</td>
            <td><button class='btn btn-danger btn-sm' id='deletar' value='$user->rpa'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
            'ID' => $responsavel->id,
            'NOME' => $responsavel->nome,
            'SALT' => $responsavel->salt,
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
        
        if ($this->util->loginTakenBackEnd($email, "responsavel")) {
            header('Location: /webschool/admin/responsaveis');
            exit;
        }
        
        $endereco = $this->connection->prepare("INSERT INTO endereco (estado) VALUES (:estado)");

        $endereco->execute([
            'estado' => 1,
        ]);

        $idEndereco = (int) $this->connection->lastInsertId();

        $user = $this->connection->prepare("INSERT INTO usuario (nome, email, pass, salt, endereco)
                VALUES (:name, :email, :password, :salt, :endereco)");

        $user->execute([
            'name' => $nome,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
            'endereco' => $idEndereco,
        ]);

        $userId = (int) $this->connection->lastInsertId();

        $responsavel = $this->connection->prepare("INSERT INTO responsavel (usuario) VALUES (:idUusuario)");
        $responsavel->execute([
            'idUusuario' => $userId,
        ]);

        $avatar = $this->connection->prepare("INSERT INTO fotos_de_avatar (usuario) VALUES (:idUusuario)");
        $avatar->execute([
            'idUusuario' => $userId,
        ]);

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
        $newPassword = md5($password);
        
        if ($this->util->loginTakenBackEnd($email, "responsavel", $userId)) {
            header('Location: /webschool/admin/responsaveis');
            exit;
        }

        $sql = "
            UPDATE usuario
            SET nome=:nome, email=:email
            ";

        $fields = [
            'nome' => $nome,
            'email' => $email,
        ];

        if (strlen($password) > 0) {
            $password = $data['password'];
            $password = md5($password . $salt);

            $sql .= ' ,pass=:pass';
            $fields['pass'] = $password;
        }

        $sql .= ' where id=:userId';
        $fields['userId'] = $userId;

        $user = $this->connection->prepare($sql);
        $user->execute($fields);
        
        header('Location: /webschool/admin/responsaveis');
    }
    
    public function removerResponsavel(int $idResponsavel)
    {
        $user = $this->connection->prepare("UPDATE usuario SET is_deleted = 1 WHERE id = :id");

        $user->execute([
            'id' => $idResponsavel,
        ]);
    }
    
    public function adicionarAlunoPorResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $responsavel = $data['id'];
        $aluno = $data['aluno'];

        $responsavelQuery = $this->connection->query("select id from responsavel where usuario = $responsavel");
        $responsavelQuery = $responsavelQuery->fetchObject();

        $alunoQuery = $this->connection->query("select id from aluno where usuario = $aluno");
        $alunoQuery = $alunoQuery->fetchObject();

        $user = $this->connection->prepare("INSERT INTO responsavel_por_aluno (responsavel, aluno)
                VALUES (:responsavel, :aluno)");

        $count = $user->execute([
            'responsavel' => $responsavelQuery->id,
            'aluno' => $alunoQuery->id,
        ]);

        header("Location: /webschool/admin/responsavel/$responsavel");
    }
    
    public function removerAlunoPorResponsavel(int $id)
    {
        $user = $this->connection->prepare("DELETE from responsavel_por_aluno WHERE id = :id");

        $user->execute([
            'id' => $id,
        ]);
    }
    
    public function verTurmas()
    {   
        $turmaQuery = $this->connection->query("select * from turma order by serie");
        $turmaQuery = $turmaQuery->fetchAll(PDO::FETCH_OBJ);
        
        $turmas = '';
        
        foreach ($turmaQuery as $turma) {
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
        $turmaQuery = $this->connection->query("select * from turma where id = $turma");
        $turma = $turmaQuery->fetch(PDO::FETCH_OBJ);
        
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
        
        $serie = $data['serie'];
        $nome = $data['nome'];

        $user = $this->connection->prepare("INSERT INTO turma (nome, serie)
                VALUES (:nome, :serie)");

        $user->execute([
            'nome' => $nome,
            'serie' => $serie,
        ]);
        
        header('Location: /webschool/admin/turmas');
    }
    
    public function atualizarTurma()
    {
        $data = json_decode(json_encode($_POST), true);

        $turma = $data['id'];
        $nome = $data['nome'];
        $serie = $data['serie'];

        $user = $this->connection->prepare("
            UPDATE turma
            SET serie=:serie, nome=:nome
            where id=:turma
            ");

        $user->execute([
            'nome' => $nome,
            'serie' => $serie,
            'turma' => $turma,
        ]);
        
        header('Location: /webschool/admin/turmas');
    }
    
    public function removerTurma(int $turma)
    {
        $user = $this->connection->prepare("DELETE FROM turma WHERE id=:id");

        $user->execute([
            'id' => $turma,
        ]);
    }
    
    public function verMaterias()
    {

        $disciplinaQuery = $this->connection->query("select * from disciplina");
        $disciplinaQuery = $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
        
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
        $disciplinaQuery = $this->connection->query("select * from disciplina where id = $materia");
        $disciplina = $disciplinaQuery->fetch(PDO::FETCH_OBJ);
        
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

        $user = $this->connection->prepare("INSERT INTO disciplina (nome)
                VALUES (:nome)");

        $user->execute([
            'nome' => $nome,
        ]);
        
        header('Location: /webschool/admin/disciplinas');       
    }
    
    public function atualizarMateria()
    {
        $data = json_decode(json_encode($_POST), true);

        $disciplina = $data['id'];
        $nome = $data['nome'];

        $user = $this->connection->prepare("
            UPDATE disciplina
            SET nome=:nome
            where id=:disciplina
            ");

        $user->execute([
            'nome' => $nome,
            'disciplina' => $disciplina,
        ]);
        
        header('Location: /webschool/admin/disciplinas');        
    }
    
    public function removerMateria(int $disciplina)
    {
        $user = $this->connection->prepare("DELETE FROM disciplina WHERE id=:id");

        $user->execute([
            'id' => $disciplina,
        ]);
    }
}
