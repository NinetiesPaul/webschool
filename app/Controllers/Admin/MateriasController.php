<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\DB\Storage\MateriaStorage;
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
        $materiaStorage = new MateriaStorage();
        $disciplinaQuery = $materiaStorage->verMaterias();

        $disciplinas = '';

        foreach ($disciplinaQuery as $disciplina) {
            $disciplinas .= "
                <tr data-id='$disciplina->id'>
                    <td>$disciplina->id</td>
                    <td>$disciplina->nome</td>
                    <td>
                        <a href='disciplina/$disciplina->id' class='btn btn-sm'><span class='glyphicon glyphicon-edit'></span> </a>
                        <a href='#' class='btn btn-sm' id='deletar'> <span class='glyphicon glyphicon-trash'></span> </a>
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
        $materiaStorage = new MateriaStorage();
        $disciplina = $materiaStorage->verMateria($materia);

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

        $materiaStorage = new MateriaStorage();
        $materiaStorage->adicionarMateria($nome);
        header('Location: /admin/disciplinas');
    }

    public function atualizarMateria()
    {
        $data = json_decode(json_encode($_POST), true);

        $nome = $data['nome'];
        $id = $data['id'];

        $materiaStorage = new MateriaStorage();
        $materiaStorage->alterarMateria($nome, $id);
        header('Location: /admin/disciplinas');
    }

    public function removerMateria($disciplina)
    {
        try {
            $materiaStorage = new MateriaStorage();
            $materiaStorage->removerMateria($disciplina);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex, "materia");
        }

        ResponseHandler::response();
    }
}
