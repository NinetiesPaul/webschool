<?php

error_reporting(E_ALL ^ E_NOTICE);

use Pecee\SimpleRouter\SimpleRouter;

include 'vendor/autoload.php';

require_once 'helpers.php';
require_once 'routes/routes.php';
require_once 'routes/teachers.php';
require_once 'routes/students.php';
require_once 'routes/relatives.php';
require_once 'routes/admin/admin.php';
require_once 'routes/admin/classes.php';
require_once 'routes/admin/disciplines.php';
require_once 'routes/admin/relatives.php';
require_once 'routes/admin/students.php';
require_once 'routes/admin/teachers.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

SimpleRouter::setDefaultNamespace('Controllers');
SimpleRouter::start();