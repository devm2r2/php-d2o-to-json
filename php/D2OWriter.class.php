<?php

class D2OWriter
{
	private static $D2OFieldType = array
	(
		-1 		=> 	array('Int', 		'readInt'),
		-2 		=> 	array('Bool', 		'readBool'),
		-3 		=> 	array('String', 	'readUtf'),
		-4 		=> 	array('Double', 	'readDouble'),
		-5 		=> 	array('I18N', 		'readInt'),
		-6 		=> 	array('UInt', 		'readShort'),
		-99 	=> 	array('List', 		'readList')
	);
	private $data;
	
	public function D2OWriter($file) { $this->data = file_get_contents($file); }
	
	public function d2o($file=null)
	{
		$buffer = null;
		
		//	TODO:
		// 		Write Magic
		// 		Write IndexTable
		// 		Write Classes Definition
		// 		Write Data
		
		return $buffer;
	}
}

