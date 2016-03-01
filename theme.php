<?php
session_start();

$theme = $_GET["theme"];
if ($theme == null) {
	$theme = $_SESSION["theme"];
	if ($theme == null || $theme == "" || $theme == "default" ) {
		$mdcss = "markdown.css";
	} else {
		$mdcss = "markdown/$theme.css";
	}
	
	$path = $_GET["p"];
	if (substr($path, -1) == '/' || substr($path, -3) == '.md') {
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$_SESSION["lasturl"] = $url;
	}
} else {
	$_SESSION["theme"] = $theme;
	
	$lasturl = $_SESSION["lasturl"];
	header("HTTP/1.1 302 Found");
	header("Location: $lasturl");
	
	exit();
}

?>