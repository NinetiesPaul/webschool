<?php

namespace App\DB\Storage;

use APP\DB\DB;
use PDO;

class AvatarStorage
{
    protected $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function inserirAvatar($usuario)
    {
        $avatar = $this->db->prepare("INSERT INTO fotos_de_avatar (usuario) VALUES (:idUusuario)");
        $avatar->execute([
            'idUusuario' => $usuario,
        ]);
    }

    public function verAvatar($id)
    {
        $avatarQuery = $this->db->query("select * from fotos_de_avatar where usuario = $id");
        return $avatarQuery->fetch(PDO::FETCH_OBJ);
    }
}
