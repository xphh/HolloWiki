<?php
require_once("footprint.php");

function redirect2lastpage() {
	$node = Footprint::getFirst();
	$url = $node['url'];
	
	header("HTTP/1.1 302 Found");
	header("Location: $url");
	
	exit();
}

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
	redirect2lastpage();
}

$nocache = $_GET["nocache"];
if ($nocache == 1) {
	$_SESSION["nocache"] = 1;
	redirect2lastpage();
} else {
	if ($_SESSION["nocache"] == 1) {
		$nocache = 1;
	}
	unset($_SESSION["nocache"]);
}

?>