<?php
session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "aluno") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../data/functions.php';
        
        include '../../data/conn.php';

        $alunoQuery = $db->query("select * from aluno where idUsuario=$userId");
        $alunoQuery = $alunoQuery->fetchObject(); ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../res/css.css" rel="stylesheet">
        <link href="../../res/navbar.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <title>Aluno :: Minhas Turmas</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="home">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Logado como <?php echo pegarNomeDoAluno($alunoQuery->idAluno); ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="home">Home</a>
                        <a class="dropdown-item" href="../perfil.php">Meu perfil</a>
                        <a class="dropdown-item" href="../../logout.php">Sair</a>
                        <!-- <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a> -->
                    </div>
                  </li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <div class="jumbotron text-center">
                <strong>Minhas turmas</strong><p/>

                <?php

                $turmasQuery = $db->query("select idTurma from notaporaluno where idAluno=$alunoQuery->idAluno group by idTurma");
                $turmas = $turmasQuery->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($turmas as $turma) {
                    $turma = $turma->idTurma;
                    echo pegarTurma($turma).'<br/>';
                    $notasQuery = $db->query("select * from notaporaluno where idAluno=$alunoQuery->idAluno and idTurma=$turma order by idDisciplina");
                    $notas = $notasQuery->fetchAll(PDO::FETCH_OBJ);
                    echo '<table style="margin-left: auto; margin-right: auto; font-size: 13;" class="table">';

                    foreach ($notas as $nota) {
                        echo '<tr><td>'.pegarDisciplina($nota->idDisciplina).'</td>';
                        echo '<td>Nota 1: '.$nota->nota1.'</td>';
                        echo '<td>Nota 2: '.$nota->nota2.'</td>';
                        echo '<td>Nota 3: '.$nota->nota3.'</td>';
                        echo '<td>Nota 4: '.$nota->nota4.'</td>';
                        echo '<td>Rec 1: '.$nota->rec1.'</td>';
                        echo '<td>Rec 2: '.$nota->rec2.'</td>';
                        echo '<td>Rec 3: '.$nota->rec3.'</td>';
                        echo '<td>Rec 4: '.$nota->rec4.'</td>';
                        //echo "<td>Faltas: <a href='verFaltas.php?a[]=$alunoQuery->idAluno&a[]=$nota->idDisciplina&a[]=$nota->idTurma'>".pegarFaltasDoAluno($userId, $nota->idDisciplina, $nota->idTurma)."</a></td>";
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                
                ?>

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>

<?php
    }
} else {
    header('Location: index.php');
}
