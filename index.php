<?php
require_once("handler.php");
require_once("utils.php");
require_once("parsedown/Parsedown.php");

$rev = $_GET["r"];
$sid = $_GET["s"];
if ($sid == null) {
	$sid = 'main';
}
$path = $_GET["p"];
if ($path == null) {
	$path = "/";
}

include("settings.php");
$handler = new Handler($g_sources, $sid);

$Parsedown = new Parsedown();
$template = file_get_contents("template.html");

if ($rev == "@") {
	$index = $handler->makeIndex(get_dirpath($path));
	$content = $handler->handleHistory($path);
} else {
	$lastchr = substr($path, -1);
	if ($lastchr == "/") {
		$index = $handler->makeIndex($path);
		$content = $handler->handleFile($path."Home.md", null);
	} else {
		$index = $handler->makeIndex(get_dirpath($path));
		$content = $handler->handleFile($path, $rev);
	}
}

$index = $Parsedown->text($index);
$content = $Parsedown->text($content);

$template = str_replace("{%index%}", $index, $template);
$template = str_replace("{%content%}", $content, $template);

echo $template;

?>
