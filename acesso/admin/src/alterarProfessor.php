<?php

include '../../../data/functions.php';
include '../../../data/conn.php';

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];

        if (!empty($_POST)) {
            $userId = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            
            $exists = verificarLoginOnPost('professor', $email, $userId);
        
            if ($exists) {
                header('Location: ../professor');
                exit();
            }
            
            $password = $_POST['password'];
            $salt = $_POST['salt'];
            $newPassword = md5($password);

                
            $sql = "
                    UPDATE usuario
                    SET nome=:nome, email=:email";
        
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
        
            $fields['userId'] = $userId;
            $sql .= ' where idUsuario=:userId';
                
            $professorQuery = $db->prepare($sql);
            $professorQuery->execute($fields);
        
            header('Location: ../professor');
        }
    }
} else {
    header('Location: index.php');
}
