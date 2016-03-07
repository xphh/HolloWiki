<?php

class SourceFactory
{
	private static $sources = array();

	public static function create($type, $id, $name) {
		require_once("source/$type.php");
		$source = new $type($id, $name);
		self::$sources[$id] = $source;
		return $source;
	}
	
	public static function createProxy($proxy, $type, $id, $name) {
		require_once("source/$type.php");
		require_once("source/$proxy.php");
		$source = new $type($id, $name);
		$sourceProxy = new $proxy($source, $id, $name);
		self::$sources[$id] = $sourceProxy;
		return $source;
	}
	
	public static function all() {
		return self::$sources;
	}
	
	public static function get($id) {
		if ($id == null || !array_key_exists($id, self::$sources)) {
			return reset(self::$sources);
		}
		return self::$sources[$id];
	}

}

?>