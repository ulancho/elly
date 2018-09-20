<?php



class tableController extends controller {
	

    public function index() {

		$table = db::rows("SELECT * FROM reestr LIMIT 50");

		return array(
			'table'=>$table,
        );
    }

	
}


?>
