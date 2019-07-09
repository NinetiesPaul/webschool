<?php
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
            $turma = $_GET['t'];
            $disciplina = $_GET['d']; 
        
        ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../res/css.css" rel="stylesheet">
        <link href="../../res/navbar.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <script src="../../res/detect.js">
        </script>
        <script>
        function pesquisarFrequencia(mes, ano, turma, disc) {
            $.ajax({
                type: "POST",
                url: "../../data/pesquisarFrequencia.php",
                data:'mes='+mes+'&ano='+ano+'&turma='+turma+'&disc='+disc,
                success: function(data ){
                    $("#result").html(data);
                }
            });
        }
        
        $(document).ready(function(){
            $("#search").click(function(){
                console.log($("#mes").val());
                console.log($("#ano").val());
                console.log($("#turma").val());
                console.log($("#disc").val());
                pesquisarFrequencia($("#mes").val(), $("#ano").val(), $("#turma").val(), $("#disc").val());
                
            });
        });
        </script>
        <title>Professor :: Home Page</title>
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
                <strong>Diário de Classe</strong><p/>
                
                <input type="hidden" id="disc" value='<?php echo $disciplina?>' />
                <input type="hidden" id="turma" value='<?php echo $turma ?>' />
                
                <br/><select id="ano">
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                </select>
                
                <select id="mes">
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select><p/>
                
                <input type="button" id="search" value="Pesquisar" />
                
                <div id="result">
                    Resultado aqui.
                </div>
                
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>

<?php


        //echo "<td> <a href='faltas.php' class='btn btn-info'><span class='glyphicon glyphicon-remove'></span> Faltas</a> </td>";
        //echo "<td> <a href='comentarios.php' class='btn btn-info'><span class='glyphicon glyphicon-comment'></span> Comentários</a> </td>";

        } else {
            header('Location: visualizarTurmas.php');
        }
    }
} else {
    header('Location: index.php');
}



	