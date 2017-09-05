<?php

class D2OReader
{
	private static $D2OFieldType = array
	(
		'-1' => 	array('Int', 		'readInt'),
		'-2' => 	array('Bool', 		'readBool'),
		'-3' => 	array('String', 	'readUtf'),
		'-4' => 	array('Double', 	''),
		'-5' => 	array('I18N', 		'readInt'),
		'-6' => 	array('UInt', 		'readShort'),
		'-99' => 	array('List', 		'readList')
	);
	private $offset = 0;
	private $data;
	
	public function D2OReader($file) { $this->data = file_get_contents($file); }
	private function seek($offset){ $this->offset=$offset; }
	private function readMagic() { return 	$this->read(3); }
	private function readBool() { return !!$this->read(1); }
	private function readInt() { $r=unpack('N', $this->read(4)); return $r[1]; }
	private function readShort() { $r=unpack('n', $this->read(2)); return $r[1]; }
	private function readUtf() { return $this->read( $this->readShort() ); }
	private function readVector(&$ret) {
		$ret[] = $v = array(
			'name'=> $this->readUtf(),
			'type'=> $this->readInt()
		);
		if ($v['type'] == -99) $this->readVector($ret);
		return $ret;
	}
	private function readList($field, $dim=0)
	{
		$ret = array();
		$count = $this->readInt();
		for ($i=0; $i < $count; ++$i)
		{
			$type = $field['vectorTypes'][$dim]['type'];
			if ($type > 0)
			{
				$c = @$this->classes[$this->readInt() -1];
				if($c==null)$c=null; //Important car ==check by val (!===type)
				if($c != null) $c = $this->readObject($c);
				$ret[] = $c;
			} else {
				$func =  self::$D2OFieldType[ ($type.'') ][1];
				$ret[] =  $this->$func($field, $dim+1);
			}
		}
		return $ret;
	}
	private function readObject(&$obj = null)
	{
		
		if($obj == null)$obj =  $this->classes[$this->readInt() - 1] ;// array();
		$ret = array();
		$fieldsCount = count($obj['fields']);
		
		for($i=0; $i<$fieldsCount; ++$i)
		{
			$field = $obj['fields'][$i];
			$fieldType = $field['type'];
			
			if($fieldType>0)
			{
				var_dump('YOOOOOOOOOOOOOOO!');
				$cId = $this->readInt();
				$ret[ $field['name'] ] = $this->readObject( $this->classes[$cId - 1], $this->classes);
				continue;
			}
			$func =  self::$D2OFieldType[ ($fieldType.'') ][1];
			//var_dump($func . ' -> '. $fieldType);
			
			
			$ret[ $field['name'] ] = $this->$func($field);
		}
		return $ret;
	}
	private function read($bytes)
	{
		$r=substr($this->data, $this->offset, $bytes);
		$this->offset += $bytes;
		return $r;
	}
	
	
	private $classes = array();
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
		$classesSize = $this->readInt();
		for($i=0; $i<$classesSize; ++$i)
		{
			$cId = $this->readInt();
			$c = array('memberName' => $this->readUtf(), 'packageName' => $this->readUtf(), 'fields'=>array());
			$fieldCount = $this->readInt();
			for($fieldId = 0; $fieldId<$fieldCount; ++$fieldId) {
				$a = array();
				$c['fields'][] = array (
					'name' => $this->readUtf(),
					'type' =>  $t = $this->readInt(),
					'vectorTypes' => ($t == -99 ? $this->readVector( $a ) : false)
				);
			}
			$this->classes[$cId-1] = $c;
		}
		
		
		//Class Objects
		$objects = array();
		foreach($indexTable as $k=>$o)
		{
			$oIndex = $indexTable[$k];
			$this->seek($oIndex);
			$objects[] = $this->readObject( $this->classes[$this->readInt() - 1]);
		}
		
		
		return array (
			'def' => $this->classes,
			'data' => $objects
		);
	}
}

