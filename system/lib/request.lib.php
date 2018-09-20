<?php
class request {

	public static function req($var, $default = NULL) {
		return isset($_REQUEST[$var]) ? $_REQUEST[$var] : $default;
	}

//	public static function post($var, $default = NULL) {
//		return isset($_POST[$var]) ? $_POST[$var] : $default;
//	}
    public static function post($var, $default = NULL) {
		return ($_POST[$var]!=null) ? $_POST[$var] : $default;
	}

	public static function get($var, $default = NULL) {
		return isset($_GET[$var]) ? $_GET[$var] : $default;
	}

	public static function cookie($var, $default = NULL) {
		return isset($_COOKIE[$var]) ? $_COOKIE[$var] : $default;
	}

	public static function setReq($var, $value='') {
		if ( is_array($var) ) {
			foreach ( $var as $key=>$value ) {
				$_REQUEST[$key] = $value;
			}
		} else {
			$_REQUEST[$var] = $value;
		}
	}

	public static function isPost() {
		return ( $_SERVER['REQUEST_METHOD'] === 'POST' );
	}
}