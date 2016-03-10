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

function hwLink($sid, $path, $rev = null) {
	$link = "?p=".rawurlencode($path)."&s=".rawurlencode($sid);
	if ($rev != null) {
		$link = $link."&r=$rev";
	}
	return $link;
}

function wfDebug($x) {}
$wgMimeTypeFile = 'mimes/mime.types';
require_once("mimes/MimeMagic.php");

function get_mimetype($filename) {
	$m = new MimeMagic();
	$type = $m->getTypesForExtension(get_extension($filename));
	return $type;
}

function normalizePath($path)
{
    $parts = array();// Array to build a new path from the good parts
    $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
    $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
    $segments = explode('/', $path);// Collect path segments
    $test = '';// Initialize testing variable
    foreach($segments as $segment)
    {
        if($segment != '.')
        {
            $test = array_pop($parts);
            if(is_null($test))
                $parts[] = $segment;
            else if($segment == '..')
            {
                if($test == '..')
                    $parts[] = $test;

                if($test == '..' || $test == '')
                    $parts[] = $segment;
            }
            else
            {
                $parts[] = $test;
                $parts[] = $segment;
            }
        }
    }
    return implode('/', $parts);
}

?>