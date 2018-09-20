
<?php 
class imanController extends controller {
	
public function index() {

    $shirina = 700; 
    $visota = 150; 
    $graf = ImageCreate ($shirina, $visota);       
    $beliy = ImageColorAllocate ($graf, 255, 255, 255);
    $cherniy = ImageColorAllocate ($graf, 0, 0, 0);
    $siniy = ImageColorAllocate ($graf, 0, 0, 255);   

// проводим горизонтальную линию, ось абсцисс (время)
	ImageLine ($graf, 10, $visota-10, $shirina-10, $visota-10, $cherniy); 
// проводим вертикальную линию, чтобы ось ординат (число посещений) 
	ImageLine ($graf, 10, 10, 10, $visota-10, $cherniy); 

// максимум  
    $temperatureMax = 200; 
      
// рисуем палочки 
    for ($i=1; $i<=20; $i++) 
	{ 
		ImageString ($graf, 0, $i*30, $visota-10, $i, $cherniy); 
		$temperature[$i-1] = rand(20,100);
        $Okruglenie = round(($temperature[$i-1]*$visota)/$temperatureMax); 
        ImageFilledRectangle ($graf, $i*30-7, $visota-$Okruglenie, $i*30+7, $visota-10, $siniy); 
        ImageString ($graf, 0, $i*30-7, $visota-$Okruglenie-10, $temperature[$i-1], $cherniy); 
    }
      
		imagePNG($graf);
        return array(
           'graf'=>$graf
        );	
		header("Content-type: image/gif");
}
}

?> 








