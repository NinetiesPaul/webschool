<?php

if (! empty($_POST)) {
    include('conn.php');
    
    $mes = $_POST["mes"];
    $ano = $_POST["ano"];
    $turma = $_POST["turma"];
    $disc = $_POST["disc"];

    $days = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    
    $dates = [];
    $results = [];
    for ($i = 1; $i < $days+1; $i++) {
        $date = $ano.'-'.$mes.'-'.$i;
        $dates[] = new DateTime($date);
    }
    
    echo '<p/>';
    
    $alunosQuery = $db->query("
        select diariodeclasse.*, usuario.nome
        from usuario
        inner join aluno on aluno.idUsuario = usuario.idUsuario
        inner join diariodeclasse on diariodeclasse.idAluno = aluno.idAluno
        and diariodeclasse.idDisciplina = $disc and diariodeclasse.idTurma = $turma
        group by usuario.nome
        order by usuario.nome 
    ");
    $alunos = $alunosQuery->fetchAll(PDO::FETCH_OBJ);
    
    echo "<table class='table table-striped table-borderless table-responsive'>";
    echo "<thead class='thead-dark'>";
    echo "<tr>";
    echo "<th scope='col'>  </th>";
    foreach ($dates as $date) {
        echo "<th scope='col'>".$date->format('d')."</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($alunos as $aluno) {
        echo "<tr>";
        echo "<td>$aluno->nome</td>";
        foreach ($dates as $date) {
            $diarioQuery = $db->query("
                select *
                from diariodeclasse
                where idAluno = $aluno->idAluno
                and diariodeclasse.idDisciplina = $disc and diariodeclasse.idTurma = $turma
                and dataDaFalta = '".$date->format('Y-m-d')."'
            ");
            $diario = $diarioQuery->fetch(PDO::FETCH_OBJ);
            
            $date = explode('-', $date->format('Y-m-d'));
           
            $spanPresenca = "<span class='glyphicon glyphicon-remove'></span>";
            $linkPresenca = "../../criar-presenca/$date[0]/$date[1]/$date[2]/$aluno->idAluno/$aluno->idDisciplina/$aluno->idTurma";
            
            if ($diario && $diario->presenca == 1) {
                $spanPresenca = "<span class='glyphicon glyphicon-ok'></span>";
                $linkPresenca = "../../presenca/$diario->idDiario";
            }
            
            $linkComentario = "../../comentario/$date[0]/$date[1]/$date[2]/$aluno->idAluno/$aluno->idDisciplina/$aluno->idTurma";
            
            echo "<td>";
            echo "<a href='$linkPresenca' id='presenca'>$spanPresenca</a> <a href='$linkComentario' id='presenca' ><span class='glyphicon glyphicon-comment'></span></a>";
            echo "</td>";
        }
    }
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
}
