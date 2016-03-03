<?php

function source_init($type, $name) {
	require_once("source/$type.php");
	return new $type($name);
}

?>