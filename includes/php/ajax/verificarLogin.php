<?php

if (! empty($_POST)) {
    include('../conn.php');
    $login = $_POST["login"];
    $tipo = $_POST["tipo"];
    $id = (isset($_POST["id"])) ? $_POST['id'] : null;
    $query = "
        SELECT usuario.id FROM usuario,$tipo
        WHERE usuario.id = $tipo.usuario
        and usuario.email = '$login'
    ";
    
    if ($id) {
        $query .= " and usuario.id != $id";
    }
    
    $cidadeQuery = $db->query($query);
    $cidadeQuery = $cidadeQuery->fetchObject();
    
    $res = false;
    if ($cidadeQuery) {
        $res = true;
    }
        
    echo $res;
}
