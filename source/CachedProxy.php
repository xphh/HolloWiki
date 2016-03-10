<?php
require_once("source/ProxySource.php");
require_once("FileSystemCache/lib/FileSystemCache.php");

FileSystemCache::$cacheDir = 'cache';

class CachedProxy extends ProxySource
{
	private $expired = 31536000;
	
	public function setExpired($expired) {
		$this->expired = $expired;
	}
	
	private function getNocache() {
		global $nocache;
		return $nocache;
	}
	
	public function getDirectory($path) {
		$key = FileSystemCache::generateCacheKey($path, $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false || $this->getNocache() == 1) {
			$data = parent::getDirectory($path);
			FileSystemCache::store($key, $data, $this->expired);
		}
		return $data;
	}
	
	public function getFile($path, $rev = null) {
		if ($this->getFileType($path) == 'md') {
			if ($rev == null) {
				return $this->getLatestFile($path);
			} else {
				return $this->getHistoricFile($path, $rev);
			}
		} else {
			return parent::getFile($path);
		}
	}
	
	private function getLatestFile($path) {
		$key = FileSystemCache::generateCacheKey($path, $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false || $this->getNocache() == 1) {
			$data = parent::getFile($path);
			FileSystemCache::store($key, $data, $this->expired);
		} else {
			$rootdir = dirname($_SERVER['PHP_SELF']);
			$past = $this->pastTime(time() - filemtime($key->getFileName()));
			$head = "![]($rootdir/assets/cached.png) ";
			$head = $head."This is a cached version from `$past` ";
			$head = $head."[Get the latest version]($rootdir/options.php?nocache=1)\n\n";
			$data = $head.$data;
		}
		return $data;
	}
	
	private function getHistoricFile($path, $rev) {
		$key = FileSystemCache::generateCacheKey("$path@$rev", $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false) {
			$data = parent::getFile($path, $rev);
			FileSystemCache::store($key, $data);
		}
		return $data;
	}
	
	private function pastTime($sec) {
		$bounds = array(1, 60, 60, 24, 30.5, 12);
		$units = array('second', 'minute', 'hour', 'day', 'month', 'year');
		$n = $sec < 1 ? 1 : $sec;
		foreach ($bounds as $i => $b) {
			if ($n >= $b) {
				$n = floor($n / $b);
				$u = $units[$i];
			} else {
				break;
			}
		}
		if ($n > 1) {
			$u = $u.'s';
		}
		return "$n $u ago";
	}
	
}

?>