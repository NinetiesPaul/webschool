<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "professor" || !$tipo) {
    header('Location: ../../home');
}

$iddisciplina = isset($_GET['id']) ? $_GET['id'] : false;
if (!$iddisciplina || $iddisciplina == '') {
    header('Location: ../turmas');
}

$user = $_SESSION['user'];
include '../../includes/php/functions.php';
include '../../includes/php/conn.php';

$iddisciplina = $_GET['id'];
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../../includes/bootstrap/css/glyphicons.css" rel="stylesheet">
        <link href="../../../includes/css/navbar.css" rel="stylesheet">
        <link href="../../../includes/css/css.css" rel="stylesheet">
        <style>
            #btn_diario {
                text-decoration: none;
            }
        </style>
        <script src="../../../includes/js/jquery.js">
        </script>
        <title>Professor :: Detalhes da turma</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="../home">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como <?php echo $user->nome; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="../home">Home</a>
                            <a class="dropdown-item" href="../../perfil">Meu perfil</a>
                            <a class="dropdown-item" href="../../../logout">Sair</a>
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

                $disciplinaQuery = $db->query("select * from disciplina_por_professor where id=$iddisciplina");
                $result = $disciplinaQuery->fetchObject();
                
                if (!$result) {
                    header('Location: ../turmas');
                }
                
                $disciplina = $result->disciplina;
                $turma = $result->turma;

                echo pegarDisciplina($disciplina).', '.pegarTurma($turma).'<br/>';
                echo "<a href='../diario-de-classe/$turma/$disciplina' class='btn-sm btn-info' id='btn_diario'><span class='glyphicon glyphicon-pencil'></span> Diário de classe</a><p/>";
                
                $alunosQuery = $db->query("
                    select usuario.id, usuario.nome, nota_por_aluno.nota1, nota_por_aluno.nota2, nota_por_aluno.nota3, nota_por_aluno.nota4, nota_por_aluno.rec1, nota_por_aluno.rec2, nota_por_aluno.rec3, nota_por_aluno.rec4
                    from usuario
                    inner join aluno on aluno.usuario = usuario.id
                    inner join nota_por_aluno on nota_por_aluno.aluno = aluno.id and nota_por_aluno.disciplina=$disciplina and nota_por_aluno.turma=$turma
                    group by usuario.nome
                    order by usuario.nome
                ");
                $alunosQuery = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

                echo '<table style="margin-left: auto; margin-right: auto; font-size: 13;" class="table">';
                echo '<tr><td></td><td>Nota 1</td><td>Rec. 1</td><td>Nota 2:</td><td>Rec. 2</td><td>Nota 3</td><td>Rec. 3</td><td>Nota 4</td><td>Rec. 4</td><td></td><td></td>';
                foreach ($alunosQuery as $aluno){
                    echo '<tr><td>'.$aluno->nome.'</td>';
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/nota1'>$aluno->nota1</a> </td>";
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/rec1'>$aluno->rec1</a> </td>";
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/nota2'>$aluno->nota2</a> </td>";
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/rec2'>$aluno->rec2</a> </td>";
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/nota3'>$aluno->nota3</a> </td>";
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/rec3'>$aluno->rec3</a> </td>";
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/nota4'>$aluno->nota4</a> </td>";
                    echo "<td> <a href='../nota/$aluno->id/$disciplina/$turma/rec4'>$aluno->rec4</a> </td>";
                }
                echo '</table>';
                
                ?>	

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	


    </body>
</html>
