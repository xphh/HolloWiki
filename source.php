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
	
	public static function proxy($type, $source) {
		require_once("source/$type.php");
		$proxy = new $type($source);
		self::$sources[$source->getId()] = $proxy;
		return $proxy;
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