<?php
require_once("source/svn.php");
require_once("source/lofs.php");

function source_init($type, $name) {
	return new $type($name);
}

?>