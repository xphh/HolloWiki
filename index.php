<?php
require_once("handler.php");
require_once("utils.php");
require_once("parsedown/Parsedown.php");

include("settings.php");
global $g_source;

$handler = new Handler($g_source);

$rev = $_GET["r"];
$path = $_GET["p"];
if ($path == null) {
	$path = "/";
}

$Parsedown = new Parsedown();
$template = file_get_contents("template.html");

if ($rev == "@") {
	$index = $handler->handleDirectory(get_dirpath($path));
	$content = $handler->handleHistory($path);
} else {
	$lastchr = substr($path, -1);
	if ($lastchr == "/") {
		$index = $handler->handleDirectory($path);
		$content = $handler->handleFile($path."Home.md", null);
	} else {
		$index = $handler->handleDirectory(get_dirpath($path));
		$content = $handler->handleFile($path, $rev);
	}
}

$index = $Parsedown->text($index);
$content = $Parsedown->text($content);

$template = str_replace("{%index%}", $index, $template);
$template = str_replace("{%content%}", $content, $template);

echo $template;

?>
