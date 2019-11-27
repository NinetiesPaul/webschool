<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "admin" || !$tipo) {
    header('Location: ..');
}

include '../../includes/php/functions.php';
include '../../includes/php/conn.php';

if (empty($_GET)) {
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
        <link href="../../includes/css/glyphicons.css" rel="stylesheet">
        <link href="../../includes/css/navbar.css" rel="stylesheet">
        <link href="../../includes/css/datatables.css" rel="stylesheet">
        <script src="../../includes/js/jquery.js"></script>
        <script>
        function verificarLogin(val) {
            $.ajax({
                type: "POST",
                url: "../../includes/php/ajax/verificarLogin.php",
                data:'login='+val+'&tipo=aluno',
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
        
        $(document).ready( function () {
            $('#tableAlunos').DataTable({
                "paging": true,
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
                }
            });
        });


        </script>
        <title>webSchool :: Cadastro de Alunos</title>
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

                <strong>Cadastro de Aluno</strong> <p/>
                <form action="src/adicionarAluno.php" method="post" role="form" class="form-horizontal " >
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm ">Nome:</label>
                        <div class="col-md-3">
                            <input type="text" name="nome" id="nome" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm ">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" class="form-control form-control-sm " aria-describedby="disponibilidade" required>
                            <small id="disponibilidade">
                                Login em uso!
                            </small>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="password" class="col-form-label col-md-2 col-form-label-sm required">Senha:</label>
                        <div class="col-md-3">
                            <input type="password" name="password" class="form-control form-control-sm " required>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center ">
                        <label for="turma" class="col-form-label col-md-2 col-form-label-sm ">Turma:</label>
                        <div class="col-md-3">
                            <select name="turma" class="form-control form-control-sm ">
                            <?php
                            $turmaQuery = $db->query("select * from turma order by serie");
                            $turmas = $turmaQuery->fetchAll(PDO::FETCH_OBJ);

                            foreach ($turmas as $turma) {
                                echo "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
                            }
                            ?>	
                            </select>
                        </div>
                    </div>

                    <button type="submit" id="btn" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
                </form>

                <strong>Lista de Alunos</strong><p/>

                <table style="margin-left: auto; margin-right: auto; font-size: 13; width: auto !important;" class="table hover" id="tableAlunos">
                    <thead><tr><td>Nome</td><td>Turma</td><td></td><td></td></tr></thead><tbody>
                <?php

                $usersQuery = $db->query("select usuario.* from usuario, aluno where usuario.id=aluno.usuario");
                $alunos = $usersQuery->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($alunos as $aluno) {
                    echo '<tr><td>'.$aluno->nome.'</td>';
                    echo '<td>'.pegarTurmaDoAluno($aluno->id).'</td>';
                    echo "<td><a href='aluno/$aluno->id' class='btn btn-info btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>";
                    echo "<td><a href='deletar-aluno/$aluno->id' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></a></td></tr>";
                }
                
                ?>	
                    </tbody>
                </table>

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
        <script type="application/json" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
    </body>
</html>
	

<?php
} else {
    $id = $_GET['id'];

    $usersQuery = $db->query("
        select usuario.*,aluno.id from usuario, aluno where usuario.id=aluno.usuario and usuario.id=$id
    ");

    $aluno = $usersQuery->fetch(PDO::FETCH_OBJ);;

    if (empty ($aluno)) {
        header('Location: ../aluno');
    }
?>
<html lang="en">
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
                data:'login='+val+'&tipo=aluno&id='+<?php echo $id; ?>,
                success: function(data ){
                    if (data == 1){
                        $("#disponibilidade").show();
                        $("#btn").attr("disabled", true);
                    } else {
                        $("#disponibilidade").hide();
                        $("#btn").attr("disabled", false);
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
        <title>webSchool :: Alteração de Aluno</title>
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
                <strong>Alteração de Aluno</strong><p/>
                <form action="../src/alterarAluno.php" method="post" role="form" class="form-horizontal " >
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="hidden" name="idAluno" value="<?php echo $aluno->id ?>" />
                    <input type="hidden" name="salt" value="<?php echo $aluno->salt ?>" />
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
                        <div class="col-md-3">
                                <input type="text" name="nome" id="nome" class="form-control form-control-sm" value="<?php echo $aluno->nome; ?>" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" class="form-control form-control-sm" aria-describedby="disponibilidade" value="<?php echo $aluno->email; ?>" required>
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
                    <?php

                    $turmaQuery = $db->query("select * from turma order by serie");
                    $turmas = $turmaQuery->fetchAll(PDO::FETCH_OBJ);
                    
                    ?>
                    <div class="form-group row justify-content-center ">
                        <label for="password" class="col-form-label col-md-2 col-form-label-sm">Turma:</label>
                        <div class="col-md-3">
                            <select name="turma" class="form-control form-control-sm"  aria-describedby="avisoTurma">
                            <?php
                            foreach($turmas as $turma) {
                                echo "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";   
                            }
                            ?>
                            </select> 
                            <small id="avisoTurma" class="form-text text-muted">(Atualmente <?php echo pegarTurmaDoAluno($id); ?>)</small>
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
