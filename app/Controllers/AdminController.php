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
        $turmas = $turmaQuery->fetchAll(PDO::FETCH_OBJ);
        
        $alunos = '';
        
        foreach ($turmas as $turma) {
            $alunos .= 
             "<tr id='row-$turma->id'><td>$turma->serie º Série $turma->nome</td>
             <td><a href='turma/$turma->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>
             <td><button class='btn btn-danger btn-sm' id='deletar' value='$turma->id'><span class='glyphicon glyphicon-remove'></span> Deletar</button></td></tr>";
        }
        
        $args = [
          'ALUNOS' => $alunos  
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
    
    public function materias()
    {
        $templateFinal 	= $this->template->getTemplate('admin/index.html');
        echo $templateFinal;
    }
}
