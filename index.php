<?php
require_once("handler.php");
require_once("utils.php");
require_once("footprint.php");
require_once("pagemaker.php");
require_once("options.php");

include("settings.php");

Options::handle('theme');
Options::handle('nocache');

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

$maker = new PageMaker();
$maker->put('mdcss', Options::get('mdcss'));
$maker->putMarkdown('index', $handler->makeIndex());
$maker->putMarkdown('pointer', $handler->makePointer());
$maker->putMarkdown('content', $handler->makeContent());
$maker->putMarkdown('footprint', Footprint::makeList());
$maker->putMarkdown('themes', file_get_contents('themes.md'));

$template = SourceFactory::get($sid)->getTemplate();
echo $maker->generate("template/$template.html");

?>
