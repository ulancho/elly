<?php
class controller {

	protected $context;		// output context (json, ajax, html)
	protected $template;	// template name for rendering (default is main.tpl)
	public $user;			// current user object
    
    // [ELLY+]
    protected $core;
    protected $theme;
    protected $subTemplate;
    public $vars = array();

	public function __construct($view) {
		$this->view =& $view;
        
        // [ELLY+]
        $this->core  = CCore::getInstance();
        $this->theme = CTheme::getInstance();
        
		$this->setTemplateName(config::get('defaultTemplate', 'main'));
		$this->setContext(request::req('context', 'html'));
		if ( config::get('dbHOST')!='' ) {
			$this->user = $this->view->user = user::getCurrentUser();
		}

        if ( !$this->user->getId() && $this->view->getControllerName()!='user' && $this->view->getActionName()!='login'  ) {
            $this->redirect($this->view->link('user', 'login'));
        }
	}
    
    // [ELLY+] {
    public function json($data)  { $this->ajaxAdd($data, 'json'  ); }
    public function script($data){ $this->ajaxAdd($data, 'script'); }
    public function html($data)  { $this->ajaxAdd($data, 'html'  ); }
    public function ajaxAdd($data, $type)
    { 
        $this->setContext('json');
        $this->vars[$type] = $data;
    }
    public function loadTheme($mainTheme = 'main', $subTheme = '')
    {
        $this->setTemplateName($mainTheme);
        $this->setSubTemplateName($subTheme);
        $this->setContext((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
            ? 'ajax' : 'html');   
    }
    public function setSubTemplateName($subTemplate) {
        $this->subTemplate = $subTemplate;
    }
    public function getSubTemplateName() {
		return $this->subTemplate;
	}
    // [ELLY+] }
    

	public function setContext($context) {
		$this->context = ( in_array($context, array('json','ajax','html')) ) ? $context : 'html';
	}

	public function getContext() {
		return ( !empty($this->context) ) ? $this->context : 'html';
	}

	public function setTemplateName($template) {
		$this->template = $template;
	}

	public function getTemplateName() {
		return $this->template;
	}

	public function redirect($url = '') {
		header('Location: '.$url);
		exit();
	}
    
    /**
     * Очистка потенциально опасных входных данных
     * @value String значение 
     * @type  String тип данных, как в таблице
     */
    public function clear($value, $type = 'varchar')
    {
        $type = strtoupper($type);
        return $this->core->_securityField($value, $type, true);
    }

}