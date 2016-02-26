<?php
require_once("handler.php");
require_once("utils.php");
require_once("parsedown/Parsedown.php");
include("settings.php");

$rev = $_GET["r"];
$sid = $_GET["s"];
if ($sid == null) {
	$sid = key($g_sources);
}
$path = $_GET["p"];
if ($path == null) {
	$path = "/";
}

$handler = new Handler($g_sources);

if (substr($path, -1) == "/") {
	$index = $handler->makeIndex($sid, $path);
	$content = $handler->handleFile($sid, $path."Home.md", null);
} else {
	$index = $handler->makeIndex($sid, get_dirpath($path));
	$content = $handler->handleFile($sid, $path, $rev);
}

$Parsedown = new Parsedown();
$index = $Parsedown->text($index);
$content = $Parsedown->text($content);

$template = file_get_contents("template.html");
$template = str_replace("{%index%}", $index, $template);
$template = str_replace("{%content%}", $content, $template);

echo $template;

?>
