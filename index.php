<?php

//Init Debug stuff
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);



require 'php/D2OReader.class.php';


//Convert d2o to json
$d2o = new D2OReader('d2o/Npcs.d2o');
$json = $d2o->json();
var_dump($json);


