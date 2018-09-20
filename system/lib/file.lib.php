<?php
class file {

	/**
	 * Полный путь до файла
	 * @var string
	*/
	public $path = '';

    public $preview = '';

	/**
	 * Директория расположения файла (относительно константы $base_dir)
	 * @var string
	*/
	public $dir = '';

	/**
	 * Имя файла
	 * @var string
	*/
	public $name = '';

	/**
	 * Расширение файла
	 * @var string
	*/
	private $ext = '';

	/**
	 * Размер файла
	 * @var string
	*/
	private $size = '';

	/**
	 * Тип файла
	 * @var string
	*/
	private $type = '';

	/**
	 * Оригинальное имя файла
	 * @var string
	*/
	public $original_name = '';

	/**
	 * Временное расположение файла (если загружен из формы)
	 * @var string
	*/
	private $tmp_name = '';

	/**
	 * Здесь сохраняется номер ошибки
	 * @var integer
	*/
	private $error = 0;

	/**
	 * Корневая директория для файлов
	 * @var string
	*/
	private $base_dir = '/public/';

	/**
	 * Список возможных ошибок
	 * @var array
	*/
	static $error_codes = array(
		0 => 'Ошибки нет',
		1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
		2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		3 => 'The uploaded file was only partially uploaded',
		4 => 'No file was uploaded',
		6 => 'Missing a temporary folder',
		10 => 'Ошибка файл не загружен на сервер',
		11 => 'Missing a temporary folder',
		12 => 'Не удалось создать директорию для сохранения файла (путь не существует)',
		13 => 'Нет прав на запись в директорию',
		14 => 'Файл не загружен на сервер (move_uploaded_file не смог переписать файл)',
		15 => 'Файл не был загружен (переменная tmp_name в $_FILES пустая)',
	);


	public function __construct( $file = null ) {

			// если передали модель файла
		if ( gettype($file)=='object' && is_subclass_of($file, 'model') ) {

			$this->original_name = $file->original_name;
			$this->name = $file->name;
			$this->ext = $file->ext;
			$this->dir = $file->dir;
			$this->path = $file->path;
            $this->preview = $file->preview;
			$this->size = $file->size;
			$this->type = $file->type;
			return;

		}

			// если передали массив из $_FILES
		if ( gettype($file)=='array' && isset($file['error']) ) {

			if ( $file['error'] !== UPLOAD_ERR_OK ) {

				$this->error = $file['error'];
				return;

			} else {

					// получаем оригинальное название файла и расширение
				$position = mb_strrpos($file['name'], '.');
				$this->original_name = $file['name'];
				$this->ext = mb_substr($file['name'], $position+1);

					// генерируем название файла под которым будет хранится на сервере
				$this->name = uniqid();
				$this->tmp_name = $file['tmp_name'];
				$this->type = $file['type'];
				$this->size = $file['size'];
				return;

			}
		}

			// если передали массив с подготовленной информацией о файле
		if ( gettype($file)=='array' && !isset($file['error']) ) {
			$this->original_name = ( isset($file['original_name']) ? $file['original_name'] : '');
			$this->name = ( isset($file['name']) ? $file['name'] : '');
			$this->ext = ( isset($file['ext']) ? $file['ext'] : '');
			$this->dir = ( isset($file['dir']) ? $file['dir'] : '');
			$this->path = ( isset($file['path']) ? $file['path'] : '');
            $this->preview = ( isset($file['preview']) ? $file['preview'] : '');
			$this->size = ( isset($file['size']) ? $file['size'] : '');
			$this->type = ( isset($file['type']) ? $file['type'] : '');
			return;
		}

			// если передали json строку
		if ( gettype($file)=='string' ) {
			$file = json_decode($file, true);

			if ( !$file ) {
				return;
			}

			$this->original_name = ( isset($file['original_name']) ? $file['original_name'] : '');
			$this->name = ( isset($file['name']) ? $file['name'] : '');
			$this->ext = ( isset($file['ext']) ? $file['ext'] : '');
			$this->dir = ( isset($file['dir']) ? $file['dir'] : '');
			$this->path = ( isset($file['path']) ? $file['path'] : '');
            $this->preview = ( isset($file['preview']) ? $file['preview'] : '');
			$this->size = ( isset($file['size']) ? $file['size'] : '');
			$this->type = ( isset($file['type']) ? $file['type'] : '');
		}

	}

	/**
	 * Сохранить файл в папку с полным путем до файла
	 * @var $dir string - директория сохранения файла (относительно $this->base_dir)
	 * @return bool
	*/
	public function upload($dir='') {

		if ( $this->error!=0 ) {
			return false;
		}

			// если нету переменной tmp_name значит файл не загружен
		if ( empty($this->tmp_name) ) {
			$this->error = 15;
			return false;
		}

			// обрезаем начальные и конечные слеши из названия директории
		$this->dir = trim($dir, '/\\');

			// сохраняем полный путь до файла
		$this->path = $this->base_dir . $this->dir . '/' . $this->name . '.' . $this->ext;
        ;

			// если указанная директория не существует, то пытаемся создать
		if ( !is_dir(SITE_ROOT . $this->base_dir . $this->dir) ) {
			if ( !mkdir(SITE_ROOT . $this->base_dir . $this->dir) ) {
				$this->error = 12;
				return false;
			}
			chmod(SITE_ROOT . $this->base_dir . $this->dir, 0777);
		}

		if ( !move_uploaded_file($this->tmp_name, SITE_ROOT . $this->path) ) {
			$this->error = 14;
			return false;
		}


		return true;
	}

	/**
	 * Удалить файл по пути, сохраненном в $path
	 * @return bool
	*/
	public function delete() {
		if ( is_file(SITE_ROOT . $this->path) ) {
			if ( unlink(SITE_ROOT . $this->path) ) {
				return true;
			} else {
				return false;
			}
		}

	}

	/**
	 * Информация о файле в виде массива
	 * @return	array
	*/
	public function toArray() {
		if ( empty($this->path) ) {
			return NULL;
		}

		return array(
			'path'=>$this->path,
			//'dir'=>$this->dir,
			//'name'=>$this->name,
			// 'ext'=>$this->ext,
			// 'size'=>$this->size,
			// 'type'=>$this->type,
			// 'original_name'=>$this->original_name,
		);
	}

	/**
	 * Информация о файле в виде json
	 * @return	string
	*/
	public function toJson() {
		return json_encode(
			array('path'=>$this->path, 'original_name'=>$this->original_name)
		);
	}

	public function getErrorCode() {
		return $this->error;
	}

	public function checkExt($ext) {
		$ext_array = explode(',', $ext);

		return ( in_array($this->ext, $ext_array) );
	}

	public function getErrorMessage() {
		return self::$error_codes[$this->error];
	}
    
    public static function reArrayFiles($file)
    {
        $file_ary = array();
        $file_count = count($file['name']);
        $file_key = array_keys($file);
        
        for($i=0;$i<$file_count;$i++)
        {
            foreach($file_key as $val)
            {
                $file_ary[$i][$val] = $file[$val][$i];
            }
        }
        return $file_ary;
    }
}