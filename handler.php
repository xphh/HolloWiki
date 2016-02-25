<?php
function doLink($alt, $path, $sid) {
	return "[$alt](?p=".urlencode($path)."&s=$sid)";
}
	
function doLinkRev($alt, $path, $sid, $rev) {
	global $sid;
	return "[$alt](?p=".urlencode($path)."&s=$sid&r=$rev)";
}

class Handler
{
	private $sources;
	private $source;
	private $sid;
	
	public function Handler($sources, $sid) {
		$this->sources = $sources;
		$this->source = $sources[$sid];
		$this->sid = $sid;
	}
	
	public function makeIndex($path) {
		$mdtext = "## Directory\n";

		foreach ($this->sources as $i => $s) {
			if ($i == $this->sid) {
				$mdtext = $mdtext.$this->handleDirectory($path);
			} else {
				$mdtext = $mdtext."* ".doLink($s->getName(), "/", $i)."\n";
			}
		}
		
		return $mdtext;
	}
	
	public function handleDirectory($path) {
		$mdtext = "";

		$arr = explode("/", $path);
		$dirpath = "/";

		$mdtext = $mdtext."* ".doLink($this->source->getName(), "/", $this->sid)." / ";
		foreach ($arr as $dir) {
			if ($dir != "") {
				$dirpath = "$dirpath$dir/";
				$mdtext = $mdtext.doLink($dir, $dirpath, $this->sid)." / ";
			}
		}
		$mdtext = $mdtext."\n";

		$list = $this->source->getDirectory($path);
		foreach($list as $name) {
			if ($name == "") {
				continue;
			}
			$subpath = $path.$name;
			if (substr($name, -1) == "/") {
				$mdtext = $mdtext." * ".doLink($name, $subpath, $this->sid)."\n";
			} else {
				$mdtext = $mdtext." * [".doLinkRev("@", $subpath, $this->sid, "@")."] ".doLink($name, $subpath, $this->sid)."\n";
			}
		}

		return $mdtext;
	}
	
	public function handleFile($path, $rev) {
		if ($rev == null) {
			$filename = basename($path);
			$content = $this->source->getFile($path);
		} else {
			$filename = "r".$rev."-".basename($path);
			$content = $this->source->getFile($path."@".$rev);
		}

		$ext = get_extension($path);
		
		if ($ext == "md") {
			return $this->handleMarkdown($path, $content);
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
	
	public function handleHistory($path) {
		$logs = $this->source->getHistory($path);

		$mdtext = "##History\n";

		foreach ($logs as $node) {
			$rev = $node['rev'];
			$author = $node['author'];
			$date = $node['date'];
			$desc = $node['desc'];
			
			$mdtext = $mdtext."* ".doLinkRev($rev, $path, $this->sid, $rev).", $author, $date\n";
			$mdtext = $mdtext."> $desc\n\n";
		}

		return $mdtext;
	}
	
	private function handleMarkdown($path, $content) {
		global $i_fpath, $i_sid;
		$i_fpath = get_dirpath($path);
		$i_sid = $this->sid;
		
		function replace1($m) {
			global $i_fpath, $i_sid;
			return doLink($m[1], $i_fpath.$m[2], $i_sid);
		}
		function replace2($m) {
			global $i_fpath, $i_sid;
			return doLink($m[1], $i_fpath.$m[1].".md", $i_sid);
		}

		$content = preg_replace_callback("/\[(.*)\]\(#(.*)\)/U", replace1, $content);
		$content = preg_replace_callback("/\[\[(.*)\]\]/U", replace2, $content);

		return $content;
	}
	
}
?>