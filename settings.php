<?php
require_once("source.php");

$svnpath = "https://github.com/xphh/HolloWiki/trunk";
$svnusr = '';
$svnpwd = '';

$g_source = source_init("SvnSource");
$g_source->setBasedir($svnpath);
$g_source->setAuth($svnusr, $svnpwd);

?>