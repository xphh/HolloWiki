<?php
require_once("source.php");

global $g_sources;
$g_sources = array();

$g_sources['lo'] = source_init('LofsSource', 'local');
$g_sources['lo']->setBasedir('.');

$g_sources['svn'] = source_init('SvnSource', 'github');
$g_sources['svn']->setEncoding('GBK');
$g_sources['svn']->setBasedir('https://github.com/xphh/HolloWiki/trunk');
$g_sources['svn']->setAuth('', '');

?>