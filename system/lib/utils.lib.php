<?php
class utils {

	/** Экспорт в эксель.
	* Функция просто добавляет заголовки, необходимые для скачивания файла, печатает $html и делает exit()
	* Пример использования:
		$users = user::find();
		$fields = array(
			'fio'=>'ФИО',
			'phone'=>'Номер телефона',
			'email'=>'Электронный адрес',
		);
		export_excel($fields, $users)
	*/
	public static function export_excel($fields, $data, $filename='') {

		$table = '<table><tr>';

		foreach($fields as $field){
			$table .= '<td>'.iconv('utf-8','cp1251',$field).'</td>';
		}

		$table .= '</tr>';

		foreach($data as $item){
			$table .= '<tr>';

			if(is_array($item)){
				foreach($fields as $key => $field){
					$table .= '<td style="mso-number-format:\'\@\'">'.iconv('utf-8','cp1251',$item[$key]).'</td>';
				}
			} else {
				foreach($fields as $key => $field){
					$table .= '<td style="mso-number-format:\'\@\'">'.iconv('utf-8','cp1251',$item->$key).'</td>';
				}
			}

			$table .= '</tr>';
		}

		$table .= '</table>';

		$filename = ( $filename ) ? str_replace(' ', '-', $filename).'.xls' : date('d-m-YY') . '.xls';

		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Type: application/vnd.ms-excel; format=attachment;');
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-Transfer-Encoding: binary");

		print '<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="TEXT/HTML; CHARSET=WINDOWS-1251">';
		print $table;

		exit();
	}

	/** Экспорт в ворд.
	* Функция просто добавляет заголовки, необходимые для скачивания файла, печатает $html и делает exit()
	* Пример использования:
		export_word('<h1>Привет Атай</h1>', $users);
	*/
	public static function export_word($text, $filename='') {

		$word_xmlns = "<xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>";
		$word_xml_settings = "<xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml>";
		$word_landscape_style = "@page {size:8.5in 11.0in; margin:0.5in 0.31in 0.42in 0.25in;} div.Section1{page:Section1;}";
		$word_landscape_div_start = "<div class='Section1'>";
		$word_landscape_div_end = "</div>";

		$filename = ( $filename ) ? str_replace(' ', '-', $filename).'.doc' : date('d-m-Y') . '.doc';

		$content = '
		<html '.$word_xmlns.'>
		<head>'.$word_xml_settings.'<style type="text/css">
		'.$word_landscape_style.' table,td {border:0px solid #FFFFFF;}</style>
		</head>
		<body>'.$word_landscape_div_start.$text.$word_landscape_div_end.'</body>
		</html>
		';

		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Type: application/msword; format=attachment;');
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-Transfer-Encoding: binary");
		header('Content-Length: ' . strlen($content));

		print '<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="TEXT/HTML; CHARSET=UTF-8">';
		print $content;

		exit();
	}

}