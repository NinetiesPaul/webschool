<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\Admin\MateriasController as AdminMateriasController;

SimpleRouter::get('/admin/disciplina', function() {
    $admin = new AdminMateriasController();
    $admin->criarDisciplina();
});

SimpleRouter::get('/admin/disciplinas', function() {
    $admin = new AdminMateriasController();
    $admin->verMaterias();
})->name('admin_disciplinas');

SimpleRouter::get('/admin/disciplina/{idDisciplina}', function($idDisciplina) {
    $admin = new AdminMateriasController();
    $admin->verMateria($idDisciplina);
});

SimpleRouter::delete('/admin/disciplina/{idDisciplina}/delete', function($idDisciplina) {
    $ajaxAdmin = new AdminMateriasController();
    $ajaxAdmin->removerMateria($idDisciplina);
});

SimpleRouter::put('/admin/disciplina', function() {
    $admin = new AdminMateriasController();
    $admin->atualizarMateria();
});

SimpleRouter::post('/admin/disciplina', function() {
    $admin = new AdminMateriasController();
    $admin->adicionarMateria();
});