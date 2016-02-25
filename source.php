<?php
require_once("source/svn.php");

function source_init($type, $name) {
	return new $type($name);
}

?>