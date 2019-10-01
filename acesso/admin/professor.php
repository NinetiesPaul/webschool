<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "admin" || !$tipo) {
    header('Location: ..');
}

$userId = $_SESSION['user_id'];
include '../../data/functions.php';
include '../../data/conn.php';

if (empty($_GET)) {
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../css/glyphicons.css" rel="stylesheet">
        <link href="../../res/navbar.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <script>
        function verificarLogin(val) {
            $.ajax({
                type: "POST",
                url: "../../../data/verificarLogin.php",
                data:'login='+val+'&tipo=professor',
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
        <title>webSchool :: Cadastro de Professor</title>
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
                <strong>Cadastro de Professor</strong><p/>
                <form action="src/adicionarProfessor.php" method="post" role="form" class="form-horizontal " >
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm ">Nome:</label>
                        <div class="col-md-3">
                                <input type="text" name="nome" id="nome" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" class="form-control form-control-sm" aria-describedby="disponibilidade" required>
                            <small id="disponibilidade">
                                Login em uso!
                            </small>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Senha:</label>
                        <div class="col-md-3">
                                <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <button type="submit" id="btn" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
                </form>

                <strong>Lista de Professores</strong><p/>

                <?php

                $usersQuery = $db->query("select usuario.* from usuario, professor where usuario.idUsuario=professor.idUsuario");
                $usersQuery = $usersQuery->fetchAll(PDO::FETCH_OBJ);

                ?>

                <table style="margin-left: auto; margin-right: auto; font-size: 13; width: auto !important;" class="table table-condensed">
                <?php
                
                foreach ($usersQuery as $user) {
                    echo '<tr><td>'.$user->nome.'</td>';
                    echo "<td><a href='professor/$user->idUsuario' class='btn btn-info btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>";
                    echo "<td><a href='deletar-professor/$user->idUsuario' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></td></tr></a> ";
                }
                
                ?>	
                </table>

                <p><strong>Cadastrar Professor por Disciplina de Turma</strong></p>

                <?php

                $disciplinaQuery = $db->query("select * from disciplina order by nomeDisciplina");
                $disciplinaQuery = $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);

                $turmaQuery = $db->query("select * from turma order by serie");
                $turmaQuery = $turmaQuery->fetchAll(PDO::FETCH_OBJ);

                ?>

                <form action="src/adicionarProfessorPorDisciplinaDeTurma.php" method="post" role="form" class="form-horizontal " >
                    <div class="form-group row justify-content-center ">
                        <label for="professor" class="col-form-label col-md-2 col-form-label-sm ">Professor:</label>
                        <div class="col-md-3">
                            <select name="professor" class="form-control form-control-sm">
                                <?php foreach ($usersQuery as $user) : ?>
                                    <option value="<?php echo pegaridProfessor($user->idUsuario); ?>"><?php echo $user->nome; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                        <div class="form-group row justify-content-center ">
                            <label for="disciplina" class="col-form-label col-md-2 col-form-label-sm">Disciplina:</label>
                            <div class="col-md-3" >
                                <select name="disciplina" class="form-control form-control-sm">
                                    <?php foreach ($disciplinaQuery as $disciplina) : ?>
                                        <option value="<?php echo $disciplina->idDisciplina; ?>">
                                        <?php echo $disciplina->nomeDisciplina ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center ">
                                <label for="turma" class="col-form-label col-md-2 col-form-label-sm">Turma:</label>
                                <div class="col-md-3">
                                        <select name="turma" class="form-control form-control-sm">
                                        <?php foreach ($turmaQuery as $turma) : ?>
                                                <option value="<?php echo $turma->idTurma; ?>">
                                                <?php echo pegarTurma($turma->idTurma); ?>
                                                </option>
                                        <?php endforeach; ?>	
                                        </select>
                                </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button><br/>

                </form>

                <strong>Lista de Professor por Disciplina de Turma</strong><p/>

                <?php

                $profDiscQuery = $db->query("select * from disciplinaporprofessor order by idTurma");
                $profDiscQuery = $profDiscQuery->fetchAll(PDO::FETCH_OBJ);
                
                ?>

                <table style="margin-left: auto; margin-right: auto; font-size: 13; width: auto !important;" class="table">
                    <?php
                    
                    foreach ($profDiscQuery as $profDisc) {
                        echo "<tr><td>".pegarNomeProfessor($profDisc->idProfessor)."</td>";
                        echo "<td>".pegarDisciplina($profDisc->idDisciplina)."</td>";
                        echo "<td>".pegarTurma($profDisc->idTurma)."</td>";
                        echo "<td><a href='deletar-disciplina-professor/$profDisc->idDisciplinaPorProfessor' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></td></tr>";
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
        select usuario.* from usuario, professor where usuario.idUsuario=professor.idUsuario and usuario.idUsuario=$id
        ");

    $usersQuery = $usersQuery->fetchObject();

    if (empty ($usersQuery)) {
        header('Location: ../professor');
    }
?>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../../css/glyphicons.css" rel="stylesheet">
        <link href="../../../res/navbar.css" rel="stylesheet">
        <script src="../../../res/jquery.js">
        </script>
        <script>
        function verificarLogin(val) {
            $.ajax({
                type: "POST",
                url: "../../data/verificarLoginEmAlteracao.php",
                data:'login='+val+'&tipo=professor&id='+<?php echo $id; ?>,
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
        <title>webSchool :: Alteração de Professor</title>
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
                            <a class="dropdown-item" href="../">Home</a>
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
                <strong>Alteração de professor</strong><p/>
                <form action="../src/alterarProfessor.php" method="post" role="form" class="form-horizontal " >
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="hidden" name="salt" value="<?php echo $usersQuery->salt ?>" />
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
                        <div class="col-md-3">
                                <input type="text" name="nome" id="nome" value="<?php echo $usersQuery->nome; ?>" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" value="<?php echo $usersQuery->email; ?>" class="form-control form-control-sm" required >
                            <small id="disponibilidade">
                                Login em uso!
                            </small>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="password" class="col-form-label col-md-2 col-form-label-sm">Senha:</label>
                        <div class="col-md-3">
                                <input type="password" name="password" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="submit" id="btn" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Atualizar</button>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>
<?php
}  
