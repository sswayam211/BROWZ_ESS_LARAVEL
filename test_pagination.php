<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$pag = \App\Models\Login::paginate(10);
var_dump(get_class($pag));
var_dump($pag->count());

