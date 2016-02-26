<?php
require_once("source.php");

global $g_sources;
$g_sources = array();

$g_sources['main'] = source_init('SvnSource', 'root');
$g_sources['main']->setBasedir('https://github.com/xphh/HolloWiki/trunk');
$g_sources['main']->setAuth('', '');

?>