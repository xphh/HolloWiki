<?php

function get_extension($filename) {
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	return $ext;
}

function get_dirpath($path) {
	$fpath = str_replace("\\", "/", dirname($path));
	if (substr($fpath, -1) != "/") {
		$fpath = $fpath."/";
	}
	return $fpath;
}

function wfDebug($x) {}
$wgMimeTypeFile = 'mimes/mime.types';
require_once("mimes/MimeMagic.php");

function get_mimetype($filename) {
	$m = new MimeMagic();
	$type = $m->getTypesForExtension(get_extension($filename));
	return $type;
}

?>