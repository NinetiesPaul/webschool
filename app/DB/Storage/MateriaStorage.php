<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\DB\Storage;

use App\DB\DB;
use PDO;
/**
 * Description of MateriaStorage
 *
 * @author Paul Richard
 */
class MateriaStorage extends DB
{    
    public function verMaterias()
    {
        $disciplinaQuery = $this->connect()->query("select * from disciplina");
        return $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);
    }

    public function verMateria(int $materia)
    {
        $disciplinaQuery = $this->connect()->query("select * from disciplina where id = $materia");
        return $disciplinaQuery->fetch(PDO::FETCH_OBJ);
    }

    public function adicionarMateria($data)
    {
        $data = json_decode(json_encode($_POST), true);
        
        $user = $this->connect()->prepare("INSERT INTO disciplina (nome)
                VALUES (:nome)");

        $user->execute([
            'nome' => $data['nome'],
        ]);
    }

    public function alterarMateria($data)
    {
        $data = json_decode(json_encode($_POST), true);

        $user = $this->connect()->prepare("
            UPDATE disciplina
            SET nome=:nome
            where id=:disciplina
            ");

        $user->execute([
            'nome' => $data['nome'],
            'disciplina' => $data['id'],
        ]);
    }

    public function removerMateria($materia)
    {
        $user = $this->connect()->prepare("DELETE FROM disciplina WHERE id=:id");

        $user->execute([
            'id' => $materia,
        ]);
    }
}
