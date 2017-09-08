<?php initDebug();

// Import
require 'php/D2OReader.class.php';
require 'php/D2OWriter.class.php';

// Convert
$json = '';
d2o_to_json($json);
json_to_d2o($json);





function initDebug()
{
	//Init Debug stuff
	ini_set('xdebug.var_display_max_depth', 16);
	ini_set('xdebug.var_display_max_children', 256);
	ini_set('xdebug.var_display_max_data', 1024);
}
function d2o_to_json(&$json)
{
	$D2o = new D2OReader('d2o/Jobs.d2o');
	$json = $D2o->json();
	var_dump($json);
}
function json_to_d2o(&$json)
{
	$D2o = new D2OWriter($json);
	$bin = $D2o->d2o();
	var_dump($bin);
}