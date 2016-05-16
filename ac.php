<?php

class AccessControl
{
	private $authorizor;
	
	public function AccessControl($type) {
		require_once("ac/$type.php");
		$this->authorizor = new $type;
	}
	
	public function getAuthorizor() {
		return $this->authorizor;
	}
	
	public function auth() {
		if (!$this->isAuthorized()) {
			$action = $_GET['action'];
			if ($action == 'acpost') {
				$username = $_POST['username'];
				$password = $_POST['password'];
				if (!$this->inWlist($username)) {
					$this->showPage("域账号 $username 不在白名单内，请联系 许平 添加！");
				} else if (!$this->doAuth($username, $password)) {
					$this->showPage("认证失败");
				} else {
					$this->setAuthorized($username);
					$this->redirectBack();
				}
			} else {
				$this->setFrom();
				$this->showPage("");
			}
		}
	}
	
	private function isAuthorized() {
		session_start();
		if ($_SESSION['auth_user'] == null) {
			return false;
		} else {
			return true;
		}
	}
	
	private function setFrom() {
		session_start();
		$_SESSION['auth_from'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	private function redirectBack() {
		session_start();
		$from = $_SESSION['auth_from'];
		header("HTTP/1.1 302 Found");
		header("Location: $from");
		exit();
	}
	
	private function setAuthorized($username) {
		session_start();
		$_SESSION['auth_user'] = $username;
	}
	
	private function showPage($hint) {
		$html = file_get_contents("template/ac.html");
		$html = str_replace('{%hint%}', $hint, $html);
		echo $html;
		exit();
	}

	private function inWlist($username) {
		$wlist = $this->authorizor->getWlist();
		if (in_array($username, $wlist)) {
			return true;
		} else {
			return false;
		}
	}

	private function doAuth($username, $password) {
		return $this->authorizor->doAuth($username, $password);
	}

}

?>
