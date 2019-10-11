<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';
include '../../../data/functions.php';

$email = $_POST['email'];

$exists = verificarLoginOnPost('aluno', $email);

if ($exists) {
    header('Location: ../aluno');
    exit();
}
        
$endereco = $db->prepare("INSERT INTO endereco (estado)
    VALUES (:estado)");

$endereco->execute([
    'estado' => 1,
]);

$idEndereco = (int) $db->lastInsertId();

$nome = $_POST['nome'];
$password = $_POST['password'];
$salt = time() + rand(100, 1000);
$password = md5($password . $salt);
$turma = $_POST['turma'];

$user = $db->prepare("
    INSERT INTO usuario (nome, email, pass, endereco, salt)
    VALUES (:name, :email, :password, :endereco, :salt)
");

$user->execute([
    'name' => $nome,
    'email' => $email,
    'password' => $password,
    'endereco' => $idEndereco,
    'salt' => $salt,
]);

$userId = (int) $db->lastInsertId();

$avatar = $db->prepare("INSERT INTO fotos_de_avatar (usuario) VALUES (:idUusuario)");
$avatar->execute([
    'idUusuario' => $userId,
]);


$aluno = $db->prepare("INSERT INTO aluno (usuario, turma) VALUES (:idUusuario, :idTurma)");
$aluno->execute([
    'idUusuario' => $userId,
    'idTurma' => $turma,
]);

$lastid = (int) $db->lastInsertId();

$disciplinasQuery = $db->query("SELECT * FROM disciplina_por_professor where turma = $turma");
$disciplinas = $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);

foreach ($disciplinas as $disciplina) {
    $nota = $db->prepare("INSERT INTO nota_por_aluno (aluno, disciplina, turma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, 0, 0, 0, 0, 0, 0, 0, 0)");

    $nota->execute([
        'idAluno' => $lastid,
        'idDisciplina' => $disciplina->disciplina,
        'idTurma' => $turma,
    ]);
    
    $diario = $db->prepare("INSERT INTO diario_de_classe (aluno, disciplina, turma, data, contexto, presenca) VALUES (:idAluno, :idDisciplina, :idTurma, NOW(), 'presenca', 0)");

    $diario->execute([
        'idAluno' => $lastid,
        'idDisciplina' => $disciplina->disciplina,
        'idTurma' => $turma,
    ]);
}

header('Location: ../aluno');
