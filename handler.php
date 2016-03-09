<?php
require_once("source.php");
require_once("footprint.php");

function hwLink($sid, $path, $rev = null) {
	$link = "?p=".rawurlencode($path)."&s=".rawurlencode($sid);
	if ($rev != null) {
		$link = $link."&r=$rev";
	}
	return $link;
}

class Handler
{
	private $source;
	private $sid;
	private $path;
	private $rev;
	
	public function Handler($sid, $path, $rev) {
		$this->source = SourceFactory::get($sid);
		$this->sid = $this->source->getId();
		$this->path = $path;
		$this->rev = $rev;
	}
	
	public function makeIndex() {
		$mdtext = "## Directory\n";
		
		$mdtext = $mdtext.$this->makeFootprint();

		$sources = SourceFactory::all();
		foreach ($sources as $i => $s) {
			if ($i == $this->sid) {
				$mdtext = $mdtext.$this->makeDirectory();
			} else {
				$name = $s->getName();
				$link = hwLink($i, "/");
				$mdtext = $mdtext."* [$name]($link)\n";
			}
		}
		
		return $mdtext;
	}
	
	private function makeFootprint() {
		$mdtext = "* Footprint\n";
		
		$footprint = $_SESSION['footprint'];
		foreach ($footprint as $i => $fp) {
			$sid = $fp['sid'];
			$path = $fp['path'];
			$rev = $fp['rev'];
			$name = SourceFactory::get($sid)->getName().$path;
			if (is_numeric($rev)) {
				$name = $name."@$rev";
			} else {
				$name = $name."$rev";
			}
			$link = hwLink($sid, $path, $rev);
			$mdtext = $mdtext." $i. [$name]($link)\n";
		}
		
		return $mdtext;
	}
	
	private function makeDirectory() {
		$source = $this->source;
		$sid = $this->sid;
		$path = get_dirpath($this->path);
		$mark = basename($this->path);
		
		$mdtext = "";

		$arr = explode("/", $path);
		$dirpath = "/";

		$sname = $source->getName();
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

		$list = $source->getDirectory($path);
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
				if ($mark === $name) {
					$mdtext = $mdtext." **$name**\n";
				} else {
					$mdtext = $mdtext." [$name]($link)\n";
				}
			}
		}

		return $mdtext;
	}
	
	public function makeContent() {
		$source = $this->source;
		$sid = $this->sid;
		$path = $this->path;
		$rev = $this->rev;

		if ($rev != null && !is_numeric($rev)) {
			return $this->makeHistory();
		}
		
		$content = $source->getFile($path, $rev);
		
		if ($source->getFileType($path) == 'md') {
			if ($content == "") {
				return "## Content\n".$this->makeDirectory();
			} else {
				return $this->handleMarkdown($content);
			}
		} else {
			$filename = basename($path);
			if ($rev != null) {
				$filename = "r$rev-$filename";
			}
			
			$filesize = strlen($content);
			$type = get_mimetype($filename);
			$filename = rawurlencode($filename);

			header("Content-Type: $type");
			header("Content-Length: $filesize");
			header("Content-Disposition: attachment; filename=\"$filename\""); 
			echo $content;
			exit();
		}
	}
	
	private function makeHistory() {
		$source = $this->source;
		$sid = $this->sid;
		$path = $this->path;

		$logs = $source->getHistory($path);

		$mdtext = "##History\n";

		foreach ($logs as $node) {
			$rev = $node['rev'];
			$author = $node['author'];
			$date = $node['date'];
			$desc = $node['desc'];
			
			$link = hwLink($sid, $path, $rev);
			$mdtext = $mdtext."* [$rev]($link), $author, $date\n";
			$mdtext = $mdtext."> $desc\n\n";
		}

		return $mdtext;
	}
	
	private function handleMarkdown($content) {
		global $i_path, $i_fpath, $i_sid;
		$i_path = $this->path;
		$i_fpath = get_dirpath($this->path);
		$i_sid = $this->sid;
		
		function replace1($m) {
			global $i_path, $i_fpath, $i_sid;
			$url = $m[2];
			if (strpos($url, "/") === 0 || 
				strpos($url, "?") === 0 || 
				strpos($url, "http://") === 0 || 
				strpos($url, "https://") === 0) {
				return "[$m[1]]($url)";
			} else if (strpos($url, "@") === 0) {
				$rev = substr($url, 1);
				$rev = ($rev == "") ? "@" : $rev;
				$link = hwLink($i_sid, $i_path, $rev);
				return "[$m[1]]($link)";
			} else {
				$realurl = normalizePath($i_fpath.$url);
				$link = hwLink($i_sid, $realurl);
				return "[$m[1]]($link)";
			}
		}
		function replace2($m) {
			global $i_path, $i_fpath, $i_sid;
			$link = hwLink($i_sid, $i_fpath.$m[1].".md");
			return "[$m[1]]($link)";
		}

		$content = preg_replace_callback("/\[(.*)\]\((.+)\)/U", replace1, $content);
		$content = preg_replace_callback("/\[\[(.+)\]\]/U", replace2, $content);

		return $content;
	}
	
}
?>