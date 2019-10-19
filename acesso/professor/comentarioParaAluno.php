<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "professor" || !$tipo) {
    header('Location: ../../home');
}

include '../../includes/php/functions.php';
include '../../includes/php/conn.php';

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

$user = $_SESSION['user'];

?>

<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../../../../../../../includes/css/glyphicons.css" rel="stylesheet">
        <link href="../../../../../../../../includes/css/navbar.css" rel="stylesheet">
        <link href="../../../../../../../../includes/css/css.css" rel="stylesheet">
        <script src="../../../../../../../../includes/js/jquery.js"></script>
        <title>webSchool :: Comentário</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="../../../../../../home">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como <?php echo $user->nome; ?>
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
                <strong>Comentário</strong> <p/>
                <form action="../../../../../../src/adicionarComentarioParaAluno.php" method="post" role="form" class="form-horizontal " enctype="multipart/form-data" >
                    <input type="hidden" name="aluno" value="<?php echo $aluno; ?>" />
                    <input type="hidden" name="disciplina" value="<?php echo $disciplina; ?>" />
                    <input type="hidden" name="turma" value="<?php echo $turma; ?>" />
                    <input type="hidden" name="data" value="<?php echo $data; ?>" />
                    <input type="hidden" name="prof" value="<?php echo $user->professor; ?>" />
                    <div class="form-group row justify-content-center ">
                        <div class="col-md-3">
                            <textarea name="comentario"> </textarea>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label for="fileToUpload" class="col-form-label col-md-2 col-form-label-sm">Selecione arquivo para anexar:</label>
                        <div class="col-md-3">
                            <input type="file" name="fileToUpload" >
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
                </form>
                
                <strong>Lista de comentários</strong> <p/>                
                
                <?php
                    $comentarioQuery = $db->query("
                        select *
                        from diario_de_classe
                        where aluno = $aluno
                        and disciplina = $disciplina
                        and turma = $turma
                        and data = '$data'
                        and professor = $user->professor
                        and contexto = 'observacao'
                    ");
                    $comentarios = $comentarioQuery->fetchAll(PDO::FETCH_OBJ);

                    echo '<table style="margin-left: auto; margin-right: auto; font-size: 13;" class="table">';
                    echo "<thead><tr><th>Comentário</th><th colspan='2'>Arquivos anexados</th><th>Deletar</th></tr></thead><tbody>";
                    foreach ($comentarios as $comentario) {
                        echo "<tr><td>$comentario->observacao</td>";

                        $arquivoQuery = $db->query("
                            select *
                            from arquivos
                            where contexto = 'ddc'
                            and diario = $comentario->id
                        ");
                        $arquivos = $arquivoQuery->fetchAll(PDO::FETCH_OBJ);

                        $span = 'colpan=2';
                        $line = 'Essa observação não contem arquivos anexados';
                        $td = '<td></td>';
                        if ($arquivos) {
                            $span = '';
                            $line = '';
                            $td = '';
                            foreach ($arquivos as $arquivo) {
                               $line .= "<a href='../../../../../$arquivo->endereco'>$arquivo->nome</a></td><td><a href='../../../../../deletar-arquivo/$arquivo->id'><span class='glyphicon glyphicon-trash'></span></a>";
                            }
                        }
                        echo "<td $span>$line</td>$td";
                        echo "<td><a href='../../../../../deletar/$comentario->id'><span class='glyphicon glyphicon-trash'></span></a></td></tr>";
                        //echo "<tr><td></td><td colspan='2'>Anexar arquivo</td><td></td><td></td></tr>";
                    }
                    echo '</tbody></table>';
                ?>
                             
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>
