<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "aluno" || !$tipo) {
    header('Location: ../../home');
}

$userId = $_SESSION['user_id'];
include '../../data/functions.php';
include '../../data/conn.php';

$alunoQuery = $db->query("select * from aluno where idUsuario=$userId");
$alunoQuery = $alunoQuery->fetchObject();
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../res/navbar.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <script src="../../res/detect.js">
        </script>
        <title>Aluno :: Home Page</title>
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
                            <a class="dropdown-item" href="../perfil">Meu perfil</a>
                            <a class="dropdown-item" href="../../logout">Sair</a>
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
                <a href="turmas" class="btn btn-light btn btn-block">Turmas</a>
                <a href="../perfil" class="mobile btn btn-dark btn btn-block" >Meu Perfil</a>
                <a href="../../logout" class="mobile btn btn-danger btn btn-block" >Sair</a>
                <!-- <a href="solicitacoes.php">Fazer solicitações ao administrador</a><br/> -->
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>
