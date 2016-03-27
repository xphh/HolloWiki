<?php
require_once("parsedown/Parsedown.php");

class PageMaker
{
	private $data = array();
	
	public function put($key, $value) {
		$this->data[$key] = $value;
	}
	
	public function putMarkdown($key, $value) {
		$Parsedown = new Parsedown();
		$value = $Parsedown->text($value);
		$this->put($key, $value);
	}
	
	public function generate($filename) {
		$template = file_get_contents($filename);
		foreach ($this->data as $key => $value) {
			$template = str_replace("{%$key%}", $value, $template);
		}
		return $template;
	}
}

?>