<?php

class LofsSource 
{
	private $name;
	private $encoding;
	private $basedir;

	public function LofsSource($name) {
		$this->name = $name;
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
	
	private function safepath($path) {
		$path = mb_convert_encoding($path, $this->encoding, 'UTF-8');
		$path = preg_replace('/^\\.\\.\\//', '', $path);
		$path = preg_replace('/\\/\\.\\.\\//', '', $path);
		return $path;
	}
	
	public function getDirectory($path) {
		$dir = $this->safepath($this->basedir.$path);
		$files = scandir($dir);
		$arr = array();
		foreach ($files as $name) {
			if ($name == '.' || $name == '..') {
				
			} else {
				$showname = mb_convert_encoding($name, 'UTF-8', $this->encoding);
				if (filetype($dir.$name) == 'dir') {
					$arr[count($arr)] = $showname.'/';
				} else {
					$arr[count($arr)] = $showname;
				}
			}
		}
		return $arr;
	}
	
	public function getFile($path) {
		$filename = $this->safepath($this->basedir.$path);
		if (file_exists($filename)) {
			return file_get_contents($filename);
		} else {
			return '';
		}
	}
	
	public function getHistory($path) {
		$filename = $this->safepath($this->basedir.$path);
		$list = array();
		if (file_exists($filename)) {
			$owner = fileowner($filename);
			$ctime = filectime($filename);
			$mtime = filemtime($filename);
			if ($ctime != $mtime) {
				$node = array();
				$node["rev"] = '';
				$node["author"] = $owner;
				$node["date"] = date('r', $mtime);
				$node["desc"] = 'Last modify';
				$list[count($list)] = $node;
			}
			$node = array();
			$node["rev"] = '';
			$node["author"] = $owner;
			$node["date"] = date('r', $ctime);
			$node["desc"] = 'Created';
			$list[count($list)] = $node;
		}
		return $list;
	}
	
}

?>