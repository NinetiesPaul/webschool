<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AuthController;
use App\Controllers\GeneralController;
use App\Controllers\AjaxControllers\GeneralController as AjaxGeneral;

//general

SimpleRouter::get('/', function() {
    $general = new GeneralController;
    $general->index();
});

SimpleRouter::post('/gerarBoletim', function() {
    $general = new GeneralController;
    $general->gerarBoletim();
});

SimpleRouter::post('/gerarHistorico', function() {
    $general = new GeneralController;
    $general->gerarHistorico();
});

SimpleRouter::post('/pesquisarFaltas', function() {
    $generalAjax = new AjaxGeneral();
    $generalAjax->pesquisarFaltas();
});

SimpleRouter::get('/perfil', function() {
    $general = new GeneralController;
    $general->visualizarPerfil();
});

SimpleRouter::put('/atualizarPerfil', function() {
    $general = new GeneralController;
    $general->alterarPerfil();
});

//auth e logout

SimpleRouter::post('/login', function() {
    $auth = new AuthController;
    $auth->login();
});

SimpleRouter::get('/logout', function() {
    $auth = new AuthController;
    $auth->logout();
});

SimpleRouter::post('/verificarLogin', function() {
    $auth = new AuthController;
    $auth->loginTaken();
});