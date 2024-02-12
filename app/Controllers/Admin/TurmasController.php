<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\DB\Storage\TurmaStorage;
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
        $turmaStorage = new TurmaStorage();
        $turmasQuery = $turmaStorage->verTurmas();

        $turmas = '';

        foreach ($turmasQuery as $turma) {
            $turmas .="
                <tr data-id='$turma->id'>
                    <td>$turma->id</td>
                    <td>$turma->nome ($turma->ano)</td>
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
        $turmaStorage = new TurmaStorage();
        $turma = $turmaStorage->verTurma($turma);

        $args = [
            'ID' => $turma->id,
            'ANO' => $turma->ano,
            'NOME' => $turma->nome,
        ];

        new Templates('admin/turmas/editar.html', $args);
    }

    public function adicionarTurma()
    {
        $data = json_decode(json_encode($_POST), true);

        $nome = $data['nome'];
        $ano = $data['ano'];

        $turmaStorage = new TurmaStorage();
        $turmaStorage->adicionarTurma($nome, $ano);
        header('Location: /admin/turmas');
    }

    public function atualizarTurma()
    {
        $data = json_decode(json_encode($_POST), true);

        $nome = $data['nome'];
        $ano = $data['ano'];
        $turma = $data['id'];

        $turmaStorage = new TurmaStorage();
        $turmaStorage->alterarTurma($nome, $ano, $turma);
        header('Location: /admin/turmas');
    }

    public function removerTurma($turma)
    {
        try {
            $turmaStorage = new TurmaStorage();
            $turmaStorage->removerTurma($turma);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex, "turma");
        }

        ResponseHandler::response();
    }
}
