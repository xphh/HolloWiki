<?php
require_once("footprint.php");

session_start();

class Options
{
	private static $options = array();
	
	public static function get($name) {
		return self::$options[$name];
	}
	
	public static function handle($name) {
		if ($name == 'theme') {
			$theme = $_GET['theme'];
			if ($theme == null) {
				$theme = $_SESSION["theme"];
				if ($theme == null || $theme == '' || $theme == 'default' ) {
					self::$options['mdcss'] = "markdown.css";
				} else {
					self::$options['mdcss'] = "markdown/$theme.css";
				}
			} else {
				$_SESSION['theme'] = $theme;
				Footprint::redirectToLast();
			}
		} else if ($name == 'nocache') {
			$nocache = $_GET['nocache'];
			if ($nocache == 1) {
				$_SESSION['nocache'] = 1;
				Footprint::redirectToLast();
			} else {
				if ($_SESSION['nocache'] == 1) {
					self::$options['nocache'] = 1;
				}
				unset($_SESSION['nocache']);
			}
		}
	}
}

?>