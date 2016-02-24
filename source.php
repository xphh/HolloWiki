<?php
require_once("source/svn.php");

function source_init($name) {
	return new $name();
}

?>