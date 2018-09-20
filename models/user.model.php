<?php
class user extends model {

	protected static $_tableName = 'sp_user';

	protected static $_modelName = '';
	protected static $_rowIdentity = '';
	protected static $_dateFields = array();
	protected static $_tableStructure = array();


	private $_password = NULL;
	private $_displayname = NULL;
    public $position_access = array();
    /*
	public function getPosition() {
        //$position_name = 'Директор';
        
        if ($this->_rowData['code_position']) {
            
            $position_name = db::field('SELECT name from [dbo].[sp_user_struct] WHERE codeid='.$this->_rowData['code_position']);
            
            return $position_name;
        } 
    } 
    
	public function getCodeStruct() {

        return $this->_rowData['code_struct'];

    }
	*/
	/**
	 * Get user visible name by existing users data
	 * @return string
	*/
	public function getName() {
		if ( !empty($this->_displayname) ) {
			return $this->_displayname;
		} elseif ( !empty($this->_rowData['[fio]']) ) {
			$this->_displayname = $this->_rowData['[fio]'];
		}

		return $this->_displayname;
	}

	/**
	 * Used for encrypt user password (currently no encrypting)
	 * @return string
	*/
	public function password_encrypt($password) {
		return $password;
	}

	/**
	 * Get password
	 * @return string
	*/
	public function getPassword() {
		return $this->_password;
	}

	/**
	 * Set password
	*/
	public function setPassword($password) {
		$this->_password = $password;
	}

	/**
	 * Logout current user and clear cookies
	*/
	public function logout() {
		$this->clearCookies();
	}

	/**
	 * Check user identity and password, set cookies, set error if wrong password
	 * @param  string $user_identity
	 * @param  string $password
	 * @return bool
	*/
	public static function login($user_identity, $password) {
		$user = self::find(config::get('userIdentity', 'login') . '=\'' . $user_identity . '\'');

		$error = false;

		if ( empty($user) ) {
			$error = 10;
		} else {
			$user = array_shift($user);
			if ( $user->status == -1) {
				$error = 12;
			}
			if ( $user->getPassword()!=$user->password_encrypt($password) ) {
				$error = 11;
			}
		}

		if ( $error ) {
			return $error;
		}

		$user->setCookies();
		return 0;
	}

	/**
	 * Set session variables for user (identity and password)
	*/
	public function setCookies() {
		setcookie('SESSION_NUMBER', $this->getId(), time() + (10 * 365 * 24 * 60 * 60));
		setcookie('PHPSESSION', md5(md5(md5($this->getPassword())) . $this->getName()), time() + (10 * 365 * 24 * 60 * 60));
		$_SESSION['code_login'] = $this->getId();
	}

	/**
	 * Clear session variables for user (identity and password)
	*/
	private function clearCookies() {
		setcookie('SESSION_NUMBER', 0, time() - 600);
		setcookie('PHPSESSION', 0, time() - 600);
		unset($_COOKIE['SESSION_NUMBER']);
		unset($_COOKIE['PHPSESSION']);
	}

	/**
	 * Check user cookies
	 * @return bool
	*/
	public function checkCookies() {
		return ( $this->getId() && isset($_COOKIE['SESSION_NUMBER']) && isset($_COOKIE['PHPSESSION']) && $_COOKIE['SESSION_NUMBER']==$this->getId() && $_COOKIE['PHPSESSION']==md5(md5(md5($this->getPassword())) . $this->getName()));
	}


	/**
	 * Get user avatar (return default avatar if no image)
	 * @return string
	*/
	public function getAvatar() {
		if ( !empty($this->_rowData[config::get('userAvatar', 'avatar')]) ) {
			$this->_avatar = $this->_rowData[config::get('userAvatar', 'avatar')]->path;
		}
		return $this->_avatar;
	}

	/**
	 * Get user avatar (return default avatar if no image)
	 * @return string | bool
	*/
	public function uploadAvatar($file) {
		if ( $file['error']!=UPLOAD_ERR_OK ) {

			return false;

		} else {
			$avatar = $this->avatar;

			if ( !empty($avatar) && file_exists(SITE_ROOT . '/uploads/avatars/' . $avatar) ) {
				unlink(SITE_ROOT . '/uploads/avatars/' . $avatar);
			}
			$avatar = $this->getId() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

			if ( move_uploaded_file($file['tmp_name'], 'uploads/avatars/' . $avatar) ) {
				return $avatar;
			} else {
				return false;
			}

		}
	}



	/**
	 * Get current loggined user
	 * @return User object
	*/
	public static function &getCurrentUser() {
		static $instance;
		if( is_null($instance) ) {
			if ( !empty($_COOKIE['SESSION_NUMBER']) ) {
				$instance = self::getUser($_COOKIE['SESSION_NUMBER']);
				if ( !$instance || !$instance->checkCookies() ) {
					$instance = new user;
				}
			} else {
				$instance = new user;
			}
		}
		return $instance;
	}

	/**
	 * Get user by identity from static cache, or from APC cache
	 * @return User object
	*/
	public static function &getUser($identity) {

		if ( false === ( $user = cache::getStatic('user', $identity) ) ) {
			$user = self::get($identity);
			cache::setStatic('user', $identity, $user);
		}
		if ( !$user ) {
			$user = new user;
		}
		return $user;
	}

	public function toArray() {
		$data = parent::toArray();
		$data['password'] = $this->getPassword();

		return $data;
	}

	public function toJsonArray() {
		$data = parent::toJsonArray();

		//unset($data['password']);
		$data['password'] = $this->getPassword();
		$data['avatar'] = $this->getAvatar();
		

		return $data;
	}

	public function setFromArray($data) {
		parent::setFromArray($data);
		$this->getAvatar();
		$this->setPassword($this->password);
		unset($this->_rowData['password']);

		
	}

	public function create() {
		$result = parent::create();

		if ( !$result ) return false;

		// foreach ($this->code_departments as $code_department) {
			// db::insert('department_doctor', array(
					// 'code_department'=>$code_department,
					// 'code_doctor'=>$this->getId(),
				// ));
		// }
	}

	public function update() {
		$result = parent::update();

		if ( !$result ) return false;

		// db::query("DELETE FROM department_doctor WHERE code_doctor='?'", $this->getId());
		// foreach ($this->code_departments as $code_department) {
			// db::insert('department_doctor', array(
					// 'code_department'=>$code_department,
					// 'code_doctor'=>$this->getId(),
				// ));
		// }
	}

	public function delete() {
		$this->status = -1;
		return $this->update();
	}

	

	public static function get($identity, $fieldname='') {
		$user = parent::get($identity, $fieldname);
        //print_r($identity);
        //print_r($fieldname);
        //die;
//		if ( $user ) {
//			$user->position_access = view_position_access::get($user->codeid);
//		}
		return $user;
	}


//	public function isAdmin() {
//		return $this->user_level==1;
//	}

//	public function isRegisterOrAdmin() {
//		return $this->user_level == 1 || $this->user_level == 3;
//	}

}