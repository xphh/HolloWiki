<?php
require_once("source/ProxySource.php");
require_once("FileSystemCache/lib/FileSystemCache.php");

FileSystemCache::$cacheDir = 'cache';

class CachedSource extends ProxySource
{
	private $expired = 3600;
	
	public function setExpired($expired) {
		$this->expired = $expired;
	}
	
	public function getDirectory($path) {
		$key = FileSystemCache::generateCacheKey($path, $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false) {
			$data = parent::getDirectory($path);
			FileSystemCache::store($key, $data, $this->expired);
		}
		return $data;
	}
	
	public function getFile($path, $rev = null) {
		if ($rev == null) {
			return $this->getLatestFile($path);
		} else {
			return $this->getHistoricFile($path, $rev);
		}
	}
	
	private function getLatestFile($path) {
		$key = FileSystemCache::generateCacheKey($path, $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false) {
			$data = parent::getFile($path);
			FileSystemCache::store($key, $data, $this->expired);
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
	
}

?>