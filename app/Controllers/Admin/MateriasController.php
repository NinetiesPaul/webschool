<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\ResponseHandler;
use App\Templates;

class MateriasController extends AdminController
{
    public function criarDisciplina()
    {
        new Templates('admin/disciplinas/criar.html');
    }

    public function verMaterias()
    {
        $disciplinaQuery = $this->materiaStorage->verMaterias();

        $disciplinas = '';

        foreach ($disciplinaQuery as $disciplina) {
            $disciplinas .= "
                <tr id='row-$disciplina->id'>
                    <td>$disciplina->id</td>
                    <td>$disciplina->nome</td>
                    <td>
                        <a href='disciplina/$disciplina->id' class='btn btn-sm'><span class='glyphicon glyphicon-edit'></span> </a>
                        <a href='#' class='btn btn-sm' id='deletar' value='$disciplina->id'> <span class='glyphicon glyphicon-trash'></span> </a>
                    </td>
                </tr>
            ";
        }

        $args = [
            'DISCIPLINAS' => $disciplinas,
        ];

        new Templates('admin/disciplinas/listar.html', $args);
    }

    public function verMateria($materia)
    {
        $disciplina = $this->materiaStorage->verMateria($materia);

        $args = [
            'ID' => $disciplina->id,
            'NOME' => $disciplina->nome,
        ];

        new Templates('admin/disciplinas/editar.html', $args, '../');
    }

    public function adicionarMateria()
    {
        $data = json_decode(json_encode($_POST), true);

        $nome = $data['nome'];

        $this->materiaStorage->adicionarMateria($nome);
        header('Location: /admin/disciplinas');
    }

    public function atualizarMateria()
    {
        $data = json_decode(json_encode($_POST), true);

        $nome = $data['nome'];
        $id = $data['id'];

        $this->materiaStorage->alterarMateria($nome, $id);
        header('Location: /admin/disciplinas');
    }

    public function removerMateria($disciplina)
    {
        try {
            $this->materiaStorage->removerMateria($disciplina);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex, "materia");
        }

        ResponseHandler::response();
    }
}
