<?php
require_once("source/ProxySource.php");


class DirBrowserProxy extends ProxySource
{
	
	public function getFile($path, $rev = null) {
		$content = parent::getFile($path, $rev);
		if ($content == null && preg_match("/\\/Home\\.md$/", $path) == 1) {
			return $this->make(get_dirpath($path));
		} else {
			return $content;
		}
	}
	
	private function make($path) {
		$mdtext = "## Browser\n\n";
		
		$sid = $this->getId();
		
		$arr = explode("/", $path);
		$dirpath = "/";

		$sname = $this->getName();
		$link = hwLink($sid, "/");
		$mdtext = $mdtext."* [$sname]($link) / ";
		foreach ($arr as $dir) {
			if ($dir != "") {
				$dirpath = "$dirpath$dir/";
				$link = hwLink($sid, $dirpath);
				$mdtext = $mdtext."[$dir]($link) / ";
			}
		}
		$mdtext = $mdtext."\n";

		$list = $this->getDirectory($path);
		foreach($list as $name) {
			if ($name == "") {
				continue;
			}
			$subpath = $path.$name;
			if (substr($name, -1) == "/") {
				$link = hwLink($sid, $subpath);
				$mdtext = $mdtext." * [$name]($link)\n";
			}
		}
		foreach($list as $name) {
			if ($name == "") {
				continue;
			}
			$subpath = $path.$name;
			if (substr($name, -1) != "/") {
				$linkLog = hwLink($sid, $subpath, "@");
				$link = hwLink($sid, $subpath);
				$mdtext = $mdtext." * [[@]($linkLog)]";
				$mdtext = $mdtext." [$name]($link)\n";
			}
		}

		return $mdtext;
	}
		
}

?>