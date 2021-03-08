<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Parser;

$filesDir = __DIR__ . "/files";

function pred($data)
{
    var_dump($data);
    die('На этом всё');
}

try {
    $parser = new Parser($filesDir);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    return false;
}

$parser->start();