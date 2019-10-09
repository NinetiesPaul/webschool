<?php

if (! empty($_POST)) {
    include('conn.php');
    
    $aluno = $_POST["aluno"];
    $turma = $_POST["turma"];
    $disciplina = $_POST["disciplina"];
    
    echo '<p/>';
    
    $faltasQuery = $db->query("
        select * from diariodeclasse where idTurma=$turma and idAluno=$aluno and idDisciplina=$disciplina and presenca = 1 order by dataDaFalta
    ");
    $faltas = $faltasQuery->fetchAll(PDO::FETCH_OBJ);
    
    echo "Este aluno possui ".$faltasQuery->rowCount()." falta(s) nesta matéria:<p/>";
    
    foreach ($faltas as $falta) {
        $data = new DateTime($falta->dataDaFalta);
        echo $data->format('d/m/Y') . "<br/>";
    }
    
    $comentariosQuery = $db->query("
        select * from diariodeclasse_comentarios where turma=$turma and aluno=$aluno and disciplina=$disciplina order by data
    ");
    $comentarios = $comentariosQuery->fetchAll(PDO::FETCH_OBJ);
    
    echo "<p/>Este aluno possui ".$comentariosQuery->rowCount()." comentários(s) por professores:<p/>";
    
    foreach ($comentarios as $comentario) {
        $data = new DateTime($comentario->data);
        echo $data->format('d/m/Y') . "<br/>$comentario->mensagem<br/>";
    } 
   
}
