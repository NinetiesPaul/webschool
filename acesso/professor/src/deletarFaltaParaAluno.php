<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'professor') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';

$id = $_GET['falta'];

$diarioQuery = $db->query("
select *
from diario_de_classe
where id = $id
");

$diario = $diarioQuery->fetch(PDO::FETCH_OBJ);

$user = $db->prepare("UPDATE diario_de_classe SET presenca = 0 WHERE id=:id");
$user->execute([
    'id' => $id,
]);

header("Location: ../diario-de-classe/$diario->turma/$diario->disciplina");
