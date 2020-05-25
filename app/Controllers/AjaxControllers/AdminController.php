<?php

namespace App\Controllers\AjaxControllers;

use App\Enum;
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

class AdminController extends AjaxController
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

    public function __construct()
    {
        parent::__construct();
        $this->util = new Util();
        $this->util->userPermission(Enum::TIPO_ADMIN);

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
    
    public function removerAluno($idAluno)
    {
        $aluno = $this->alunoStorage->verAluno($idAluno);
        
        $endereco = $this->enderecoStorage->verEndereco($aluno->endereco);
        $avatar = $this->avatarStorage->verAvatar($aluno->id);
        $diario_de_classe = $this->diarioStorage->verDiarioDeClassePorAluno($aluno->aluno);
        $nota_por_aluno = $this->notaStorage->verNotasPorAluno($aluno->aluno);
        $arquivos_do_diario = $this->arquivoStorage->verArquivoPorAluno($aluno->aluno);
        $filho_de = $this->responsavelStorage->verAlunosDoResponsavelPorAluno($aluno->aluno);

        $footprint= [
            'usuario' => $aluno,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'diario_de_classe' => $diario_de_classe,
            'nota_por_aluno' => $nota_por_aluno,
            'arquivos' => $arquivos_do_diario,
            'filho_de' => $filho_de
        ];

        try {
            $this->alunoStorage->removerAluno($aluno->aluno, $aluno->id, $aluno->endereco, $footprint);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
    
    public function desativarAluno($idAluno)
    {
        try {
            $this->alunoStorage->desativarAluno($idAluno);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }
    }
    
    public function removerProfessor($idProfessor)
    {
        $professor = $this->professorStorage->verProfessor($idProfessor);
        
        $endereco = $this->enderecoStorage->verEndereco($professor->endereco);
        $avatar = $this->avatarStorage->verAvatar($professor->id);
        $diario_de_classe = $this->diarioStorage->verDiarioDeClassePorProfessor($professor->professor);
        $disciplina = $this->professorStorage->verProfessorPorMateria($professor->professor);
        $arquivos_do_diario = $this->arquivoStorage->verArquivoPorProfessor($professor->professor);

        $footprint= [
            'usuario' => $professor,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'diario_de_classe' => $diario_de_classe,
            'disciplina' => $disciplina,
            'arquivos' => $arquivos_do_diario
        ];

        try {
            $this->professorStorage->removerProfessor($professor->professor, $professor->id, $professor->endereco, $footprint);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
    
    public function desativarProfessor($idProfessor)
    {
        try {
            $this->professorStorage->desativarProfessor($idProfessor);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }

    public function removerProfessorPorMateria($id)
    {
        try {
            $this->professorStorage->removerProfessorPorMateria($id);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
    
    public function desativarResponsavel($idResponsavel)
    {
        try {
            $this->responsavelStorage->desativarResponsavel($idResponsavel);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
    
    public function removerResponsavel($idResponsavel)
    {
        $responsavel = $this->responsavelStorage->verResponsavel($idResponsavel);
        
        $endereco = $this->enderecoStorage->verEndereco($responsavel->endereco);
        $avatar = $this->avatarStorage->verAvatar($responsavel->id);
        $responsavel_por = $this->responsavelStorage->verAlunosDoResponsavel($responsavel->responsavel);
        
        $footprint= [
            'usuario' => $responsavel,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'responsavel_por' => $responsavel_por
        ];

        try {
            $this->responsavelStorage->removerResponsavel($responsavel->responsavel, $responsavel->id, $responsavel->endereco, $footprint);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }

    public function removerAlunoPorResponsavel($id)
    {
        try {
            $this->responsavelStorage->removerAlunoPorResponsavel($id);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
    
    public function removerTurma($turma)
    {
        try {
            $this->turmaStorage->removerTurma($turma);
        } catch (\Exception $ex) {
            $this->throwError($ex, "turma");
        }

        $this->response();
    }
    
    public function removerMateria($disciplina)
    {
        try {
            $this->materiaStorage->removerMateria($disciplina);
        } catch (\Exception $ex) {
            $this->throwError($ex, "materia");
        }

        $this->response();
    }
}
