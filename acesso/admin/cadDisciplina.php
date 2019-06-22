<?php
session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: ../../index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../data/functions.php'; ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../css/glyphicons.css" rel="stylesheet">
        <link href="../../res/navbar.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <title>webSchool :: Cadastro de Disciplina</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="index.php">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="index.php">Home</a>
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

                <strong>Cadastro de Disciplina</strong><p/>
                <form action="_addDisciplina.php" method="post" role="form" class="form-horizontal " >
                        <div class="form-group row justify-content-center ">
                                <label for="nome" class="col-form-label col-md-2 col-form-label-sm ">Nome da disciplina:</label>
                                <div class="col-md-3">
                                        <input type="text" name="nome" class="form-control form-control-sm">
                                </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus' required></span> Cadastrar</button>
                </form>

                <hr/>

                <p><strong>Lista de Disciplina</strong></p>

                <?php

                include '../../data/conn.php';

                $usersQuery = $db->query("select * from disciplina");

                $usersQuery = $usersQuery->fetchAll(PDO::FETCH_OBJ);
                
                ?>

                <table style="margin-left: auto; margin-right: auto; font-size: 13;">
                <?php
                
                foreach ($usersQuery as $user) {
                    echo '<tr><td>'.$user->nomeDisciplina.'</td>';
                    echo "<td><a href='_editDisciplina.php?disc=$user->idDisciplina' class='btn btn-info btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>";
                    echo "<td><a href='_deleteDisciplina.php?disc=$user->idDisciplina' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></td></tr>";
                }
                
                ?>	
                </table>
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
    header('Location: ../../index.php');
}
