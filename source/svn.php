<?php

class SvnSource 
{
	private $name;
	private $basedir;
	private $username;
	private $password;

	private static function docmd($cmd, $mode) {
		$cmd = mb_convert_encoding($cmd, 'GBK', 'UTF-8');
		$fp = popen($cmd, $mode);
		while (!feof($fp)) { 
			$data .= fgets($fp);
		}
		pclose($fp);
		return $data;
	}
	
	public function SvnSource($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}

	public function setBasedir($basedir) {
		$this->basedir = $basedir;
	}
	
	public function setAuth($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}
	
	public function getDirectory($path) {
		$raw = SvnSource::docmd("svn list $this->basedir$path --username $this->username --password $this->password", "r");
		$raw = mb_convert_encoding($raw, 'UTF-8', 'GBK');
		$arr = explode("\n", $raw);
		return $arr;
	}
	
	public function getFile($path) {
		$raw = SvnSource::docmd("svn cat $this->basedir$path --username $this->username --password $this->password", "rb");
		return $raw;
	}
	
	public function getHistory($path) {
		$raw = SvnSource::docmd("svn log $this->basedir$path --username $this->username --password $this->password", "r");
		$raw = mb_convert_encoding($raw, 'UTF-8', 'GBK');
		$arr = explode("------------------------------------------------------------------------", $raw);
		
		$list = array();
		foreach ($arr as $content) {
			$ok = preg_match("/r(\\d+) \\| ([^|]+) \\| ([^|]+)/", $content, $m);
			if ($ok == 1) {
				$node = array();
				$node["rev"] = $m[1];
				$node["author"] = $m[2];
				$node["date"] = $m[3];
				$node["desc"] = substr($content, strpos($content, "\n\n") + 2);
				$list[count($list)] = $node;
			}
		}

		return $list;
	}
	
}

?>