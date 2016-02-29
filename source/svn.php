<?php

class SvnSource 
{
	private $name;
	private $basedir;
	private $encoding;
	private $username;
	private $password;

	private function command($op, $path, $istext) {
		$cmd = "svn $op $this->basedir$path --username $this->username --password $this->password";
		$cmd = iconv('UTF-8', $this->encoding, $cmd);
		$mode = $istext ? 'r' : 'rb';
		
		$fp = popen($cmd, $mode);
		while (!feof($fp)) { 
			$data .= fgets($fp);
		}
		pclose($fp);
		
		if ($istext) {
			$data = iconv($this->encoding, 'UTF-8', $data);
		}
		
		return $data;
	}
	
	public function SvnSource($name) {
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
	
	public function setAuth($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}
	
	public function getDirectory($path) {
		$raw = $this->command('list', $path, true);
		$arr = explode("\n", $raw);
		return $arr;
	}
	
	public function getFile($path) {
		$raw = $this->command('cat', $path, false);
		return $raw;
	}
	
	public function getHistory($path) {
		$raw = $this->command('log', $path, true);
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