<?php
session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "professor") {
        header('Location: ../../index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../data/functions.php';
        
        include '../../data/conn.php';

        $professorQuery = $db->query("select * from professor where idUsuario=$userId");
        $professorQuery = $professorQuery->fetchObject();
    
        if (!empty($_GET)) {
            $turma = $_GET['turma'];
            $disciplina = $_GET['disc']; ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../res/navbar.css" rel="stylesheet">
        <link href="../../res/css.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <title>Professor :: Detalhes da turma</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="index.php">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como <?php echo pegarNomeProfessor($professorQuery->idProfessor); ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="index.php">Home</a>
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

                <strong>Detalhes da turma</strong> <p/>
                <?php

                echo pegarDisciplina($disciplina).', '.pegarTurma($turma).'<p/>';

            $alunosQuery = $db->query("
                    select usuario.idUsuario, usuario.nome, notaporaluno.nota1, notaporaluno.nota2, notaporaluno.nota3, notaporaluno.nota4, notaporaluno.rec1, notaporaluno.rec2, notaporaluno.rec3, notaporaluno.rec4, count(faltaporaluno.idFaltaPorAluno) as faltas
                    from usuario
                    inner join aluno on aluno.idUsuario = usuario.idUsuario
                    inner join notaporaluno on notaporaluno.idAluno = aluno.idAluno and notaporaluno.idDisciplina=$disciplina and notaporaluno.idTurma=$turma
                    left join faltaporaluno on faltaporaluno.idAluno = aluno.idAluno and faltaporaluno.idDisciplina=$disciplina and faltaporaluno.idTurma=$turma
                    group by usuario.nome
                    order by usuario.nome
                ");
            $alunosQuery = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

            echo '<table style="margin-left: auto; margin-right: auto; font-size: 13;">';
            foreach ($alunosQuery as $aluno):
                echo '<tr><td>'.$aluno->nome.'</td>';
            echo "<td>Nota 1: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=nota1'>$aluno->nota1</a> </td>";
            echo "<td>Recuperação 1: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=rec1'>$aluno->rec1</a> </td>";
            echo "<td>Nota 2: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=nota2'>$aluno->nota2</a> </td>";
            echo "<td>Recuperação 2: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=rec2'>$aluno->rec2</a> </td>";
            echo "<td>Nota 3: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=nota3'>$aluno->nota3</a> </td>";
            echo "<td>Recuperação 3: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=rec3'>$aluno->rec3</a> </td>";
            echo "<td>Nota 4: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=nota4'>$aluno->nota4</a> </td>";
            echo "<td>Recuperação 4: <a href='_addNotaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma&a[]=rec4'>$aluno->rec4</a> </td>";
            echo "<td>Faltas: <a href='_addFaltaParaAluno.php?a[]=$aluno->idUsuario&a[]=$disciplina&a[]=$turma'>$aluno->faltas </a></td></tr>";
            endforeach;
            echo '</table>'; ?>	

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	


    </body>
</html>

<?php
        } else {
            header('Location: verTurmas.php');
        }
    }
} else {
    header('Location: ../../index.php');
}
?>


	