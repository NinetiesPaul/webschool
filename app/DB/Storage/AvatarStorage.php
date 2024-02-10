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

    public function inserirUsuarioNaAvatar($usuario)
    {
        $avatar = $this->db->prepare("INSERT INTO fotos_de_avatar (usuario) VALUES (:idUusuario)");
        $avatar->execute([
            'idUusuario' => $usuario,
        ]);
    }

    public function atualizarAvatar($urlFinal, $urlThumbFinal, $userId)
    {
        $avatarQuery = $this->db->query("
            SELECT *
            FROM fotos_de_avatar
            WHERE usuario=$userId
        ");

        $avatar = $avatarQuery->fetchObject();

        if ($avatar) {
            unlink($avatar->endereco_thumb);
            unlink($avatar->endereco);

            $deleteAvatar = $this->db->prepare("DELETE FROM fotos_de_avatar WHERE usuario=:idUsuario");

            $deleteAvatar->execute([
                'idUsuario' => $userId,
            ]);
        }

        $user = $this->db->prepare("
            INSERT INTO fotos_de_avatar (endereco_thumb, endereco, usuario) VALUES (:imagemThumbUrl, :imagemUrl, :idUsuario)
        ");

        $user->execute([
            'imagemThumbUrl' => $urlThumbFinal,
            'imagemUrl' => $urlFinal,
            'idUsuario' => $userId,
        ]);
    }

    public function verAvatar($id)
    {
        $avatarQuery = $this->db->query("
            SELECT *
            FROM fotos_de_avatar
            WHERE usuario = $id
        ");

        return $avatarQuery->fetch(PDO::FETCH_OBJ);
    }
}
