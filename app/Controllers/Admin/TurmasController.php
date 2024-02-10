<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\ResponseHandler;
use App\Templates;

class TurmasController extends AdminController
{
    public function criarTurma()
    {
        new Templates('admin/turmas/criar.html');
    }

    public function verTurmas()
    {
        $turmasQuery = $this->turmaStorage->verTurmas();

        $turmas = '';

        foreach ($turmasQuery as $turma) {
            $turmas .="
                <tr data-id='$turma->id'>
                    <td>$turma->id</td>
                    <td>$turma->serie º Série $turma->nome</td>
                    <td>
                        <a href='turma/$turma->id' class='btn btn-sm'><span class='glyphicon glyphicon-edit'></span> </a>
                        <a href='#' class='btn btn-sm' id='deletar'><span class='glyphicon glyphicon-trash'></span> </a>
                    </td>
                </tr>
            ";
        }

        $args = [
            'TURMAS' => $turmas,
        ];

        new Templates('admin/turmas/listar.html', $args);
    }

    public function verTurma($turma)
    {
        $turma = $this->turmaStorage->verTurma($turma);

        $args = [
            'ID' => $turma->id,
            'TURMA' => $turma->serie,
            'LETRA' => $turma->nome,
        ];

        new Templates('admin/turmas/editar.html', $args);
    }

    public function adicionarTurma()
    {
        $data = json_decode(json_encode($_POST), true);

        $nome = $data['nome'];
        $serie = $data['serie'];

        $this->turmaStorage->adicionarTurma($nome, $serie);
        header('Location: /admin/turmas');
    }

    public function atualizarTurma()
    {
        $data = json_decode(json_encode($_POST), true);

        $nome = $data['nome'];
        $serie = $data['serie'];
        $turma = $data['id'];


        $this->turmaStorage->alterarTurma($nome, $serie, $turma);
        header('Location: /admin/turmas');
    }

    public function removerTurma($turma)
    {
        try {
            $this->turmaStorage->removerTurma($turma);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex, "turma");
        }

        ResponseHandler::response();
    }
}
