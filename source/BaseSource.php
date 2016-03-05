<?php

class BaseSource 
{
	protected $id;
	protected $name;
	protected $basedir;
	protected $encoding;
	protected $username;
	protected $password;

	public function BaseSource($id, $name) {
		$this->id = $id;
		$this->name = $name;
	}
	
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}
	
	public function setBasedir($basedir) {
		$this->basedir = $basedir;
	}
	
	public function setAuth($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}
	
	public function getDirectory($path) {
		return "";
	}
	
	public function getFile($path, $rev = null) {
		return "";
	}
	
	public function getFileType($path) {
		$ext = get_extension($path);
		if ($ext == 'md') {
			return 'md';
		}
		return 'file';
	}
	
	public function getHistory($path) {
		return array();
	}
	
}

?>