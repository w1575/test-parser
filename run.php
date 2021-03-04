<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Parser;

$filesDir = __DIR__ . "/files";

$parser = new Parser($filesDir);
$parser->start();