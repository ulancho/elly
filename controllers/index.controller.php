<?php



class indexController extends controller {
	

    public function index() {
		
		//$rows = db::rows("SELECT * FROM SP_USER");
        return array(
           'rows'=>$rows
        );

    }
	
	public function add_user() {
		
		// $this->setContext('json');
		
		
		$login = request::post('login', -1);
		$password = request::post('password');
		$company_id = request::post('company_id', -1);
		
		if($login == -1 || $login == ""){
			return array(
				'result'=>'Login field can not be empty.'
			);
		}
		if($company_id == -1 || $company_id == ""){
			return array(
				'result'=>'Choose company.'
			);
		}
		
		$dbHOST = "10.160.21.92";
		$dbNAME = "CERTIFICATE";
		$dbUSER = "sa";
		$dbPASS = "Areta312468";
		
		
		$link = mssql_connect($dbHOST, $dbUSER, $dbPASS);
		if (!$link) {
			return array(
				'result'=>'Can not connect to database.'
			);
		}
		
		mssql_select_db($dbNAME, $link);
		$proc = mssql_init('add_user', $link);
		mssql_bind($proc, '@login',  $login, SQLVARCHAR,  false,  false,  50);
		mssql_bind($proc, '@password',  $password, SQLVARCHAR,  false,  false,  50);
		mssql_bind($proc, '@company_id',  $company_id, SQLINT1);
		
		$result = mssql_execute($proc);
		
		if($result){
			return array(
				'login'=>$login,
				'result'=>"1"
			);
		}else{
			return array(
				'result'=>mssql_get_last_message()
			);
		}
		
		mssql_free_statement($proc); 
    }
	
	public function get_companies() {
		$this->setContext('json');
		
		//$companies=db::rows("SELECT * FROM SP_COMPANY");
		
        return array(
            'companies'=>$companies
        );
    }
	
	public function get_history() {
		$this->setContext('json');
		
		$id = request::get('id', -1);
		$login = request::get('login', -1);
		$result = 1;
		
		if($id == -1){
			$result = -1;
		}
		
        $rows = db::rows("SELECT codeid, CONVERT(DATETIME2(0), date_system), login, password, company, ip, version, status, comment FROM HISTORY WHERE status >= 0 AND login = '$login' ORDER BY DATE_SYSTEM DESC");
		
        return array(
			'result'=>$result,
            'rows'=>$rows
        );
    }
	
	public function edit_user() {
		$this->setContext('json');
		
		$id = request::post('id', -1);
		$login = request::post('login', '');
		$password = request::post('password', '');
		
		
		//db::query("UPDATE SP_USER set login = '".$login."' where codeid = ".$id);
		//db::query("UPDATE SP_USER set password = '".$password."' where codeid = ".$id);
		
		$result = 1;
		if($id == -1 || $login == ""){
			$result = -1;
		}
		
        return array(
			'result'=>$result
        );
    }
	
	public function add_company() {
		// $this->setContext('json');
		
		$company = request::post('company', -1);
		
		if($company == -1 || $company == ""){
			return array(
				'result'=>'No company name was given.'
			);
		}
		
		$dbHOST = "10.160.21.92";
		$dbNAME = "CERTIFICATE";
		$dbUSER = "sa";
		$dbPASS = "Areta312468";
		
		$link = mssql_connect($dbHOST, $dbUSER, $dbPASS);
		if (!$link) {
			return array(
				'result'=>'Can not connect to database.'
			);
		}
		
		mssql_select_db($dbNAME, $link);
		$proc = mssql_init('add_company', $link);
		mssql_bind($proc, '@name',  $company, SQLVARCHAR,  false,  false,  50);
		
		$result = mssql_execute($proc);
		
		if($result){
			return array(
				'company'=>$company,
				'result'=>"Company was added to database"
			);
		}else{
			return array(
				'result'=>mssql_get_last_message()
			);
		}
		
		mssql_free_statement($proc); 
		
        
    }
	
	public function get_users() {
        $this->setContext('json');

		$company = request::get('company');
		
		// print_r($company);
		// die();

        $rows = db::rows("SELECT * FROM SP_USER WHERE code_sp_company = $company");
		
        return array(
            'rows'=>$rows
        );
    }
	
	public function turn_user() {
        $this->setContext('json');

		$user = request::post('id');
		$turn = request::post('turn');
		$result = 0;

        if($turn == 'OFF'){
			db::query("UPDATE SP_USER set status = 0 where codeid = ".$user);
			$result = 1;
		}else if($turn == 'ON'){
			db::query("UPDATE SP_USER set status = 1 where codeid = ".$user);
			$result = 1;
		}
		
        return array(
            'result'=>$result
        );
    }
    
    public function db_trigger($mis = true)
    {
        if($mis)
            db::connect(
                config::get('dbHOST'), config::get('dbUSER'), config::get('dbPASS'), config::get('dbNAME')
            );
        else
            db::connect(
                config::get('dbHOST2'), config::get('dbUSER2'), config::get('dbPASS2'), config::get('dbNAME2')
            );
    }
    
    
    public function azamat(){
        $this->db_trigger(0);
        
        $field = db::rows("SELECT * FROM [sp_client]");
        //$action = $_REQUEST['action'];
        $action = 1;
        if (isset($action))
        {
            switch ($action)
            {
                case 1://выбор программы
                {
                if ($_REQUEST['pincode']=='1234')
                {
                $rows=array();
                $rows['result']=true;
                $rows['message']='taatan';
                echo json_encode($rows);
                print_r(($rows));
                die();
                
                }else
                {
                $rows=array();
                $rows['result']=false;
                $rows['message']='не найдено';
                echo json_encode($rows); 
                print_r(($rows));
                die();         
                }
            }break;
        }
            }
        $this->db_trigger();
        
    }
	
}


		
?>
