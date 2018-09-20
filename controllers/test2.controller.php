
<?php 
		$dbHOST = "217.29.18.149,1434";
		$dbNAME = "TSENSOR";
		$dbUSER = "sa";
		$dbPASS = "Areta312468";

		$link = mssql_connect($dbHOST, $dbUSER, $dbPASS);
		if (!$link) {
			return array(
				'result'=>'Can not connect to database.'
			);
		}
		
		mssql_select_db($dbNAME, $link);
		$rows = db::rows("SELECT t_sensor1 FROM reestr ORDER BY codeid");
		return array(
        'tabl'=>$rows
        );

    $shirina = 700; 
    $visota = 150; 
    $graf = ImageCreate ($shirina, $visota);       
    $beliy = ImageColorAllocate ($graf, 255, 255, 255);
    $cherniy = ImageColorAllocate ($graf, 0, 0, 0);
    $siniy = ImageColorAllocate ($graf, 0, 0, 255);   

	ImageLine ($graf, 10, $visota-10, $shirina-10, $visota-10, $cherniy); // проводим горизонтальную линию, ось абсцисс (время)

	ImageLine ($graf, 10, 10, 10, $visota-10, $cherniy); // проводим вертикальную линию, чтобы ось ординат (число посещений)  

    $temperatureMax = 200; // максимум  
      

	foreach($rows as $key => $value){
		for ($i=1; $i<=20; $i++) // рисуем палочки 
		{ 
			ImageString ($graf, 0, $i*30, $visota-10, $i, $cherniy); 
			$rows = db::rows("SELECT t_sensor1 FROM reestr WHERE codeid = $i");
			$temperature[$i-1] = $value['t_sensor1'];
			$Okruglenie = round(($temperature[$i-1]*$visota)/$temperatureMax); 
			ImageFilledRectangle ($graf, $i*30-7, $visota-$Okruglenie, $i*30+7, $visota-10, $siniy); 
			ImageString ($graf, 0, $i*30-7, $visota-$Okruglenie-10, $temperature[$i-1], $cherniy); 
		}
	}
		    ImagePng ($graf); 
	


?> 






