<?php

if (! empty($_POST)) {
        include('conn.php');
	$login = $_POST["login"];
	$tipo = $_POST["tipo"];
	$id = $_POST["id"];
	$cidadeQuery = $db->query("
            SELECT usuario.idUsuario FROM usuario,$tipo
            WHERE usuario.idUsuario = $tipo.idUsuario
            and usuario.email = '$login'
            and usuario.idUsuario != $id
        ");
	$cidadeQuery = $cidadeQuery->fetchObject();
    
        $res = false;
        
        if ($cidadeQuery) {
            $res = true;
        }
        
        echo $res;
}

