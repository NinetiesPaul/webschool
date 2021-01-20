<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AdminController;
use App\Controllers\AjaxControllers\AdminController as AjaxAdmin;

SimpleRouter::get('/admin/disciplinas', function() {
    $admin = new AdminController();
    $admin->verMaterias();
});

SimpleRouter::get('/admin/disciplina/{idDisciplina}', function($idDisciplina) {
    $admin = new AdminController();
    $admin->verMateria($idDisciplina);
});

SimpleRouter::delete('/admin/disciplina/{idDisciplina}/delete', function($idDisciplina) {
    $ajaxAdmin = new AjaxAdmin();
    $ajaxAdmin->removerMateria($idDisciplina);
});

SimpleRouter::put('/admin/disciplina', function() {
    $admin = new AdminController();
    $admin->atualizarMateria();
});

SimpleRouter::post('/admin/disciplina', function() {
    $admin = new AdminController();
    $admin->adicionarMateria();
});