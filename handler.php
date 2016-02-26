<?php
function hwLink($sid, $path, $rev) {
	$link = "?p=".rawurlencode($path)."&s=".rawurlencode($sid);
	if ($rev != null) {
		$link = $link."&r=$rev";
	}
	return $link;
}

class Handler
{
	private $sources;
	
	public function Handler($sources) {
		$this->sources = $sources;
	}
	
	public function makeIndex($sid, $path) {
		$mdtext = "## Directory\n";

		foreach ($this->sources as $i => $s) {
			if ($i == $sid) {
				$mdtext = $mdtext.$this->handleDirectory($sid, $path);
			} else {
				$name = $s->getName();
				$link = hwLink($i, "/", null);
				$mdtext = $mdtext."* [$name]($link)\n";
			}
		}
		
		return $mdtext;
	}
	
	public function handleDirectory($sid, $path) {
		$mdtext = "";

		$arr = explode("/", $path);
		$dirpath = "/";

		$sname = $this->sources[$sid]->getName();
		$link = hwLink($sid, "/", null);
		$mdtext = $mdtext."* [$sname]($link) / ";
		foreach ($arr as $dir) {
			if ($dir != "") {
				$dirpath = "$dirpath$dir/";
				$link = hwLink($sid, $dirpath, null);
				$mdtext = $mdtext."[$dir]($link) / ";
			}
		}
		$mdtext = $mdtext."\n";

		$list = $this->sources[$sid]->getDirectory($path);
		foreach($list as $name) {
			if ($name == "") {
				continue;
			}
			$subpath = $path.$name;
			if (substr($name, -1) == "/") {
				$link = hwLink($sid, $subpath, null);
				$mdtext = $mdtext." * [$name]($link)\n";
			} else {
				$linkLog = hwLink($sid, $subpath, "@");
				$link = hwLink($sid, $subpath, null);
				$mdtext = $mdtext." * [[@]($linkLog)] [$name]($link)\n";
			}
		}

		return $mdtext;
	}
	
	public function handleFile($sid, $path, $rev) {
		if ($rev != null && !is_numeric($rev)) {
			return $this->handleHistory($sid, $path);
		}
		
		if ($rev == null) {
			$filename = basename($path);
			$content = $this->sources[$sid]->getFile($path);
		} else {
			$filename = "r".$rev."-".basename($path);
			$content = $this->sources[$sid]->getFile($path."@".$rev);
		}

		$ext = get_extension($path);
		
		if ($ext == "md") {
			return $this->handleMarkdown($sid, $path, $content);
		} else {
			$filesize = strlen($content);
			$type = get_mimetype($filename);
			$filename = urlencode($filename);

			header("Content-Type: $type");
			header("Content-Length: $filesize");
			header("Content-Disposition: attachment; filename=\"$filename\""); 
			echo $content;
			exit();
		}
	}
	
	private function handleHistory($sid, $path) {
		$logs = $this->sources[$sid]->getHistory($path);

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
	
	private function handleMarkdown($sid, $path, $content) {
		global $i_fpath, $i_sid;
		$i_fpath = get_dirpath($path);
		$i_sid = $sid;
		
		function replace1($m) {
			global $i_fpath, $i_sid;
			$url = $m[2];
			if (strpos($url, "/") === 0 || 
				strpos($url, "?") === 0 || 
				strpos($url, "http://") === 0 || 
				strpos($url, "https://") === 0) {
				return "[$m[1]]($url)";
			} else {
				$link = hwLink($i_sid, $i_fpath.$url, null);
				return "[$m[1]]($link)";
			}
		}
		function replace2($m) {
			global $i_fpath, $i_sid;
			$link = hwLink($i_sid, $i_fpath.$m[1].".md", null);
			return "[$m[1]]($link)";
		}

		$content = preg_replace_callback("/\[(.*)\]\((.+)\)/U", replace1, $content);
		$content = preg_replace_callback("/\[\[(.+)\]\]/U", replace2, $content);

		return $content;
	}
	
}
?>