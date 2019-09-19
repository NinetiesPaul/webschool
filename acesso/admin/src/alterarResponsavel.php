<?php

ini_set('display_errors', true);

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
            $password = $_POST['password'];
            $salt = $_POST['salt'];
            $newPassword = md5($password);

            include '../../../data/conn.php';
                
            $sql = "
                UPDATE usuario
                SET nome=:nome, email=:email
                ";
            
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
                    
            $user = $db->prepare($sql);
            $user->execute($fields);
        
            header('Location: ../responsavel');
        }
    }
} else {
    header('Location: index.php');
}
