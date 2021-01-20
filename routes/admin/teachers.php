<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AdminController;
use App\Controllers\AjaxControllers\AdminController as AjaxAdmin;

SimpleRouter::get('/admin/professores', function() {
    $admin = new AdminController();
    $admin->verProfessores();
});

SimpleRouter::get('/admin/professor/{idProfessor}', function($idProfessor) {
    $admin = new AdminController();
    $admin->verProfessor($idProfessor);
});

SimpleRouter::put('/admin/professor/{idProfessor}/desativar', function($idProfessor) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->desativarProfessor($idProfessor);
});

SimpleRouter::delete('/admin/professor/{idProfessor}/delete', function($idProfessor) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->removerProfessor($idProfessor);
});

SimpleRouter::put('/admin/professor', function() {
    $admin = new AdminController();
    $admin->atualizarProfessor();
});

SimpleRouter::post('/admin/professor', function() {
    $admin = new AdminController();
    $admin->adicionarProfessor();
});

SimpleRouter::post('/admin/professorPorMateria', function() {
    $admin = new AdminController();
    $admin->adicionarProfessorPorMateria();
});

SimpleRouter::delete('/admin/professorPorMateria/{id}', function($id) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->removerProfessorPorMateria($id);
});