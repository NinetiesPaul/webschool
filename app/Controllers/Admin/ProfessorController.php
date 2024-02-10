<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Enum;
use App\ResponseHandler;
use App\Templates;

class ProfessorController extends AdminController
{
    public function criarProfessor()
    {
        new Templates('admin/professores/criar.html');
    }

    public function verProfessores()
    {
        $professorQuery = $this->professorStorage->verProfessores();

        $professores = '';

        foreach ($professorQuery as $professor) {
            $is_deleted = ($professor->is_deleted) ? "<span class='label-status_$professor->id label-success'>Ativo</span>" : "<span class='label-status_$professor->id label-danger'>Inativo</span>";
            
            $professores .="
                <tr data-id='$professor->id'>
                    <td>$professor->id</td>
                    <td>
                        $professor->nome<br/>
                        $is_deleted
                    </td>
                    <td>
                        <a href='professor/$professor->id' class='btn btn-sm'><span class='glyphicon glyphicon-edit'></span></a>
                        <a href='#' class='btn btn-sm desativar'><span class='glyphicon glyphicon-ban-circle'></span></a>
                    </td>
                </tr>
            ";
        }

        $args = [
            'PROFESSORES' => $professores,
        ];

        new Templates('admin/professores/listar.html', $args);
    }

    public function verProfessor($idProfessor)
    {
        $professor = $this->professorStorage->verProfessor($idProfessor);

        $disciplinaPorProfessorQuery = $this->materiaStorage->verMateriasDoProfessorAdmin($professor->professor);

        $disciplinasProProfessor = '';
        foreach ($disciplinaPorProfessorQuery as $disciplinaProProfessor) {
            $disciplinasProProfessor .= "
                <tr data-id='$disciplinaProProfessor->id'>
                    <td>$disciplinaProProfessor->turma</td>
                    <td>$disciplinaProProfessor->nome</td>
                    <td style='width: 5%'>
                        <a class='btn btn-sm' href='#' id='deletar-dpp'>
                        <span class='glyphicon glyphicon-trash'></span> </a>
                    </td>
                </tr>
            ";
        }

        $turmaQuery = $this->turmaStorage->verTurmas();

        $turmas = '';
        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
        }

        $disciplinaQuery = $this->materiaStorage->verMaterias();

        $disciplinas = '';
        foreach ($disciplinaQuery as $disciplina) {
            $disciplinas .= "<option value='$disciplina->id'>$disciplina->nome</option>";
        }

        $args = [
            'ID' => $professor->id,
            'NOME' => $professor->nome,
            'SALT' => $professor->salt,
            'EMAIL' => $professor->email,
            'PROFESSOR' => $professor->professor,
            'DISCIPLINAS_SELECT' => $disciplinas,
            'TURMAS_SELECT' => $turmas,
            'DISCIPLINAS_PROFESSOR' => $disciplinasProProfessor,
        ];

        new Templates('admin/professores/editar.html', $args, '../');
    }

    public function adicionarProfessor()
    {
        $data = json_decode(json_encode($_POST), true);

        $email = $data['email'];
        $nome = $data['nome'];
        $salt = time() + rand(100, 1000);
        $password = $data['password'];
        $password = md5($password . $salt);

        $this->professorStorage->adicionarProfessor($email, $nome, $password, $salt);
        header('Location: /admin/professores');
    }

    public function atualizarProfessor()
    {
        $data = json_decode(json_encode($_POST), true);

        $userId = $data['id'];
        $nome = $data['nome'];
        $email = $data['email'];
        $password = $data['password'];
        $salt = $data['salt'];

        $this->usuarioStorage->alterarUsuario($userId, $nome, $email, $password, $salt, Enum::TIPO_PROFESSOR);
        header('Location: /admin/professores');
    }

    public function adicionarProfessorPorMateria()
    {
        $data = json_decode(json_encode($_POST), true);

        $disciplina = $data['disciplina'];
        $turma = $data['turma'];
        $professor = $data['professor'];

        $disponivel = $this->professorStorage->verificarMateriaPorProfessor($disciplina, $turma, $professor);

        if (count($disponivel) === 0) {
            $this->professorStorage->adicionarMateriaPorProfessor($disciplina, $turma, $professor);
        }

        header("Location: /admin/professor/$data[id]");
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
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }

    public function desativarProfessor()
    {
        $data = input()->all();

        try {
            $this->professorStorage->desativarProfessor($data['id']);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }

    public function removerProfessorPorMateria($id)
    {
        try {
            $this->professorStorage->removerProfessorPorMateria($id);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }
}
