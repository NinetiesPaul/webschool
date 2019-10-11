<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "admin" || !$tipo) {
    header('Location: ..');
}

include '../../includes/php/conn.php';
include '../../includes/php/functions.php';

if (empty($_GET)) {
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../includes/css/glyphicons.css" rel="stylesheet">
        <link href="../../includes/css/navbar.css" rel="stylesheet">
        <script src="../../includes/js/jquery.js"></script>
        <script>
        function verificarLogin(val) {
            $.ajax({
                type: "POST",
                url: "../../includes/php/ajax/verificarLogin.php",
                data:'login='+val+'&tipo=responsavel',
                success: function(data ){
                    if (data == 1){
                        $("#disponibilidade").show();
                        $("#btn").attr("disabled", true)
                    } else {
                        $("#disponibilidade").hide();
                        $("#btn").attr("disabled", false)
                    }
                }
            });
        }


        $(document).on('focusout', '#email', function(){
              verificarLogin($("#email").val());
        });

        $(document).ready(function(){
           $("#disponibilidade").hide();
        });


        </script>
        <title>webSchool :: Cadastro de Responsável</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="..">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="..">Home</a>
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
                <strong>Cadastro de Responsável</strong> <p/>
                <form action="src/adicionarResponsavel.php" method="post" role="form" class="form-horizontal " >
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm ">Nome:</label>
                        <div class="col-md-3">
                            <input type="text" name="nome" id="nome" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm ">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" class="form-control form-control-sm" aria-describedby="disponibilidade" required>
                            <small id="disponibilidade">
                                Login em uso!
                            </small>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="password" class="col-form-label col-md-2 col-form-label-sm ">Senha:</label>
                        <div class="col-md-3">
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <button type="submit" id="btn" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
                </form>

                <p><strong>Lista de Responsaveis</strong></p>

                <?php

                $usersQuery = $db->query("select usuario.* from usuario, responsavel where usuario.id=responsavel.usuario");
                $responsaveis = $usersQuery->fetchAll(PDO::FETCH_OBJ);

                ?>
                
                <table style="margin-left: auto; margin-right: auto; font-size: 13; width: auto !important;" class="table">
                    <?php
                    
                    foreach ($responsaveis as $responsavel) {
                        echo '<tr><td>'.$responsavel->nome.'</td>'; 
                        echo "<td><a href='responsavel/$responsavel->id' class='btn btn-info btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>"; 
                        echo "<td><a href='deletar-responsavel/$responsavel->id' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></td></tr>";
                    }
                    
                    ?>
                </table>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>		
    </body>
</html>
	

<?php
} else {
    $id = $_GET['id'];

    $usersQuery = $db->query("
        select usuario.* from usuario, responsavel where usuario.id=responsavel.usuario and responsavel.usuario=$id
        ");

    $responsavel = $usersQuery->fetch(PDO::FETCH_OBJ);

    if (empty ($responsavel)) {
        header('Location: ../responsavel');
    }
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../../includes/css/glyphicons.css" rel="stylesheet">
        <link href="../../../includes/css/navbar.css" rel="stylesheet">
        <script src="../../../includes/js/jquery.js">
        </script>
        <script>
        function verificarLogin(val) {
            $.ajax({
                type: "POST",
                url: "../../../includes/php/ajax/verificarLogin.php",
                data:'login='+val+'&tipo=responsavel&id='+<?php echo $id; ?>,
                success: function(data ){
                    if (data == 1){
                        $("#disponibilidade").show();
                        $("#btn").attr("disabled", true)
                    } else {
                        $("#disponibilidade").hide();
                        $("#btn").attr("disabled", false)
                    }
                }
            });
        }


        $(document).on('focusout', '#email', function(){
              verificarLogin($("#email").val());
        });

        $(document).ready(function(){
           $("#disponibilidade").hide();
        });


        </script>
        <title>webSchool :: Alteração de Responsável</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="..">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="..">Home</a>
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
                <strong>Alteração de Responsável</strong> <p/>
                <form action="../src/alterarResponsavel.php" method="post" role="form" class="form-horizontal " >
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="hidden" name="salt" value="<?php echo $responsavel->salt ?>" />
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
                        <div class="col-md-3">
                                <input type="text" name="nome" id="nome" placeholder="Nome" aria-describedby="disponibilidade" value="<?php echo $responsavel->nome; ?>" class="form-control form-control-sm" required >
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" value="<?php echo $responsavel->email; ?>" class="form-control form-control-sm" required >
                            <small id="disponibilidade">
                                Login em uso!
                            </small>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="password" class="col-form-label col-md-2 col-form-label-sm">Senha:</label>
                        <div class="col-md-3">
                                <input type="password" name="password" class="form-control form-control-sm" >
                        </div>
                    </div>
                    <button type="submit" id="btn" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Atualizar</button>
                </form>

                <p><strong>Cadastrar aluno para responsável</strong></p>
                
                <form action="../src/adicionarResponsavelPorAluno.php" method="post" role="form" class="form-horizontal " >
                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <div class="form-group row justify-content-center ">
                            <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
                            <div class="col-md-3" >
                                <select name="aluno" class="form-control form-control-sm">
                                <?php

                                $usersQuery = $db->query("select usuario.* from usuario, aluno where usuario.id=aluno.usuario");
                                $usersQuery = $usersQuery->fetchAll(PDO::FETCH_OBJ);
                                
                                foreach ($usersQuery as $user) {
                                    echo "<option value='$user->id'>$user->nome (".pegarTurmaDoAluno($user->id).")</option>";
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                    <button type="submit" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
                </form>

                <?php

                $responsavelQuery = $db->query("select id from responsavel where usuario=$id");

                $responsavelQuery = $responsavelQuery->fetchObject();

                $usersQuery = $db->query("
                    select distinct usuario.* from usuario, aluno, responsavel_por_aluno
                    where aluno.usuario = usuario.id
                    and aluno.id = responsavel_por_aluno.aluno
                    and responsavel_por_aluno.responsavel=$responsavelQuery->id
                    ");
                
                $usersQuery = $usersQuery->fetchAll(PDO::FETCH_OBJ);

                echo '<table style="margin-left: auto; margin-right: auto; font-size: 13; width: 50%;" class="table">';
                foreach ($usersQuery as $user) {
                    echo '<tr><td>'.$user->nome.'</td><td>'.pegarTurmaDoAluno($user->id).'</td>';
                    echo "<td><a href='../deletar-aluno-responsavel/$id/$user->id' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></td></tr>";
                }
                echo '</table>';
                
                ?>

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>
<?php
}
