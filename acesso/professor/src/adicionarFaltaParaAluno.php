<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "professor") {
        header('Location: ../../index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../../data/functions.php';
        
        include '../../../data/conn.php';

        $professorQuery = $db->query("select * from professor where idUsuario=$userId");
        $professorQuery = $professorQuery->fetchObject();

        if (!empty($_GET)) {
        
            $ano = $_GET['ano'];
            $mes = $_GET['mes'];
            $dia = $_GET['dia'];
            
            $aluno = $_GET['a'];
            $disciplina = $_GET['di'];
            $turma = $_GET['t'];
            
            $mes = (strlen($mes) == 1) ? "0".$mes : $mes;
            $dia = (strlen($dia) == 1) ? "0".$dia : $dia;
            
            $data = $ano.'-'.$mes.'-'.$dia;
            
            $diarioQuery = $db->query("
            select idDiario
            from diariodeclasse
            where idAluno = $aluno
            and idDisciplina = $disciplina
            and idTurma = $turma
            and dataDaFalta = '$data'
            ");
            
            $diario = $diarioQuery->fetch(PDO::FETCH_OBJ);
            
            if (!$diario) {
                $user = $db->prepare("
                    INSERT INTO diariodeclasse (idAluno, idDisciplina, idTurma, dataDaFalta, presenca) 
                    values (:aluno, :disciplina, :turma, :data, 1)
                ");
                
                $user->execute([
                    'aluno' => $aluno,
                    'disciplina' => $disciplina,
                    'turma' => $turma,
                    'data' => $data,
                ]);
            } else {
                $presenca = $diario->presenca;
                
                if ($presenca == False) {
                    $presenca = 1;
                } else {
                    $presenca = 0;
                }
                
                $user = $db->prepare("UPDATE diariodeclasse SET presenca = :presenca WHERE idDiario=:id");
            
                $user->execute([
                    'presenca' => $presenca,
                    'id' => $diario->idDiario,
                ]);
            }
        
            header("Location: ../../../../../../diario-de-classe/$turma/$disciplina");
        }
    }
}
