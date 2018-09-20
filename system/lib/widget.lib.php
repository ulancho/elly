<?php
class widget extends controller {

	/**
		* Object of current loggined user
		* @var User object
	*/
	public $user;


	public function __construct() {
		$this->view = new template;
		$this->setContext(request::req('context', 'ajax'));
		$this->user = $this->view->user = user::getCurrentUser();
        
        
        $this->core  = CCore::getInstance();
        $this->theme = CTheme::getInstance();
	}

}