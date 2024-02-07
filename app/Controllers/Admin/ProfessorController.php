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
        $professores_select = '';
        $professor_array = [];

        foreach ($professorQuery as $professor) {
            $data_token = strtolower($professor->nome);
            $is_deleted = ($professor->is_deleted) ? "<span class='label-status_$professor->id label-success'>Ativo</span>" : "<span class='label-status_$professor->id label-danger'>Inativo</span>";
            
            $professores .="
                <tr id='row-$professor->id'>
                    <td>$professor->id</td>
                    <td>
                        $professor->nome<br/>
                        $is_deleted
                    </td>
                    <td>
                        <a href='professor/$professor->id' class='btn btn-sm'><span class='glyphicon glyphicon-edit'></span></a>
                        <a href='#' class='btn btn-sm desativar' id='$professor->id'><span class='glyphicon glyphicon-ban-circle'></span></a>
                    </td>
                </tr>
            ";


            $professores_select .= "<option data-tokens='$data_token' value='$professor->professor'>$professor->nome</option>";
            $professor_array[$professor->professor] = $professor->nome;
        }

        $turmaQuery = $this->turmaStorage->verTurmas();

        $turmas = '';
        $turma_array = [];

        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
            $turma_array[$turma->id] = "$turma->serie º Série $turma->nome";
        }

        $disciplinaQuery = $this->materiaStorage->verMaterias();

        $disciplinas = '';
        $disciplina_array = [];

        foreach ($disciplinaQuery as $disciplina) {
            $disciplinas .= "<option value='$disciplina->id'>$disciplina->nome</option>";
            $disciplina_array[$disciplina->id] = $disciplina->nome;
        }

        $disciplinaPorProfessorQuery = $this->materiaStorage->verMateriasPorProfessor();

        $disciplinasProProfessor = '';

        foreach ($disciplinaPorProfessorQuery as $disciplinaProProfessor) {
            $dpp = $disciplinaProProfessor->professor;
            $dpd = $disciplinaProProfessor->disciplina;
            $dpt = $disciplinaProProfessor->turma;
            $disciplinasProProfessor .=
                "<tr id='row-dpp-$disciplinaProProfessor->id'><td> $professor_array[$dpp] </td>
            <td>$disciplina_array[$dpd]</td><td>$turma_array[$dpt]</td>
            <td><button class='btn btn-danger btn-sm' id='deletar-dpp' value='$disciplinaProProfessor->id'><span class='glyphicon glyphicon-trash'></span> Deletar</button></td></tr>";
        }

        $args = [
            'PROFESSORES' => $professores,
        ];

        new Templates('admin/professores/listar.html', $args);
    }

    public function verProfessor($idProfessor)
    {
        $professor = $this->professorStorage->verProfessor($idProfessor);

        $endereco = $this->enderecoStorage->verEndereco($professor->endereco);
        $avatar = $this->avatarStorage->verAvatar($professor->id);
        $diario_de_classe = $this->diarioStorage->verDiarioDeClassePorProfessor($professor->professor);
        $disciplina = $this->professorStorage->verProfessorPorMateria($professor->professor);
        $arquivos_do_diario = $this->arquivoStorage->verArquivoPorProfessor($professor->professor);

        $footprint = [
            'usuario' => $professor,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'diario_de_classe' => $diario_de_classe,
            'disciplina' => $disciplina,
            'arquivos' => $arquivos_do_diario
        ];

        $deletar = "<button class='btn btn-danger btn-sm' id='deletar' value='$professor->id'><span class='glyphicon glyphicon-trash'></span> Deletar professor</button>";

        $disciplinaPorProfessorQuery = $this->materiaStorage->verMateriasDoProfessorAdmin($professor->professor);

        $disciplinasProProfessor = '';

        foreach ($disciplinaPorProfessorQuery as $disciplinaProProfessor) {
            $disciplinasProProfessor .= "
                <tr id='row-dpp-$disciplinaProProfessor->id'>
                    <td>$disciplinaProProfessor->turma</td>
                    <td>$disciplinaProProfessor->nome</td>
                    <td style='width: 5%'><a class='btn btn-sm' href='#' id='deletar-dpp' value='$disciplinaProProfessor->id'><span class='glyphicon glyphicon-trash'></span> </a></td>
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
            'FOOTPRINT' => json_encode($footprint),
            'BOTAO_DELETAR' => $deletar,
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

    public function desativarProfessor($idProfessor)
    {
        try {
            $this->professorStorage->desativarProfessor($idProfessor);
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
