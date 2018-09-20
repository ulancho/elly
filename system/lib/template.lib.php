<?php
class template extends CTheme{

	/**
	 * Path to .tpl file for content() function.
	 * @var string
	*/
	private $page_content='';

	/**
	 * Path to .tpl file for render() function. Can be wigdets template, current controller template, or main.tpl
	 * @var string
	*/
	private $page_template='';

	/**
	 * Array of current controller and action. array('controller'=>'index', 'action'=>'index')
	 * @var array
	*/
	private $current_page=array();

	/**
	 * Array of current page metatags
	 * @var array
	*/
	private $page_metatags=array();

	/**
	 * Current page <title>
	 * @var string
	*/
	private $page_title='';

	/**
	 * Array of template variables
	 * @var array
	*/
	private $vars=array();

	/**
	 * Array of template javascript files
	 * @var array
	*/
	private $scripts=array();

	/**
	 * Array of template css files
	 * @var array
	*/
	private $styles=array();

	/**
	 * Object of current loggined user
	 * @var User object
	*/
	public $user;


	/*public static function &getInstance() {
		static $instance;

		if( is_null($instance) ) {
			$instance = new template();
		}
        
		return $instance;
	}*/

	public function __construct() {
		$this->setTitle(config::get('siteTitle'));
	}

