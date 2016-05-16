<?php
require_once('ac/Authorizor.php');

class SvnAuthorizor extends Authorizor
{
	private $path;
	private $svnuser;
	private $svnpwd;
	
	public function setConfig($path, $svnuser, $svnpwd) {
		$this->path = $path;
		$this->svnuser = $svnuser;
		$this->svnpwd = $svnpwd;
	}
	
	public function doAuth($username, $password) {
		$cmd = "svn info \"$this->path\" --username $username --password $password --non-interactive 2>&1";
		$fp = popen($cmd, 'r');
		while (!feof($fp)) { 
			$data .= fgets($fp);
		}
		pclose($fp);
		
		if (strstr($data, 'svn: E215004') == null) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getWlist() {
		$cmd = "svn cat \"$this->path\" --username $this->svnuser --password $this->svnpwd --non-interactive";
		$fp = popen($cmd, 'r');
		while (!feof($fp)) { 
			$data .= fgets($fp);
		}
		pclose($fp);
		
		$xml = simplexml_load_string($data);
		
		$wlist = array();
		foreach ($xml->acl->account as $account) {
			$wlist[] = $account->attributes()->user;
		}
		
		return $wlist;
	}

}

?>
