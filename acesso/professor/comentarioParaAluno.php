<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "professor" || !$tipo) {
    header('Location: ../../home');
}

$userId = $_SESSION['user_id'];
include '../../data/functions.php';
include '../../data/conn.php';

$ano = $_GET['ano'];
$mes = $_GET['mes'];
$dia = $_GET['dia'];

$aluno = $_GET['a'];
$disciplina = $_GET['di'];
$turma = $_GET['t'];

$mes = (strlen($mes) == 1) ? "0".$mes : $mes;
$dia = (strlen($dia) == 1) ? "0".$dia : $dia;

$data = $ano.'-'.$mes.'-'.$dia;

$date = new DateTime($data);

$user = $_SESSION['user_id'];

$idprof = pegaridProfessor($user);

?>

<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../../../../../../../css/glyphicons.css" rel="stylesheet">
        <link href="../../../../../../../../res/navbar.css" rel="stylesheet">
        <link href="../../../../../../../../res/css.css" rel="stylesheet">
        <script src="../../../../../../../../res/jquery.js">
        </script>
        <title>webSchool :: Comentário</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="../../../../../../home">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como <?php echo pegarNomeProfessor($idprof); ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="../../../../../../home">Home</a>
                            <a class="dropdown-item" href="../../../../../../../perfil">Meu perfil</a>
                            <a class="dropdown-item" href="../../../../../../../../logout">Sair</a>
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
                
                <?php
                
                $comentarioQuery = $db->query("
                    select *
                    from diariodeclasse_comentarios
                    where aluno = $aluno
                    and disciplina = $disciplina
                    and turma = $turma
                    and data = '$data'
                    and professor = $idprof
                ");
                $comentario = $comentarioQuery->fetch(PDO::FETCH_OBJ);
                
                $mensagem = '';
                if ($comentario) {
                    $mensagem = $comentario->mensagem;
                }
                
                
                ?>
                
                <strong>Comentário</strong> <p/>
                <form action="../../../../../../src/adicionarComentarioParaAluno.php" method="post" role="form" class="form-horizontal ">
                    <input type="hidden" name="aluno" value="<?php echo $aluno; ?>" />
                    <input type="hidden" name="disciplina" value="<?php echo $disciplina; ?>" />
                    <input type="hidden" name="turma" value="<?php echo $turma; ?>" />
                    <input type="hidden" name="data" value="<?php echo $data; ?>" />
                    <input type="hidden" name="prof" value="<?php echo $idprof; ?>" />
                    <div class="form-group row justify-content-center ">
                        <div class="col-md-3">
                            <textarea name="comentario"><?php echo $mensagem ?></textarea>
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