	/**
	 * Render current template (main.tpl for whole html, or only current controller template for AJAX context)
	*/
	public function render() {
		
        // [ELLY+]
        if(!$this->page_content && count($this->varsArr)) {
            return $this->parse($this->page_template);
        }
        
        ob_start();

		if ( $this->vars ) {
			foreach ( $this->vars as $varname=>$value ) {
				$$varname = $value;
			}
		}

		if ( config::get('DEBUG') ) {
			print "\r\n\r\n<!-- START TEMPLATE ".$this->page_template." -->\r\n\r\n";
		}
        
        include $this->page_template;

		if ( config::get('DEBUG') ) {
			print "\r\n\r\n<!-- END TEMPLATE ".$this->page_template." -->\r\n\r\n";
		}
        
        if (CDebug::$ajax != 'ajax') print CDebug::getInstance()->debugEND();

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Render current controller template ($this->content() function in main.tpl for display current page content)
	*/
	public function content() {
		
        // [ELLY+]
        if(count($this->varsArr)) {
            return $this->parse($this->page_content);
        }
        
        ob_start();

		$toJsonVars = array();
		if ( !empty($this->vars) ) {
			foreach ($this->vars as $var_name=>$var_value) {
				if ( $var_name=='toJson' ) {
					foreach ($var_value as $toJson_var_name => $toJson_var_value) {
                        $$var_name = $var_value;
						$toJsonVars[$toJson_var_name] = $toJson_var_value;
					}
				} else {
                    $$var_name = $var_value;
				}
			}
		}
        if ( !empty($toJsonVars) ) {
			print "\r\n\r\n<!-- VARIABLES ".$this->page_content." -->\r\n\r\n";

			$output = '<script type="text/javascript">';
			foreach ($toJsonVars as $var_name=>$var_value) {
				$output .= $this->varToJson($var_name, $var_value);
			}
			$output .= '</script>';
			print $output;
		}        
                        
		if ( config::get('DEBUG') ) {
			print "\r\n\r\n<!-- START TEMPLATE ".$this->page_content." -->\r\n\r\n";
		}

		include $this->page_content;

		if ( config::get('DEBUG') ) {
			print "\r\n\r\n<!-- END TEMPLATE ".$this->page_content." -->\r\n\r\n";
		}
                
		return ob_get_clean();
	}

	/**
	 * Render widget in template (gets class of given widget and call renderWidget function)
	 * @param string $widget Widget name (e.g. 'header')
	 * @param array  $vars Widget variables
	 * @return string
	*/
	public function widget($widget, $vars=array()) {
		$widgetClass = $widget.'Widget';
		$widgetObj = new $widgetClass;
		$widgetObj->view->setCurrentPage($this->getControllerName(), $this->getActionName());
		$widgetObj->params = $vars;

		$tpl_vars = $widgetObj->index();
		if ( empty($tpl_vars) ) {
			$tpl_vars = array();
		}

		$widgetObj->view->setVars(array_merge($vars, $tpl_vars));

		return $widgetObj->view->renderWidget($widget);
	}

	/**
	 * Render widget template
	 * @param string $widget Widget name (e.g. 'header')
	 * @param array  $tpl_vars Widget template variables
	 * @return string
	*/
	public function renderWidget($widget) {
		ob_start();
		if ( !empty($this->vars) ) {
			foreach ($this->vars as $var_name=>$var_value) {
				$$var_name = $var_value;
			}
		}

		$template_folder = 'templates';
		$widget_folder = 'widgets';
        $path = $widget_folder.'/'.$widget.'/'.$widget.'.widget.tpl';
		if ( config::get('DEBUG') ) {
			print "\r\n\r\n<!-- START WIDGET /".$path." -->\r\n\r\n";
		}

		include SITE_ROOT.'/'.$path;

		if ( config::get('DEBUG') ) {
			print "\r\n\r\n<!-- END WIDGET /".$path." -->\r\n\r\n";
		}
        
        CDebug::getInstance()->group('Используемые шаблоны', $path, true, true);
        
		return ob_get_clean();
	}

	/**
	 *
	 * @param string $controller Controller name (e.g. 'user')
	 * @param string $action Action name (e.g. 'login')
	 * @param array  $params aditional $_GET vars
	 * @return string
	*/
	public function link($controller='', $action='', $params=array()) {

			// если первый параметр - массив, ложим массив в $params
		if ( is_array($controller) ) {
			$params = $controller;
			$controller = '';
		} else if ( $controller==='index' && $action==='index' ) {
			return '?';
		}

			// если второй параметр - массив, ложим массив в $params
		if ( is_array($action) ) {
			$params = $action;
			$action = '';
		}

			// формируем гет параметры. Изначально это 'контроллер@экшен'.
		$get = array($controller . '@' . $action);

			// если передали параметры, добавляем их в $get
		if ( !empty($params) ) {
			$tmpParams = array();
			foreach ( $params as $name=>$value ) {
				$tmpParams[] = $name.'='.$value;
			}
			$get[] = implode('&', $tmpParams);
		}

		$ret = '?' . implode('&', $get);

		return ( $ret=='?@' ) ? '' : $ret;
	}

	/**
	 * Check if file or files is exists and return array of existing files
	 * @param mixed $files String relative path to file or an array of pathes (e.g. 'system/res/css/style.css')
	 * @return array
	*/
	public function _checkHeadFiles($files) {
		$checkedFiles = array();
		if ( is_array($files) ) {
			foreach( $files as $file ) {
				if ( file_exists($file) ) {
					$checkedFiles[] = $file;
				} else {
					print_arr('Fatal error: File "'.$file.'" is not exists!', 1);
				}
			}
		} else {
			if ( file_exists($files) ) {
				$checkedFiles[] = $files;
			} else {
				print_arr('Fatal error: File "'.$files.'" is not exists!', 1);
			}
		}

		return $checkedFiles;
	}

	/**
	 * Attach javascript and minify js if production mode (DEBUG=0)
	 * @param mixed $path Relative path to js script or an array of pathes (e.g. 'system/res/css/style.css')
	 * @return string
	*/
	public function headScript($path) {
		$files = $this->_checkHeadFiles($path);
		if ( is_array($path) ) {
			$this->scripts = array_merge($this->scripts, $path);
		} else {
			$this->scripts[] = $path;
		}
	}

	/**
	 * Attach stylesheet and minify css if production mode (DEBUG=0)
	 * @return string
	*/
	public function headStyle($path) {
		$files = $this->_checkHeadFiles($path);
		if ( is_array($path) ) {
			$this->styles = array_merge($this->styles, $path);
		} else {
			$this->styles[] = $path;
		}
	}

	/**
	 * Attach stylesheet and minify css if production mode (DEBUG=0)
	 * @return string
	*/
	public function attachStyle() {
		$styles = '';
		if ( config::get('DEBUG', false)) { // config::get('DEBUG', false) ) { //портится при выключении debug
			foreach( $this->styles as $file ) {
				$styles .= "<link href=\"{$file}\" rel=\"stylesheet\">\r\n";
			}
		} else {
			$files = implode(',', $this->styles);
			$styles = "<link href=\"system/mod/minify/?f={$files}\" rel=\"stylesheet\">\r\n";
		}

		return $styles;
	}

	/**
	 * Attach stylesheet and minify css if production mode (DEBUG=0)
	 * @return string
	*/
	public function attachScript() {
		$scripts = '';
		if ( config::get('DEBUG', false) ) {
			foreach( $this->scripts as $file ) {
				$scripts .= "<script type=\"text/javascript\" src=\"{$file}\"></script>\r\n";
			}
		} else {
			$files = implode(',', $this->scripts);
			$scripts = "<script type=\"text/javascript\" src=\"system/mod/minify/?f={$files}\"></script>\r\n";
		}

		return $scripts;
	}
    
	/**
	 * Sets tpl filename for render() function
	 * @param string $path Template path (e.g. '/templates/main.tpl')
	 */
	public function setTemplate($path) {
		$this->page_template = $path;
        $path = str_replace(SITE_ROOT.'/', '', $path);
        CDebug::getInstance()->group('Используемые шаблоны', $path, true, true);
	}

    /**
     * Sets tpl filename of current controller (used in content() function)
     * @param string $path Template path (e.g. '/templates/index/index.tpl')
     */
	public function setContent($path) {
        $this->page_content = $path;
        $path = str_replace(SITE_ROOT.'/', '', $path);
        CDebug::getInstance()->group('Используемые шаблоны', $path, true, true);
	}

	/**
	 * Sets tpl vars
	 * @param array $vars Array of template vars
	 */
	public function setVars($vars) {
		$this->vars = $vars;
	}

	/**
	 * Sets current page controller and action
	 * @param string $controller Current controller
	 * @param string $action Current action
	*/
	public function setCurrentPage($controller='index', $action='index') {
		$this->current_page = array('controller'=>$controller, 'action'=>$action);
	}

	/**
	 * Get current page controller
	 * @return string
	*/
	public function getControllerName() {
		return ( isset($this->current_page['controller']) ) ? $this->current_page['controller'] : '';
	}

	/**
	 * Get current page action
	 * @return string
	*/
	public function getActionName() {
		return ( isset($this->current_page['action']) ) ? $this->current_page['action'] : '';
	}

	/**
	 * Sets <title> for current page
	 * @param string $title Page title
	*/
	public function setTitle($title) {
		$this->page_title = $title;
	}

	/**
	 * Get <title> for current page
	 * @return string
	*/
	public function getTitle() {
		return $this->page_title;
	}

	/**
	 * Sets metatag for current page
	*/
	public function setMetatags($name, $content) {
		$this->page_metatags[$name]=$content;
	}

	/**
	 * Gets metatags of current page
	 * @return string
	*/
	public function getMetatags() {
		$metatags = '';
		foreach($this->page_metatags as $key=>$value) $metatags .= "<meta name=\"$key\" content=\"$value\" />\r\n";
		return $metatags;
	}


	public function varToJson($varname, $value) {

		if ( empty($value) ) {

			$output = '[];';

		} else if ( is_object($value) && is_subclass_of($value, 'model') ) {
		  		  
			$output = json_encode($value->toJsonArray());

		} else if ( is_array($value) && is_subclass_of(current($value), 'model') ) {

			$json_value = '';
			foreach ($value as $key=>$model) {
				$json_value .= $varname . '.push(' . json_encode($model->toJsonArray()) . ');';
			}
			$output = '[];' . $json_value . "\r\n$varname = new EllyModel($varname, 'codeid');\r\n";

		} else {

			$output = json_encode($value);

		}

		return "\r\nvar " . $varname . '=' . $output."\r\n";
	}

}