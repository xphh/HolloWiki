<?php

class Footprint
{
	
	public static function record($sid, $path, $rev) {
		session_start();

		if ($_SESSION['footprint'] == null) {
			$_SESSION['footprint'] = array();
		}
		$footprint = $_SESSION['footprint'];

		$node = array();
		$node['sid'] = $sid;
		$node['path'] = $path;
		$node['rev'] = $rev;
		
		$i = array_search($node, $footprint);
		if (is_numeric($i)) {
			unset($footprint[$i]);
		}
		
		if (count($footprint) >= 5) {
			array_pop($footprint);
		}
		array_unshift($footprint, $node);

		$_SESSION['footprint'] = $footprint;
	}
	
	public static function get() {
		return $_SESSION['footprint'];
	}
	
}

?>