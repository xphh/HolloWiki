<?php
require_once("handler.php");
require_once("utils.php");
require_once("parsedown/Parsedown.php");

include("settings.php");
include("theme.php");

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
$handler->footprint($sid, $path, $rev);

if (substr($path, -1) == "/") {
	$index = $handler->makeIndex($sid, $path, "Home.md");
	$content = $handler->handleFile($sid, $path."Home.md");
} else {
	$index = $handler->makeIndex($sid, get_dirpath($path), basename($path));
	$content = $handler->handleFile($sid, $path, $rev);
}

$Parsedown = new Parsedown();
$index = $Parsedown->text($index);
$content = $Parsedown->text($content);

$template = file_get_contents("template.html");
$template = str_replace("{%mdcss%}", $mdcss, $template);
$template = str_replace("{%index%}", $index, $template);
$template = str_replace("{%content%}", $content, $template);

$themes = file_get_contents("themes.md");
$themes = $Parsedown->text($themes);
$template = str_replace("{%themes%}", $themes, $template);

echo $template;

?>
