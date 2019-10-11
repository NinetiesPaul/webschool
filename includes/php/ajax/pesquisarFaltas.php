<?php

if (! empty($_POST)) {
    include('../conn.php');
    
    $aluno = $_POST["aluno"];
    $turma = $_POST["turma"];
    $disciplina = $_POST["disciplina"];
    
    echo '<p/>';
    
    $faltasQuery = $db->query("
        select * from diario_de_classe where turma=$turma and aluno=$aluno and disciplina=$disciplina and presenca = 1 order by data
    ");
    $faltas = $faltasQuery->fetchAll(PDO::FETCH_OBJ);
    
    echo "Este aluno possui ".$faltasQuery->rowCount()." falta(s) nesta matéria:<p/>";
    
    foreach ($faltas as $falta) {
        $data = new DateTime($falta->data);
        echo $data->format('d/m/Y') . "<br/>";
    }
    
    $comentariosQuery = $db->query("
        select * from diario_de_classe where turma=$turma and aluno=$aluno and disciplina=$disciplina and contexto='observacao' order by data
    ");
    $comentarios = $comentariosQuery->fetchAll(PDO::FETCH_OBJ);
    
    echo "<p/>Este aluno possui ".$comentariosQuery->rowCount()." comentários(s) por professores:<p/>";
    
    foreach ($comentarios as $comentario) {
        $data = new DateTime($comentario->data);
        echo $data->format('d/m/Y') . "<br/>$comentario->observacao<br/>";
    } 
   
}
