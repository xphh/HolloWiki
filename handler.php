<?php
function doLink($alt, $path) {
	return "[$alt](?p=".urlencode($path).")";
}
	
function doLinkRev($alt, $path, $rev) {
	return "[$alt](?p=".urlencode($path)."&r=$rev)";
}

class Handler
{
	private $source;
	
	public function Handler($source) {
		$this->source = $source;
	}
	
	public function handleDirectory($path) {
		$mdtext = "##Directory\n";

		$arr = explode("/", $path);
		$dirpath = "/";

		$mdtext = $mdtext."* ".doLink("root", "/")."/";
		foreach ($arr as $dir) {
			if ($dir != "") {
				$dirpath = "$dirpath$dir/";
				$mdtext = $mdtext.doLink($dir, $dirpath)."/";
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
				$mdtext = $mdtext." * ".doLink($name, $subpath)."\n";
			} else {
				$mdtext = $mdtext." * [".doLinkRev("@", $subpath, "@")."] ".doLink($name, $subpath)."\n";
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
			
			$mdtext = $mdtext."* ".doLinkRev($rev, $path, $rev).", $author, $date\n";
			$mdtext = $mdtext."> $desc\n\n";
		}

		return $mdtext;
	}
	
	private function handleMarkdown($path, $content) {
		global $fpath;
		$fpath = get_dirpath($path);
		
		function replace1($m) {
			global $fpath;
			return doLink($m[1], $fpath.$m[2]);
		}
		function replace2($m) {
			global $fpath;
			return doLink($m[1], $fpath.$m[1].".md");
		}

		$content = preg_replace_callback("/\[(.*)\]\(#(.*)\)/U", replace1, $content);
		$content = preg_replace_callback("/\[\[(.*)\]\]/U", replace2, $content);

		return $content;
	}
	
}
?>