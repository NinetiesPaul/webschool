<?php

if (! empty($_POST)) {
    include('conn.php');
    
    $mes = $_POST["mes"];
    $ano = $_POST["ano"];
    $turma = $_POST["turma"];
    $disc = $_POST["disc"];
    $context = $_POST["context"];

    $days = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    
    $dates = [];
    $results = [];
    for ($i = 1; $i < $days+1; $i++) {
        $date = $ano.'-'.$mes.'-'.$i;
        $dates[] = $date;
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
    echo "<th scope='col'> Data </th>";
    foreach ($alunos as $aluno) {
        echo "<th scope='col'>$aluno->nome</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($dates as $date) {
        echo "<tr>";
        echo "<th scope='row'>".$date."</th>";
        foreach ($alunos as $aluno) {
            $diarioQuery = $db->query("
            select *
            from diariodeclasse
            where idAluno = $aluno->idAluno
            and dataDaFalta = '$date'
            ");
            $diario = $diarioQuery->fetch(PDO::FETCH_OBJ);
            
            $presenca = 'Falta';
            
            $date = explode('-', $date);
            
            if ($context === 'professor') {
                $link = "../../criar-presenca/$date[0]/$date[1]/$date[2]/$aluno->idAluno/$aluno->idDisciplina/$aluno->idTurma";
            }
            
            if ($diario) {
                if ($diario->presenca == 1) {
                    $presenca = 'Presente';
                    if ($context === 'professor') {
                        $link = "../../presenca/$diario->idDiario";
                    }
                }
            }
            echo "<th scope='row'>";
            echo ($context === 'professor') ? "<a href='$link' id='presenca'>$presenca</a>" : $presenca;
            echo "</th>";
        }
    }
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
}
