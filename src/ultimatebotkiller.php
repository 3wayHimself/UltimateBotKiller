<?php
/**
 *  UltimateBotKiller - PHP Library For Block 99.99% of Malicious Bots.
 *
 *  @author Alemalakra
 *  @version 1.0
 */

namespace Alemalakra\UltimateBotKiller;
require('Packer.php');

class UBK {
	function __construct() {
		session_start();
		ob_start();
	}
	function gua() {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			return $_SERVER['HTTP_USER_AGENT'];
		}
		return md5(rand());
	}
	function cutGua($string) {
		$five = substr($string, 0, 4);
		$last = substr($string, -3);
		return md5($five.$last);
	}
	function getHeaders() {
		return session_id() . count(getallheaders()) . count($_SERVER) . count(getallheaders()) . count($_SERVER) . count(getallheaders());
	}
	function getIP() {
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$IP = $_SERVER['REMOTE_ADDR'];
			$IP = str_replace('.', "", $IP);
			$IP = str_replace(':', "", $IP);
			$IP = str_replace('::', "", $IP);
			return $IP;
		}
		return "127001";
	}
	function getToken() {
		$token = md5(uniqid(rand(), TRUE));
		$token = $this->getIP() . $this->getHeaders() . $token . "rf9784" . $this->cutGua($this->gua());
		return $token;
	}
	function getCSRF() {
		if (isset($_SESSION['token'])) {
			$token_age = time() - $_SESSION['token_time'];
			if ($token_age <= 300){    /* Less than five minutes has passed. */
				return $_SESSION['token'];
			} else {
				$token = md5(uniqid(rand(), TRUE));
				$_SESSION['token'] = $this->getToken();
				$_SESSION['token_time'] = time();
				return $_SESSION['token'];
			}
		} else {
			$token = md5(uniqid(rand(), TRUE));
			$_SESSION['token'] = $this->getToken();
			$_SESSION['token_time'] = time();
			return $_SESSION['token'];
		}
	}
	function verifyCSRF($Value) {
		if (isset($_SESSION['token'])) {
			$token_age = time() - $_SESSION['token_time'];
			if ($token_age <= 300){    /* Less than five minutes has passed. */
				if ($Value == $_SESSION['token']) {
					$Explode = explode('rf9784', $_SESSION['token']);
					$gua = $Explode[1];
					if ($this->cutGua($this->gua()) == $gua) {
						// Validated, Done!
						for ($i=0; $i < rand(5,20); $i++) { 
							header('UBK_' . rand() . ': ' . rand());
						}
						unset($_SESSION['token']);
						unset($_SESSION['token_time']);
						return true;
					}
					unset($_SESSION['token']);
					unset($_SESSION['token_time']);
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	function js($Code) {
		return '<script>' . $Code . '</script>';
	}
	function getCode() {
		return "document.getElementById('" . $this->cutGua($this->gua()) . "').value = '". $this->getCSRF() ."'; document.getElementById('" . $this->cutGua($this->gua()) . "').name = '". $this->getCSRF() ."';";
	}
	function getInput($_s) {
		$boolean = rand(0,1) == 1;
		if ($boolean == true) {
			return '<input type="hidden" id='. "'" . $this->cutGua($this->gua()) . "'" . " />" . $this->js($_s);
		}
		return "<input type='hidden' id=" . '"' . $this->cutGua($this->gua()) . '"' . " />" . $this->js($_s);
	}
	function validateForm() {
		if (!(isset($_POST[$this->getCSRF()]))) {
			return false;
		}
		if (!($this->verifyCSRF($_POST[$this->getCSRF()]))) {
			return false;
		}
		return true;
	}
}

?>