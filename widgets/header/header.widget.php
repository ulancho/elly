<?php

class headerWidget extends widget {

    public function index() {
		
		//$fio = mb_detect_encoding($this->user->fio);//
        //$fio = iconv("cp1251","utf-8",$this->user->fio);
		$login = $this->user->login;
        
		//$fio_1 = db::field("SELECT [nameid] FROM [sp_user] WHERE codeid = 1");
		
		//print_r($this->user->fio);
        //die();
		//$rows=db::rows("SELECT * FROM SP_COMPANY");
		
		// print_r($rows);
		// die();
		
        return array(
            //'fio'=>$fio,
            //'rows'=>$rows
        );
    }
	
	
}
