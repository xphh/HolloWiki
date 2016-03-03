<?php
require_once("source/BaseSource.php");

class LofsSource extends BaseSource
{
	
	private function safepath($path) {
		$path = iconv('UTF-8', $this->encoding, $path);
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
				$showname = iconv($this->encoding, 'UTF-8', $name);
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