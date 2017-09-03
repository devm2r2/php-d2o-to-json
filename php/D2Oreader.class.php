<?php

class D2OReader
{
	private static $D2OFieldType = array
	(
		'-1' => 	array('Int', 		'readInt'),
		'-2' => 	array('Bool', 		'readBool'),
		'-3' => 	array('String', 	'readUtf'),
		'-4' => 	array('Double', 	''),
		'-5' => 	array('I18N', 		''),
		'-6' => 	array('UInt', 		''),
		'-99' => 	array('List', 		'')
	);
	private $offset = 0;
	private $data;
	
	public function D2OReader($file) { $this->data = file_get_contents($file); }
	private function seek($offset){ $this->offset=$offset; }
	private function readMagic() { return 	$this->read(3); }
	private function readInt() { $r=unpack('N', $this->read(4)); return $r[1]; }
	private function readShort() { $r=unpack('n', $this->read(2)); return $r[1]; }
	private function readUtf() { return $this->read( $this->readShort() ); }
	private function readVector(&$ret) {
		$ret[] = $v = array(
			'name'=> $this->readUtf(),
			'type'=> ($ref = self::$D2OFieldType[$t = $this->readInt()][0]) != null ? $ref : $t
		);
		if ($v['type'] === 'List') addVector($ret);
		return $ret;
	}
	private function readObject(&$obj, &$classes)
	{
		$ret = array();
		$fieldsCount = count($obj['fields']);
		for($i=0; $i==$fieldsCount; ++$i)
		{
			$cId = $this->readInt();
			$field = $obj['fields'][$i];
			
			$ret[ $field['name'] ] = $field['type'] > 0
				? (($that = @$classes[$cId - 1])!=null ? $this->readObject($that) : null)
				: self::$D2OFieldType[$field['type']][1]();
		}
		return $ret;
	}
	private function read($bytes)
	{
		$r=substr($this->data, $this->offset, $bytes);
		$this->offset += $bytes;
		return $r;
	}
	
	
	public function json()
	{
		// Magic
		var_dump( $this->readMagic() );
		$index = $this->readInt();
		$this->seek($index);
		
		
		//IndexTable
		$indexTable = array();
		$indexTableSize = $this->readInt();
		for ($i=0; $i < $indexTableSize / 8; ++$i)
			$indexTable[$this->readInt()] = $this->readInt();
		
		
		//Classes Definition
		$classes = array();
		$classesSize = $this->readInt();
		for($i=0; $i<$classesSize; ++$i)
		{
			$cId = $this->readInt();
			$c = array('memberName' => $this->readUtf(), 'packageName' => $this->readUtf(), 'fields'=>array());
			$fieldCount = $this->readInt();
			for($fieldId = 0; $fieldId<$fieldCount; ++$fieldId) {
				$c['fields'][] = array (
					'name' => $this->readUtf(),
					'type' =>  $t = ($ref = self::$D2OFieldType[$t = $this->readInt()][0]) != null ? $ref : $t,
					'vectorTypes' => $t === 'List' && $this->readVector( array() )
				);
			}
			$classes[$cId-1] = $c;
		}
		
		
		//Class Objects
		$objects = array();
		foreach($classes as $k=>$o)
		{
			$oIndex = $indexTable[$k];
			$this->seek($oIndex);
			$objects[] = $this->readObject( $classes[$this->readInt() - 1], $classes );
		}
		
		
		
		return array (
			'def' => $classes,
			'data' => $objects
		);
	}
}

