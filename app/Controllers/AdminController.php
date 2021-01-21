<?php

namespace App\Controllers;

use App\Enum;
use App\Templates;
use App\Util;
use App\DB\Storage\TurmaStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\AvatarStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\ArquivoStorage;
use App\DB\Storage\AlunoStorage;
use App\DB\Storage\UsuarioStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\ResponsavelStorage;
use App\DB\Storage\LogStorage;

class AdminController
{
    protected $template;
    protected $util;
    protected $turmaStorage;
    protected $materiaStorage;
    protected $alunoStorage;
    protected $notaStorage;
    protected $diarioStorage;
    protected $enderecoStorage;
    protected $avatarStorage;
    protected $arquivoStorage;
    protected $usuarioStorage;
    protected $professorStorage;
    protected $responsavelStorage;
    protected $links;

    public function __construct()
    {
        $this->template = new Templates();
        $this->util = new Util();
        $this->util->userPermission(Enum::TIPO_ADMIN);
        $this->links = $this->util->generateLinks();
        new LogStorage();
        
        $this->turmaStorage = new TurmaStorage();
        $this->materiaStorage = new MateriaStorage();
        $this->alunoStorage = new AlunoStorage();
        $this->enderecoStorage = new EnderecoStorage();
        $this->avatarStorage = new AvatarStorage();
        $this->notaStorage = new NotaStorage();
        $this->diarioStorage = new DiarioDeClasseStorage();
        $this->arquivoStorage = new ArquivoStorage();
        $this->usuarioStorage = new UsuarioStorage();
        $this->professorStorage = new ProfessorStorage();
        $this->responsavelStorage = new ResponsavelStorage();
    }
    
    public function index()
    {
        $this->util->loadTemplate('admin/index.html');
    }
}
