<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\DB\Storage\AlunoStorage;
use App\DB\Storage\ArquivoStorage;
use App\DB\Storage\AvatarStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\ResponsavelStorage;
use App\DB\Storage\TurmaStorage;
use App\DB\Storage\UsuarioStorage;
use App\Enum;
use App\ResponseHandler;
use App\Templates;

class AlunoController extends AdminController
{
    public function criarAluno()
    {
        $turmaStorage = new TurmaStorage();
        $turmaQuery = $turmaStorage->verTurmas();

        $turmas = '';
        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
        }

        new Templates('admin/alunos/criar.html', [
            'TURMAS' => $turmas,
        ]);
    }

    public function verAlunos()
    {
        $turmaStorage = new TurmaStorage();
        $turmaQuery = $turmaStorage->verTurmas();

        $turmas = '';
        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
        }

        $alunoStorage = new AlunoStorage();
        $alunoQuery = $alunoStorage->verAlunos();

        $alunos = '';
        foreach ($alunoQuery as $aluno) {
            $is_deleted = ($aluno->is_deleted) ? "<span class='label-status_$aluno->id label-success'>Ativo</span>" : "<span class='label-status_$aluno->id label-danger'>Inativo</span>";
            
            $alunos .= "
                <tr data-id='$aluno->id'>
                    <td>$aluno->id</td>
                    <td>
                        $aluno->nome<br/>
                        $is_deleted
                        </td>
                    <td>$aluno->nome_turma</td>
                    <td>
                        <a href='aluno/$aluno->id' class='btn btn-sm'><span class='glyphicon glyphicon-edit'></span></a>
                        <a href='#' class='btn btn-sm desativar' ><span class='glyphicon glyphicon-ban-circle'></span> </a>
                    </td>
                </tr>
            ";
        }

        $args = [
            'TURMAS' => $turmas,
            'ALUNOS' => $alunos,
        ];

        new Templates('admin/alunos/listar.html', $args);
    }

    public function verAluno($idAluno)
    {
        $alunoStorage = new AlunoStorage();
        $aluno = $alunoStorage->verAluno($idAluno);

        $turmaStorage = new TurmaStorage();
        $turmaQuery = $turmaStorage->verTurmas();

        $turmas = '';
        foreach ($turmaQuery as $turma) {
            $selected = ($aluno->turma == $turma->id)? 'selected' : '';
            $turmas .= "<option value='$turma->id' $selected>$turma->serie º Série $turma->nome</option>";
        }

        $args = [
            'ID' => $aluno->id,
            'IDALUNO' => $aluno->aluno,
            'NOME' => $aluno->nome,
            'SALT' => $aluno->salt,
            'EMAIL' => $aluno->email,
            'TURMAS' => $turmas,
        ];

        new Templates('admin/alunos/editar.html', $args, '../');
    }

    public function adicionarAluno()
    {
        $data = json_decode(json_encode($_POST), true);

        $email = $data['email'];
        $nome = $data['nome'];
        $salt = time() + rand(100, 1000);
        $password = $data['password'];
        $password = md5($password . $salt);
        $turma = $data['turma'];

        $alunoStorage = new AlunoStorage();
        $alunoStorage->adicionarAluno($email, $nome, $password, $salt, $turma);
        header('Location: /admin/alunos');
    }

    public function atualizarAluno()
    {
        $data = json_decode(json_encode($_POST), true);

        $userId = $data['id'];
        $idAluno = $data['idAluno'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];
        $turma = $data['turma'];

        $usuarioStorage = new UsuarioStorage();
        $usuarioStorage->alterarUsuario($userId, $nome, $email, $password, $salt, Enum::TIPO_ALUNO);

        $alunoStorage = new AlunoStorage();
        $alunoStorage->alterarAluno($userId, $idAluno, $turma);
        header('Location: /admin/alunos');
    }

    public function removerAluno($idAluno)
    {
        $alunoStorage = new AlunoStorage();
        $aluno = $alunoStorage->verAluno($idAluno);

        $enderecoStorage = new EnderecoStorage();
        $endereco = $enderecoStorage->verEndereco($aluno->endereco);

        $avatarStorage = new AvatarStorage();
        $avatar = $avatarStorage->verAvatar($aluno->id);

        $diarioStorage = new DiarioDeClasseStorage();
        $diario_de_classe = $diarioStorage->verDiarioDeClassePorAluno($aluno->aluno);

        $notaStorage = new NotaStorage();
        $nota_por_aluno = $notaStorage->verNotasPorAluno($aluno->aluno);

        $arquivoStorage = new ArquivoStorage();
        $arquivos_do_diario = $arquivoStorage->verArquivoPorAluno($aluno->aluno);

        $responsavelStorage = new ResponsavelStorage();
        $filho_de = $responsavelStorage->verResponsaveisPeloAluno($aluno->aluno);

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
            $alunoStorage = new AlunoStorage();
            $alunoStorage->removerAluno($aluno->aluno, $aluno->id, $aluno->endereco, $footprint);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }

    public function desativarAluno()
    {
        $data = input()->all();

        try {
            $alunoStorage = new AlunoStorage();
            $alunoStorage->desativarAluno($data['id']);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }
}
