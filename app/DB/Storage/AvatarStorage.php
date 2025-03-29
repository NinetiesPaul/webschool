<?php

namespace App\DB\Storage;

use App\DB\DB;
use App\Util;
use PDO;

class AvatarStorage extends DB
{
    public function inserirUsuarioNaAvatar($usuario)
    {
        $avatar = $this->db->prepare("INSERT INTO fotos_de_avatar (usuario) VALUES (:idUusuario)");
        $avatar->execute([
            'idUusuario' => $usuario,
        ]);
    }

    // todo: precisa mesmo excluir e depois reinserir?
    public function atualizarAvatar($urlFinal, $urlThumbFinal, $userId)
    {
        $avatarQuery = $this->db->query("
            SELECT *
            FROM fotos_de_avatar
            WHERE usuario=$userId
        ");

        $avatar = $avatarQuery->fetch(PDO::FETCH_OBJ);;

        if ($avatar) {
            $util = new Util();
            $util->removerArquivo($avatar->endereco_thumb);
            $util->removerArquivo($avatar->endereco);

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
