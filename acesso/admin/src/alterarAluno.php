<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/functions.php';
include '../../../data/conn.php';

$userId = $_POST['id'];
$idAluno = $_POST['idAluno'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$password = $_POST['password'];
$salt = $_POST['salt'];
$turma = $_POST['turma'];

$exists = verificarLoginOnPost('aluno', $email, $userId);

if ($exists) {
    header('Location: ../aluno');
    exit();
}

$sql = 'UPDATE usuario
    SET nome=:nome, email=:email';

$fields = [
    'nome' => $nome,
    'email' => $email,
];

if (strlen($password) > 0) {
    $password = $_POST['password'];
    $password = md5($password . $salt);

    $sql .= ' ,pass=:pass';
    $fields['pass'] = $password;
}

$sql .= ' where id=:userId';
$fields['userId'] = $userId;

$alunoQuery = $db->prepare($sql);
$alunoQuery->execute($fields);


$turmaQuery = $db->prepare("UPDATE aluno SET turma=:idTurma WHERE usuario=:idUusuario");
$turmaQuery->execute([
    'idTurma' => $turma,
    'idUusuario' => $userId,
]);

$disciplinasQuery = $db->query("SELECT * FROM disciplina_por_professor where turma = $turma");
$disciplinas = $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);

foreach ($disciplinas as $disciplina) {
    $checkDisciplinaQuery = $db->query("SELECT id FROM nota_por_aluno WHERE turma = $turma and aluno = $idAluno and disciplina = $disciplina->disciplina");
    $checkDisciplina = $checkDisciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    
    if (empty($checkDisciplina)) {        
        $nota = $db->prepare("INSERT INTO nota_por_aluno (aluno, disciplina, turma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, 0, 0, 0, 0, 0, 0, 0, 0)");

        $nota->execute([
            'idAluno' => $idAluno,
            'idDisciplina' => $disciplina->disciplina,
            'idTurma' => $turma,
        ]);
    }
        
    $checkDiarioQuery = $db->query("SELECT id FROM diario_de_classe WHERE turma = $turma and aluno = $idAluno and disciplina = $disciplina->disciplina");
    $checkDiario = $checkDiarioQuery->fetchAll(PDO::FETCH_OBJ);

    if (empty($checkDiario)) {        
        $diario = $db->prepare("INSERT INTO diario_de_classe (aluno, disciplina, turma, data, presenca, contexto) VALUES (:idAluno, :idDisciplina, :idTurma, NOW(), 0, 'presenca')");

        $diario->execute([
            'idAluno' => $idAluno,
            'idDisciplina' => $disciplina->disciplina,
            'idTurma' => $turma,
        ]);
    }
}

header('Location: ../aluno');
