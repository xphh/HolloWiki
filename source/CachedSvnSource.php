<?php
require_once("source/SvnSource.php");
require_once("FileSystemCache/lib/FileSystemCache.php");

FileSystemCache::$cacheDir = 'cache';

class CachedSvnSource extends SvnSource
{
	private $expired = 3600;
	
	public function setExpired($expired) {
		$this->expired = $expired;
	}
	
	public function getDirectory($path) {
		$key_data = $path;
		$key = FileSystemCache::generateCacheKey($key_data, $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false) {
			$data = parent::getDirectory($path);
			FileSystemCache::store($key, $data, $this->expired);
		}
		return $data;
	}
	
	public function getFile($path, $rev = null) {
		$key_data = ($rev == null) ? $path : "$path@$rev";
		$key = FileSystemCache::generateCacheKey($key_data, $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false) {
			$data = parent::getFile($path, $rev);
			FileSystemCache::store($key, $data, $this->expired);
		}
		return $data;
	}
	
	public function getHistory($path) {
		$key_data = "$path@";
		$key = FileSystemCache::generateCacheKey($key_data, $this->getId());
		$data = FileSystemCache::retrieve($key);
		if ($data === false) {
			$data = parent::getHistory($path);
			FileSystemCache::store($key, $data, $this->expired);
		}
		return $data;
	}
	
}

?>