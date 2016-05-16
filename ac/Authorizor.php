<?php
require_once('ac.php');

class Authorizor
{
	public function doAuth($username, $password) {
		return true;
	}
	public function getWlist() {
		return array();
	}
}

?>
