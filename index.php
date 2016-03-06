<?php
require_once("handler.php");
require_once("utils.php");
require_once("footprint.php");
require_once("parsedown/Parsedown.php");

include("settings.php");
include("theme.php");

$rev = $_GET["r"];
$sid = $_GET["s"];
$path = $_GET["p"];
if ($path == null) {
	$path = "/";
}

Footprint::record($sid, $path, $rev);

if (substr($path, -1) == "/") {
	$path = $path.'Home.md';
}

$handler = new Handler($sid, $path, $rev);
$index = $handler->makeIndex();
$content = $handler->makeContent();

$themes = file_get_contents("themes.md");

$Parsedown = new Parsedown();
$index = $Parsedown->text($index);
$content = $Parsedown->text($content);
$themes = $Parsedown->text($themes);

$template = file_get_contents("template.html");
$template = str_replace("{%mdcss%}", $mdcss, $template);
$template = str_replace("{%index%}", $index, $template);
$template = str_replace("{%content%}", $content, $template);
$template = str_replace("{%themes%}", $themes, $template);

echo $template;

?>
