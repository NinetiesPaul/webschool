<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\Admin\ProfessorController as AdminProfessorController;

SimpleRouter::get('/admin/professores', function() {
    $admin = new AdminProfessorController();
    $admin->verProfessores();
});

SimpleRouter::get('/admin/professor/{idProfessor}', function($idProfessor) {
    $admin = new AdminProfessorController();
    $admin->verProfessor($idProfessor);
});

SimpleRouter::put('/admin/professor/{idProfessor}/desativar', function($idProfessor) {
    $adminAjax = new AdminProfessorController();
    $adminAjax->desativarProfessor($idProfessor);
});

SimpleRouter::delete('/admin/professor/{idProfessor}/delete', function($idProfessor) {
    $adminAjax = new AdminProfessorController();
    $adminAjax->removerProfessor($idProfessor);
});

SimpleRouter::put('/admin/professor', function() {
    $admin = new AdminProfessorController();
    $admin->atualizarProfessor();
});

SimpleRouter::post('/admin/professor', function() {
    $admin = new AdminProfessorController();
    $admin->adicionarProfessor();
});

SimpleRouter::post('/admin/professorPorMateria', function() {
    $admin = new AdminProfessorController();
    $admin->adicionarProfessorPorMateria();
});

SimpleRouter::delete('/admin/professorPorMateria/{id}', function($id) {
    $adminAjax = new AdminProfessorController();
    $adminAjax->removerProfessorPorMateria($id);
});