<?php
require_once("footprint.php");

session_start();

$theme = $_GET["theme"];
if ($theme == null) {
	$theme = $_SESSION["theme"];
	if ($theme == null || $theme == "" || $theme == "default" ) {
		$mdcss = "markdown.css";
	} else {
		$mdcss = "markdown/$theme.css";
	}
} else {
	$_SESSION["theme"] = $theme;
	
	$node = Footprint::getFirst();
	$url = $node['url'];
	
	header("HTTP/1.1 302 Found");
	header("Location: $url");
	
	exit();
}

?>