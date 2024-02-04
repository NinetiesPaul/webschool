<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\ResponseHandler;
use App\Templates;

class TurmasController extends AdminController
{
    public function verTurmas()
    {
        $turmasQuery = $this->turmaStorage->verTurmas();

        $turmas = '';

        foreach ($turmasQuery as $turma) {
            $turmas .=
                "<tr id='row-$turma->id'><td>$turma->serie º Série $turma->nome</td>
             <td><a href='turma/$turma->id' class='btn btn-info btn-sm btn-sm'><span class='glyphicon glyphicon-edit'></span> Editar</a></td>
             <td><button class='btn btn-danger btn-sm' id='deletar' value='$turma->id'><span class='glyphicon glyphicon-trash'></span> Deletar</button></td></tr>";
        }

        $args = [
            'ALUNOS' => $turmas,
        ];

        new Templates('admin/turmas.html', $args);
    }

    public function verTurma($turma)
    {
        $turma = $this->turmaStorage->verTurma($turma);

        $args = [
            'ID' => $turma->id,
            'TURMA' => $turma->serie,
            'LETRA' => $turma->nome,
        ];

        new Templates('admin/turma.html', $args, '../');
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
