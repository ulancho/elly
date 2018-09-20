<?php
class userController extends controller {

	public function login() {
		//$this->setContext('json');
		$this->setTemplateName('login');


			// Если пользователь уже залогинен, ничего не делаем
		if ( $this->user->getId() ) {
			//если пользователь залогинен чтобы его перебросить
			$this->redirect( $this->view->link(config::get('userDefaultController', 'userDefaultAction')) );
			//$this->redirect('index','index');
		}
		//здесь получаем параметры из полей формы
		if (!request::isPost())// если это не пост запрос
		return array(
				'error'=>'',
			);

		$login = request::post('login');
		$password = request::post('password');
		$error = $this->user->login($login, $password);

		if ( $error===0 ) {

			//залогинился- перебрасываем
			$this->redirect( $this->view->link(config::get('userDefaultController', 'userDefaultAction')) );
			//$this->redirect('index','index');
		} else {
			return array(
				'error'=>'Неверный логин или пароль',
			);
		}
	}

    public function register() {
        //$this->setContext('json');

        /* if ($this->user->getId()) {
          $this->redirect("index", "index");
          } */

        $email = request::post('email');
        $fio = request::post('fio');
        $pass = request::post('password');
        $pass2 = request::post('password2');

        if ($email != "" && $fio != "" && $pass != "" && $pass2 != "") {

            $user = user::get(request::post('email'), 'email');

            if ($user) {
                $this->redirect("user", "login&err=1");
            }

            if ($_POST['password'] != $_POST['password2']) {
                $this->redirect("user", "login&err=2");
            }

            $today = date('Y-m-d H:i:s');

            $user = new user;
            $user->date_register = $today;
            $user->date_last_login = $today;
            $user->setFromArray($_POST);

            if ($user->create()) {
                $error = $user->login($user->email, $user->getPassword());
                $this->redirect("user", "login&err=4");
            } else {
                $this->redirect("user", "login&err=3");
            }
        } else {
            $this->redirect("user", "login&err=3");
        }
    }

	public function logout() {
		$this->user->logout();

		// перебрасываем на дефолтную страницу для незалогиненого пользователя
		$this->redirect( $this->view->link('user', 'login') );
	}


}
