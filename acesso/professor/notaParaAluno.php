<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "professor") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../data/functions.php';
        
        include '../../data/conn.php';

        $professorQuery = $db->query("select * from professor where idUsuario=$userId");
        $professorQuery = $professorQuery->fetchObject();

        if (!empty($_GET)) {
        
            $idAluno = $_GET['a'];
            $disciplina = $_GET['d'];
            $turma = $_GET['t'];
            $notaNum = $_GET['n']; ?>

<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../../../../../css/glyphicons.css" rel="stylesheet">
        <link href="../../../../../../res/navbar.css" rel="stylesheet">
        <link href="../../../../../../res/css.css" rel="stylesheet">
        <script src="../../../../../../res/jquery.js">
        </script>
        <title>webSchool :: Alteração de Nota</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="../../../../home">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como <?php echo pegarNomeProfessor($professorQuery->idProfessor); ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="../../../../home">Home</a>
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
                <strong>Alteração de Nota</strong> <p/>
                <?php

                $idAluno = pegarIdDoAluno($idAluno);

                $notaQuery = $db->query("
                    select * from notaPorAluno where idTurma=$turma and idAluno=$idAluno and idDisciplina=$disciplina
                ");

                $count = $notaQuery->rowCount();

                $notaQuery = $notaQuery->fetchObject();

                $nota = ($count == 0) ? 0 : $notaQuery->$notaNum;
                
                pegarNomeDoAluno($idAluno);
                
                echo '<br/>'.pegarDisciplina($disciplina).', '.pegarTurma($turma).'<p/>';
                
                ?>

                <form action="../../../../src/adicionarNotaParaAluno.php" method="post" role="form" class="form-horizontal ">
                    <input type="hidden" name="aluno" value="<?php echo $idAluno; ?>" />
                    <input type="hidden" name="disciplina" value="<?php echo $disciplina; ?>" />
                    <input type="hidden" name="turma" value="<?php echo $turma; ?>" />
                    <input type="hidden" name="notaNum" value="<?php echo $notaNum; ?>" />
                    <div class="form-group row justify-content-center ">
                        <label for="nota" class="col-form-label col-md-2 col-form-label-sm ">Nota:</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" name="nota" value="<?php echo $nota; ?>"/>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>

<?php
        } else {
            header('Location: visualizarTurmas.php');
        }
    }
} else {
    header('Location: index.php');
}
?>

