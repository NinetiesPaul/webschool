<?php

ini_set('display_errors', true);

function pegarTurma($int)
{ 

    include 'conn.php';
    
    $usersQuery = $db->query("
	select * from turma where id=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    $nomeTurma = $usersQuery->serie.'º Série '.$usersQuery->nome;
    
    return $nomeTurma;
}

function pegarTurmaDoAluno($int)
{ 
    
    include 'conn.php';
    
    $usersQuery = $db->query("
	select * from aluno where usuario=$int
    ");
    $usersQuery = $usersQuery->fetchObject();
    
    $turmaQuery = $db->query("
	select * from turma where id=$usersQuery->turma
    ");
    $turmaQuery = $turmaQuery->fetchObject();
    
    $nomeTurma = 'Sem turma';
    
    if ($turmaQuery) {
        $nomeTurma = " na " . $turmaQuery->serie.'º Série '.$turmaQuery->nome;
    }
    
    return $nomeTurma;
}

function pegarDisciplina($int)
{ 

    include 'conn.php';
    
    $usersQuery = $db->query("
	select * from disciplina where id=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->nome;
}

function pegarNomeProfessor($int)
{ 

    include 'conn.php';
    
    $usersQuery = $db->query("
	select usuario.* from usuario,professor where usuario.id=professor.usuario and professor.id=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->nome;
}

function pegarIdDoAluno($int)
{ 

    include 'conn.php';
    
    $alunoQuery = $db->query("
	select * from aluno where usuario=$int
	");
    
    $alunoQuery = $alunoQuery->fetchObject();
    
    return $alunoQuery->id;
}

function pegarNotaDoAluno($aluno, $disciplina, $turma)
{ 

    include 'conn.php';
    
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
{ 

    include 'conn.php';
    
    $idAluno = pegarIdDoAluno($aluno);
    
    $notaQuery = $db->query("
	select count(idAluno) as count from faltaPorAluno where idTurma=$turma and idAluno=$idAluno and idDisciplina=$disciplina
	");
    
    $count = $notaQuery->rowCount();
    
    $notaQuery = $notaQuery->fetchObject();
    
    $nota = ($count == 0) ? 0 : $notaQuery->count;
    
    return $nota;
}

function pegarNomeDoAluno($int)
{ 

    include 'conn.php';
    
    $usersQuery = $db->query("
	select usuario.* from usuario,aluno where usuario.id=aluno.usuario and aluno.id=$int
	");
    
    $usersQuery = $usersQuery->fetchObject();
    
    return $usersQuery->nome;
}

function pegarEstado($int)
{ 

    include '../includes/php/conn.php';
    
    $estadoQuery = $db->query("
	select * from estado where id=$int
	");
    
    $estadoQuery = $estadoQuery->fetchObject();
    
    return $estadoQuery->sigla;
}

function verificarLoginOnPost($tipo, $email, $id = false)
{ 

    include 'conn.php';
    
    $query = "
	SELECT usuario.id FROM usuario,$tipo
            WHERE usuario.id = $tipo.id
            and usuario.email = '$email'
	";
    
    
    if ($id) {
        $query .= " and usuario.id != $id";
    }
    
    $userQuery = $db->query($query);
    
    $user = $userQuery->fetchObject();
    
    return $user;
}

