<?php

class ProxySource extends BaseSource
{
	protected $source;

	public function ProxySource($source, $id, $name) {
		parent::BaseSource($id, $name);
		$this->source = $source;
	}
	
	public function setEncoding($encoding) {
		$this->source->setEncoding($encoding);
	}
	
	public function setBasedir($basedir) {
		$this->source->setBasedir($basedir);
	}
	
	public function setAuth($username, $password) {
		$this->source->setAuth($username, $password);
	}
	
	public function getDirectory($path) {
		return $this->source->getDirectory($path);
	}
	
	public function getFile($path, $rev = null) {
		return $this->source->getFile($path, $rev);
	}
	
	public function getFileType($path) {
		return $this->source->getFileType($path);
	}
	
	public function getHistory($path) {
		return $this->source->getHistory($path);
	}
	
}

?>