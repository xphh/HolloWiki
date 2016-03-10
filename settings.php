<?php
require_once("source.php");

$source = SourceFactory::create('LofsSource', 'lo', 'local');
$source->setEncoding('GBK');
$source->setBasedir('.');
$source = SourceFactory::proxy('DirBrowserProxy', $source);

$source = SourceFactory::create('SvnSource', 'svn', 'github');
$source->setEncoding('GBK');
$source->setBasedir('https://github.com/xphh/HolloWiki/trunk');
$source->setAuth('', '');
$source = SourceFactory::proxy('DirBrowserProxy', $source);
$source = SourceFactory::proxy('CachedProxy', $source);
$source->setExpired(60);

?>