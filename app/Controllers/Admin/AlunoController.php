<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;

class AlunoController extends AdminController
{
    public function verAlunos()
    {
        $turmaQuery = $this->turmaStorage->verTurmas();

        $turmas = '';
        foreach ($turmaQuery as $turma) {
            $turmas .= "<option value='$turma->id'>$turma->serie º Série $turma->nome</option>";
        }

        $alunoQuery = $this->alunoStorage->verAlunos();

        $alunos = '';
        foreach ($alunoQuery as $aluno) {
            $is_deleted = ($aluno->is_deleted) ? "<span class='label-status_$aluno->id label-success'>Ativo</span>" : "<span class='label-status_$aluno->id label-danger'>Inativo</span>";
            $alunos .=
                "<tr id='row-$aluno->id'><td>$aluno->nome </td>
            <td>$aluno->nome_turma</td>
            <td>$is_deleted</td>
            <td style='text-align: center;'><a href='aluno/$aluno->id' class='btn'><span class='glyphicon glyphicon-edit'></span></a>
            <a href='#' class='btn desativar' id='$aluno->id'><span class='glyphicon glyphicon-ban-circle'></span> </a></td></tr>";
        }

        $args = [
            'TURMAS' => $turmas,
            'ALUNOS' => $alunos,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('admin/alunos.html', $args);
    }

    public function verAluno($idAluno)
    {
        $this->links = $this->util->generateLinks('../');

        $aluno = $this->alunoStorage->verAluno($idAluno);
        $turmaQuery = $this->turmaStorage->verTurmas();

        $turmas = '';

        foreach ($turmaQuery as $turma) {
            $selected = ($aluno->turma == $turma->id)? 'selected' : '';
            $turmas .= "<option value='$turma->id' $selected>$turma->serie º Série $turma->nome</option>";
        }

        $endereco = $this->enderecoStorage->verEndereco($aluno->endereco);
        $avatar = $this->avatarStorage->verAvatar($aluno->id);
        $diario_de_classe = $this->diarioStorage->verDiarioDeClassePorAluno($aluno->aluno);
        $nota_por_aluno = $this->notaStorage->verNotasPorAluno($aluno->aluno);
        $arquivos_do_diario = $this->arquivoStorage->verArquivoPorAluno($aluno->aluno);
        $filho_de = $this->responsavelStorage->verResponsaveisPeloAluno($aluno->aluno);

        $footprint = [
            'usuario' => $aluno,
            'endereco' => $endereco,
            'avatar' => $avatar,
            'diario_de_classe' => $diario_de_classe,
            'nota_por_aluno' => $nota_por_aluno,
            'arquivos' => $arquivos_do_diario,
            'filho_de' => $filho_de
        ];

        $deletar = "<button class='btn btn-danger btn-sm' id='deletar' value='$aluno->id'><span class='glyphicon glyphicon-trash'></span> Deletar aluno</button>";

        $args = [
            'ID' => $aluno->id,
            'IDALUNO' => $aluno->aluno,
            'NOME' => $aluno->nome,
            'SALT' => $aluno->salt,
            'EMAIL' => $aluno->email,
            'TURMAS' => $turmas,
            'FOOTPRINT' => json_encode($footprint),
            'BOTAO_DELETAR' => $deletar,
            'LINKS' => $this->links
        ];

        $this->util->loadTemplate('admin/aluno.html', $args);
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

        $this->alunoStorage->adicionarAluno($email, $nome, $password, $salt, $turma);
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

        $this->alunoStorage->alterarAluno($userId, $idAluno, $nome, $email, $password, $salt, $turma);
        header('Location: /admin/alunos');
    }

    public function removerAluno($idAluno)
    {
        $aluno = $this->alunoStorage->verAluno($idAluno);

        $endereco = $this->enderecoStorage->verEndereco($aluno->endereco);
        $avatar = $this->avatarStorage->verAvatar($aluno->id);
        $diario_de_classe = $this->diarioStorage->verDiarioDeClassePorAluno($aluno->aluno);
        $nota_por_aluno = $this->notaStorage->verNotasPorAluno($aluno->aluno);
        $arquivos_do_diario = $this->arquivoStorage->verArquivoPorAluno($aluno->aluno);
        $filho_de = $this->responsavelStorage->verResponsaveisPeloAluno($aluno->aluno);

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
}
