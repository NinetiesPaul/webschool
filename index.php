<?php

use Pecee\SimpleRouter\SimpleRouter;

include 'vendor/autoload.php';

require_once 'helpers.php';
require_once 'routes.php';
require_once 'includes/php/lib/tfpdf/tfpdf.php';

SimpleRouter::setDefaultNamespace('Controllers');

SimpleRouter::start();