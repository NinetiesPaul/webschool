<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\DB\Storage\ArquivoStorage;
use App\DB\Storage\AvatarStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\TurmaStorage;
use App\DB\Storage\UsuarioStorage;
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
        $professorStorage = new ProfessorStorage();
        $professorQuery = $professorStorage->verProfessores();

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
        $professorStorage = new ProfessorStorage();
        $professor = $professorStorage->verProfessor($idProfessor);

        $materiaStorage = new MateriaStorage();
        $disciplinaPorProfessorQuery = $materiaStorage->verMateriasDoProfessorAdmin($professor->professor);

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

        $turmaStorage = new TurmaStorage();
        $turmaQuery = $turmaStorage->verTurmas();

        $turmas = '';
        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
        }

        $materiaStorage = new MateriaStorage();
        $disciplinaQuery = $materiaStorage->verMaterias();

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

        $professorStorage = new ProfessorStorage();
        $professorStorage->adicionarProfessor($email, $nome, $password, $salt);
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

        $usuarioStorage = new UsuarioStorage();
        $usuarioStorage->alterarUsuario($userId, $nome, $email, $password, $salt, Enum::TIPO_PROFESSOR);
        header('Location: /admin/professores');
    }

    public function adicionarProfessorPorMateria()
    {
        $data = json_decode(json_encode($_POST), true);

        $disciplina = $data['disciplina'];
        $turma = $data['turma'];
        $professor = $data['professor'];

        $professorStorage = new ProfessorStorage();
        $disponivel = $professorStorage->verificarMateriaPorProfessor($disciplina, $turma, $professor);

        if (count($disponivel) === 0) {
            $professorStorage->adicionarMateriaPorProfessor($disciplina, $turma, $professor);
        }

        header("Location: /admin/professor/$data[id]");
    }

    public function removerProfessor($idProfessor)
    {
        $professorStorage = new ProfessorStorage();
        $professor = $professorStorage->verProfessor($idProfessor);

        $enderecoStorage = new EnderecoStorage();
        $endereco = $enderecoStorage->verEndereco($professor->endereco);

        $avatarStorage = new AvatarStorage();
        $avatar = $avatarStorage->verAvatar($professor->id);

        $diarioStorage = new DiarioDeClasseStorage();
        $diario_de_classe = $diarioStorage->verDiarioDeClassePorProfessor($professor->professor);

        $enderecoStorage = new EnderecoStorage();
        $disciplina = $professorStorage->verProfessorPorMateria($professor->professor);

        $arquivoStorage = new ArquivoStorage();
        $arquivos_do_diario = $arquivoStorage->verArquivoPorProfessor($professor->professor);

        $footprint= [
            'usuario' => $professor,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'diario_de_classe' => $diario_de_classe,
            'disciplina' => $disciplina,
            'arquivos' => $arquivos_do_diario
        ];

        try {
            $professorStorage = new ProfessorStorage();
            $professorStorage->removerProfessor($professor->professor, $professor->id, $professor->endereco, $footprint);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }

    public function desativarProfessor()
    {
        $data = input()->all();

        try {
            $professorStorage = new ProfessorStorage();
            $professorStorage->desativarProfessor($data['id']);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }

    public function removerProfessorPorMateria($id)
    {
        try {
            $professorStorage = new ProfessorStorage();
            $professorStorage->removerProfessorPorMateria($id);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }
}
