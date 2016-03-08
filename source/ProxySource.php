<?php

class ProxySource extends BaseSource
{
	protected $source;

	public function ProxySource($source) {
		parent::BaseSource($source->getId(), $source->getName());
		$this->source = $source;
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