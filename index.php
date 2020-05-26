<?php

error_reporting(E_ALL ^ E_NOTICE);

use Pecee\SimpleRouter\SimpleRouter;

include 'vendor/autoload.php';

require_once 'helpers.php';
require_once 'routes.php';
require_once 'includes/php/lib/tfpdf/tfpdf.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

SimpleRouter::setDefaultNamespace('Controllers');
SimpleRouter::start();