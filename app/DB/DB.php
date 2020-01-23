<?php

namespace App\DB;

use PDO;
use App\Util;
use App\DB\Storage\AlunoStorage;
use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\UsuarioStorage;
use App\DB\Storage\AvatarStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\TurmaStorage;

class DB extends PDO
{
    public function __construct()
    {
        $localhost_db = getenv('DB_HOST');
        $dbname_db = getenv('DB_NAME');
        $user_db = getenv('DB_USER');
        $password_db = getenv('DB_PASSWORD');
        parent::__construct("mysql:host=$localhost_db; dbname=$dbname_db", $user_db, $password_db, []);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function connect()
    {
        return new DB();
    }
    
    public function util()
    {
        return new Util();
    }
    
    public function endereco()
    {
        return new EnderecoStorage();
    }
    
    public function usuario()
    {
        return new UsuarioStorage();
    }
    
    public function avatar()
    {
        return new AvatarStorage();
    }
    
    public function materia()
    {
        return new MateriaStorage;
    }
    
    public function nota()
    {
        return new NotaStorage();
    }
    
    public function diario()
    {
        return new DiarioDeClasseStorage();
    }
    
    public function turma()
    {
        return new TurmaStorage();
    }
}
