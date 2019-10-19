<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'professor') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';

$id = $_GET['o'];

$arquivoQuery = $db->query("
select *
from arquivos
where contexto = 'ddc'
and diario = $id
");
$arquivo = $arquivoQuery->fetch(PDO::FETCH_OBJ);

if ($arquivo->endereco_thumb) {
    unlink($arquivo->endereco_thumb);
}
unlink($arquivo->endereco);

$statement = $db->prepare("DELETE FROM arquivos where id=:id");
$statement->execute([
    'id' => $arquivo->id,
]);

$statement = $db->prepare("DELETE FROM diario_de_classe where contexto = 'observacao' and id=:id");
$statement->execute([
    'id' => $id,
]);

$referer = filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL);
$referer = explode("/", $referer);
$referer = array_slice($referer, (sizeof($referer) - 6));
$referer = implode("/", $referer);

header("Location: ../$referer");
