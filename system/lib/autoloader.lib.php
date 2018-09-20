<?php
class autoloader {

	public static $loader;

	public static function init() {
		if (self::$loader == NULL)
			self::$loader = new self();

		return self::$loader;
	}

	public function __construct() {
		set_include_path(SITE_ROOT);
		spl_autoload_register(array($this, 'widgets'));
		spl_autoload_register(array($this, 'controllers'));
		spl_autoload_register(array($this, 'library'));
		spl_autoload_register(array($this, 'model'));
	}

	public function model($class) {
		if ( substr($class, -6) == 'Widget' || substr($class, -10) == 'Controller' ) {
			return;
		}

		set_include_path(SITE_ROOT.'/models');
		spl_autoload_extensions('.php');
		spl_autoload($class.'.model');

			// вызываем функцию инициализации у модели что бы узнать поля и типы полей в таблице. Если класс model то вызывать функцию не надо
		if ( $class!='model' && is_callable(array($class, '__init')) ) {
			call_user_func(array($class, '__init'));
		}
	}

	public function library($class) {
		if ( substr($class, -6) == 'Widget' || substr($class, -10) == 'Controller' ) {
			return;
		}

		set_include_path(SITE_ROOT.'/system/lib');
		spl_autoload_extensions('.php');
		spl_autoload($class.'.lib');

	}

	public function controllers($class) {
		if ( substr($class, -10) != 'Controller' ) {
			return;
		}

		$class = preg_replace('/Controller$/ui','',$class);
		set_include_path(SITE_ROOT.'/controllers');
		spl_autoload_extensions('.php');
		spl_autoload($class.'.controller');

	}

	public function widgets($class) {
		if ( substr($class, -6) != 'Widget' ) {
			return;
		}

		$class = preg_replace('/Widget$/ui','',$class);
		set_include_path(SITE_ROOT.'/widgets');
		spl_autoload_extensions('.php');
		spl_autoload($class.'/'.$class.'.widget');
	}

}