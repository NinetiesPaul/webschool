<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Enum;
use App\ResponseHandler;
use App\Templates;

class ResponsavelController extends AdminController
{
    public function criarResponsavel()
    {
        new Templates('admin/responsaveis/criar.html');
    }

    public function verResponsaveis()
    {
        $responsavelQuery = $this->responsavelStorage->verResponsaveis();

        $responsaveis = '';

        foreach ($responsavelQuery as $responsavel) {
            $is_deleted = ($responsavel->is_deleted) ? "<span class='label-status_$responsavel->id label-success'>Ativo</span>" : "<span class='label-status_$responsavel->id label-danger'>Inativo</span>";
           
            $responsaveis .= "
                <tr id='row-$responsavel->id'>
                    <td>$responsavel->id </td>
                    <td>
                        $responsavel->nome<br/>
                        $is_deleted
                    </td>
                    <td>
                        <a href='responsavel/$responsavel->id' class='btn btn-sm'><span class='glyphicon glyphicon-edit'></span></a>
                        <a href='#' class='btn btn-sm desativar' id='$responsavel->id' ><span class='glyphicon glyphicon-ban-circle'></span></a>
                    </td>
                </tr>
            ";
        }

        $args = [
            'RESPONSAVEIS' => $responsaveis,
        ];

        new Templates('admin/responsaveis/listar.html', $args);
    }

    public function verResponsavel($idResponsavel)
    {
        $responsavel = $this->responsavelStorage->verResponsavel($idResponsavel);

        $alunosQuery = $this->alunoStorage->verAlunos();

        $alunos = '';
        foreach ($alunosQuery as $aluno) {
            $nome = $aluno->nome . " (" . $aluno->nome_turma . ")";
            $alunos .= "<tr><td>$nome</td><td><button class='btn badge badge-primary escolher_aluno' style='padding: 2px 8px; color: white;' value='$aluno->aluno' id='$nome' data-dismiss='modal' >Escolher</button></td></tr>";
        }

        $alunosDoResponsavel = $this->alunoStorage->verAlunosDoResponsavel($responsavel->responsavel);

        $filhos = '';
        foreach ($alunosDoResponsavel as $user) {
            $filhos .= "<tr id='row-$user->rpa'><td>$user->nome</td><td>$user->nome_turma</td>
            <td><button class='btn btn-danger btn-sm' id='deletar' value='$user->rpa'><span class='glyphicon glyphicon-trash'></span> Deletar</button></td></tr>";
        }

        $endereco = $this->enderecoStorage->verEndereco($responsavel->endereco);
        $avatar = $this->avatarStorage->verAvatar($responsavel->id);
        $responsavel_por = $alunosDoResponsavel;

        $footprint = [
            'usuario' => $responsavel,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'responsavel_por' => $responsavel_por
        ];

        $deletar = "<button class='btn btn-danger btn-sm' id='deletar-responsavel' value='$responsavel->id'><span class='glyphicon glyphicon-trash'></span> Deletar responsável</button>";

        $args = [
            'ID' => $responsavel->id,
            'NOME' => $responsavel->nome,
            'SALT' => $responsavel->salt,
            'RESPONSAVEL' => $responsavel->responsavel,
            'EMAIL' => $responsavel->email,
            'ALUNOS' => $alunos,
            'FILHOS' => $filhos,
            'FOOTPRINT' => json_encode($footprint),
            'BOTAO_DELETAR' => $deletar,
        ];

        new Templates('admin/responsaveis/editar.html', $args, '../');
    }

    public function adicionarResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);

        $email = $data['email'];
        $nome = $data['nome'];
        $password = $data['password'];
        $salt = time() + rand(100, 1000);
        $password = md5($password . $salt);

        $this->responsavelStorage->adicionarResponsavel($email, $nome, $password, $salt);
        header('Location: /admin/responsaveis');
    }

    public function atualizarResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);

        $userId = $data['id'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];

        $this->usuarioStorage->alterarUsuario($userId, $nome, $email, $password, $salt, Enum::TIPO_RESPONSAVEL);
        header('Location: /admin/responsaveis');
    }

    public function adicionarAlunoPorResponsavel()
    {
        $data = json_decode(json_encode($_POST), true);

        $responsavel = $data['responsavel'];
        $aluno = $data['aluno'];
        $id = $data['id'];

        $responsavel = $this->responsavelStorage->adicionarAlunoPorResponsavel($responsavel, $aluno, $id);
        header("Location: /admin/responsavel/$responsavel");
    }

    public function desativarResponsavel($idResponsavel)
    {
        try {
            $this->responsavelStorage->desativarResponsavel($idResponsavel);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
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
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }

    public function removerAlunoPorResponsavel($id)
    {
        try {
            $this->responsavelStorage->removerAlunoPorResponsavel($id);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }
}
