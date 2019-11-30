<?php

namespace App\Controllers;

use App\Templates;
use App\DB\DB;
use PDO;

class AdminController {
    
    protected $template;
    
    protected $connection;

    public function __construct() {
        $this->template = new Templates();
        $this->connection = new DB;
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
    
    public function responsaveis()
    {
        $templateFinal 	= $this->template->getTemplate('admin/responsaveis.html');
        echo $templateFinal;
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
        
        $template 	= $this->template->getTemplate('admin/turmas.html');
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
