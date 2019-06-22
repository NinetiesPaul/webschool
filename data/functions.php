<?php

ini_set('display_errors', true);


function pegarTurma($int)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $usersQuery = $db->query("
	select * from turma where idTurma=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    $nomeTurma = $usersQuery->serie.'º Série '.$usersQuery->nomeTurma;
    
    return $nomeTurma;
}

function pegarTurmaDoAluno($int)
{ //function parameters, two variables.
    
    include '../../data/conn.php';
    
    $usersQuery = $db->query("
	select * from aluno where idUsuario=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    $turmaQuery = $db->query("
	select * from turma where idTurma=$usersQuery->idTurma
	");
    
    $nomeTurma = '';
    
    if ($turmaQuery==false) {
        $nomeTurma = 'Sem turma';
    } else {
        $turmaQuery = $turmaQuery->fetchObject();
        $nomeTurma = $turmaQuery->serie.'º Série '.$turmaQuery->nomeTurma;
    }
    
    return $nomeTurma;
}

function pegarDisciplina($int)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $usersQuery = $db->query("
	select * from disciplina where idDisciplina=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->nomeDisciplina;
}

function pegaridProfessor($int)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $usersQuery = $db->query("
	select * from professor where idUsuario=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->idProfessor;
}

function pegarNomeProfessor($int)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $usersQuery = $db->query("
	select usuario.* from usuario,professor where usuario.idUsuario=professor.idUsuario and professor.idProfessor=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->nome;
}

function pegarIdDoAluno($int)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $alunoQuery = $db->query("
	select * from aluno where idUsuario=$int
	");
    
    $alunoQuery = $alunoQuery->fetchObject();
    
    return $alunoQuery->idAluno;
}

function pegarNotaDoAluno($aluno, $disciplina, $turma)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $idAluno = pegarIdDoAluno($aluno);
    
    $notaQuery = $db->query("
	select * from notaPorAluno where idTurma=$turma and idAluno=$idAluno and idDisciplina=$disciplina
	");
    
    $count = $notaQuery->rowCount();
    
    $notaQuery = $notaQuery->fetchObject();
    
    $nota = ($count == 0) ? 0 : $notaQuery->nota;
    
    return $nota;
}

function pegarFaltasDoAluno($aluno, $disciplina, $turma)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $idAluno = pegarIdDoAluno($aluno);
    
    $notaQuery = $db->query("
	select count(idAluno) as count from faltaPorAluno where idTurma=$turma and idAluno=$idAluno and idDisciplina=$disciplina
	");
    
    $count = $notaQuery->rowCount();
    
    $notaQuery = $notaQuery->fetchObject();
    
    $nota = ($count == 0) ? 0 : $notaQuery->count;
    
    return $nota;
}

function pegarFaltasDoAluno_($aluno, $disciplina, $turma)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $idAluno = $aluno;
    
    $notaQuery = $db->query("
	select count(idAluno) as count from faltaPorAluno where idTurma=$turma and idAluno=$idAluno and idDisciplina=$disciplina
	");
    
    $count = $notaQuery->rowCount();
    
    $notaQuery = $notaQuery->fetchObject();
    
    $nota = ($count == 0) ? 0 : $notaQuery->count;
    
    return $nota;
}

function pegarNomeDoAluno($int)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $usersQuery = $db->query("
	select usuario.* from usuario,aluno where usuario.idUsuario=aluno.idUsuario and aluno.idAluno=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->nome;
}

function pegarNomeDoResponsavel($int)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $usersQuery = $db->query("
	select usuario.* from usuario,responsavel where usuario.idUsuario=responsavel.idUsuario and responsavel.idUsuario=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->nome;
}

function pegarEstado($int)
{ //function parameters, two variables.

    include '../data/conn.php';
    
    $estadoQuery = $db->query("
	select * from estado where idEstado=$int
	");
    
    $estadoQuery = $estadoQuery->fetchObject();
    
    return $estadoQuery->sigla;
}

function verificarLogin($email)
{ //function parameters, two variables.

    include '../../data/conn.php';
    
    $userQuery = $db->query("
	select idUsuario from usuario where email='$email'
	");
    
    $user = $userQuery->fetchObject();
    
    return $user;
}
