<?php
require_once("source/BaseSource.php");

class SvnSource extends BaseSource
{
	
	private function command($op, $path, $istext) {
		$cmd = "svn $op \"$this->basedir$path\" --username $this->username --password $this->password --non-interactive";
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
	
	public function getDirectory($path) {
		$raw = $this->command('list', $path, true);
		$arr = explode("\n", $raw);
		return $arr;
	}
	
	public function getFile($path, $rev = null) {
		if ($rev != null) {
			$path = "$path@$rev";
		}
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