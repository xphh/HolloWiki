<?php
require_once("source.php");

class Footprint
{
	
	public static function record($sid, $path, $rev) {
		$source = SourceFactory::get($sid);
		if (substr($path, -1) == '/' || $source->getFileType($path) == 'md') {
			session_start();

			if ($_SESSION['footprint'] == null) {
				$_SESSION['footprint'] = array();
			}
			$footprint = $_SESSION['footprint'];

			$node = array();
			$node['sid'] = $sid;
			$node['path'] = $path;
			$node['rev'] = $rev;
			$node['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
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
	}
	
	public static function get() {
		return $_SESSION['footprint'];
	}
	
	public static function getFirst() {
		return reset($_SESSION['footprint']);
	}
	
	public static function makeList() {
		$footprint = $_SESSION['footprint'];
		foreach ($footprint as $i => $fp) {
			$sid = $fp['sid'];
			$path = $fp['path'];
			$rev = $fp['rev'];
			$name = SourceFactory::get($sid)->getName().$path;
			if (is_numeric($rev)) {
				$name = $name."@$rev";
			} else {
				$name = $name."$rev";
			}
			$link = hwLink($sid, $path, $rev);
			$mdtext = $mdtext." $i. [$name]($link)\n";
		}
		
		return $mdtext;
	}
	
	public static function redirectToLast() {
		$node = Footprint::getFirst();
		$url = $node['url'];
		
		header("HTTP/1.1 302 Found");
		header("Location: $url");
		
		exit();
	}

}

?>