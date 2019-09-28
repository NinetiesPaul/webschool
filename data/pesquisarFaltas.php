<?php

if (! empty($_POST)) {
    include('conn.php');
    
    $aluno = $_POST["aluno"];
    $turma = $_POST["turma"];
    $disciplina = $_POST["disciplina"];
    
    echo '<p/>';
    
    $faltasQuery = $db->query("
        select * from diariodeclasse where idTurma=$turma and idAluno=$aluno and idDisciplina=$disciplina order by dataDaFalta
    ");
    $faltas = $faltasQuery->fetchAll(PDO::FETCH_OBJ);
    
    echo "Este aluno possui ".$faltasQuery->rowCount()." falta(s) nesta matéria:<p/>";
    
    foreach ($faltas as $falta) {
        $data = new DateTime($falta->dataDaFalta);
        echo $data->format('d/m/Y') . "<br/>";
    }
    
   
}
