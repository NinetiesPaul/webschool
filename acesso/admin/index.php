<?php
session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: ../../index.php');
    } else {
        $userId = $_SESSION['user_id']; ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../res/navbar.css" rel="stylesheet">
        <link href="../../res/css.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <script>
        var isMobile = {
Android: function() {
    return navigator.userAgent.match(/Android/i);
},
BlackBerry: function() {
    return navigator.userAgent.match(/BlackBerry/i);
},
iOS: function() {
    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
},
Opera: function() {
    return navigator.userAgent.match(/Opera Mini/i);
},
Windows: function() {
    return navigator.userAgent.match(/IEMobile/i);
},
any: function() {
    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
}
        };

$(document).ready(function(){
        $(".mobile").hide();
        if (isMobile.any() == null) {
                $(".mobile").hide();
                console.log(isMobile.any());
        } else {
                $(".mobile").show();
        };
});

        </script>
        <title>webSchool :: Home Page</title>
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
                <a href="turma.php" class="btn btn-light btn btn-block">Turmas</a>
                <a href="disciplina.php" class="btn btn-light btn btn-block">Disciplinas</a>
                <a href="professor.php" class="btn btn-light btn btn-block">Professores</a>
                <a href="aluno.php" class="btn btn-light btn btn-block">Alunos</a>
                <a href="responsavel.php" class="btn btn-light btn btn-block">Responsáveis</a>
                <a href="../../logout.php" class="mobile btn btn-danger btn btn-block" >Sair</a>
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
