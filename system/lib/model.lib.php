<?php
class model {

	/**
	 * Array with data of model instance (field values of table row)
	 * @var array
	*/
	protected $_rowData = array();

	/**
	 * Class name of model
	 * @var string
	*/
	protected static $_modelName = '';

	/**
	 * Table name in db for this model
	 * @var string
	*/
	protected static $_tableName = '';

	/**
	 * Array with table structure.
	 * @var array
	*/
	protected static $_tableStructure = array();

	/**
	 * Field name of identity model
	 * @var string
	*/
	protected static $_rowIdentity = '';

	/**
	 * Identity of current model
	 * @var int
	*/
	protected $_identity = 0;

	/**
	 * Turn on or off APC caching for current model
	 * @var bool
	*/
	protected static $_caching = true;

	/**
	 * Array fields of type date, datetime or time
	 * @var array
	*/
	protected static $_dateFields = array();

	/**
	 * Init model vars for static functions like get(), find()
	 * @return void
	*/
	public static function __init() {

			// get current model class name
		static::$_modelName = get_called_class();
		static::$_tableStructure = array();
		static::$_rowIdentity = '';

		if ( static::$_tableName!=='' ) {
		if ( false === ($fields_info = cache::get(static::$_modelName, 'db_schema')) ) {
			$fields_info = db::table_structure(static::$_tableName);
            $fields_info['primary']['name'] = 'codeid';//если используем вид
			if ( empty($fields_info['primary']['name']) ) {
					print_arr('Фатальная ошибка: Не определен первичный ключ для таблицы "'.static::$_tableName.'" в модели "'.get_called_class().'"', 1);
				} elseif ( empty($fields_info['fields']) ) {
					print_arr('Фатальная ошибка: Не удалось определить структуру таблицы "'.static::$_tableName.'" в модели "'.get_called_class().'". Проверьте существует ли такая таблица, и есть ли у нее поля.', 1);
				} else {
					cache::set(static::$_modelName, 'db_schema', $fields_info);
				}
			}

			static::$_tableStructure = $fields_info['fields'];
			static::$_rowIdentity = $fields_info['primary'];

			foreach (static::$_tableStructure as $field_name=>$field_type) {
				if ( $field_type!=='datetime' && $field_type!=='date' && $field_type!=='time' ) continue;
				static::$_dateFields[] = $field_name;
			}
		}
	}

	/**
	 * Get single model by identity or by $fieldname
	 *
	 * @param  integer
	 * @return mixed Model object or false if not founded
	*/
	public static function get($identity, $fieldname='') {
		$fieldname = ( !empty($fieldname) ) ? $fieldname : static::getIdentityName();

		$query = 'SELECT * FROM '.static::getTableName().' WHERE '.$fieldname."='".db::real_escape($identity)."' ";
        //print_r($query);
		$model_list = static::getModelList($query);
		$model = ( $model_list ) ? array_pop($model_list) : false;

		return $model;
	}


	/**
	 * Fetches model objects from table by given params
	 * @return array
	*/
	public static function find() {
		$args = static::prepare_args(func_get_args());

		$args['where'] = ( !empty($args['where']) ) ? ' WHERE ' . $args['where'] : '';
		$args['order'] = ( !empty($args['order']) ) ? $args['order'] : self::getIdentityName();

		$query = static::query($args['where'], $args['order'], $args['offset'], $args['limit']);
        //print_r('<pre>'.$query.'</pre>');
		return static::getModelList($query);
	}


	public static function query($where, $order, $offset, $limit) {
		return ( !$offset || !$limit )
			? 'SELECT * FROM ' . static::getTableName() . $where . ' ORDER BY ' . $order

			: 'SELECT * FROM ( SELECT *, ROW_NUMBER() OVER (ORDER BY '.self::getIdentityName().' DESC) AS RowNum FROM ' . static::getTableName() . ' ) '
					. $where . ' ORDER BY ' . $order . '
				WHERE RowNum BETWEEN ' . ($offset + 1) . ' AND ' . ($offset + $limit) . ' ORDER BY RowNum ASC'
			;
	}

	/**
	 * Insert new row in table with current model info
	 * @return bool
	*/
	public function create() {
		$data = $this->toArray();

		if ( isset($data[$this->getIdentityName()]) && static::isIdentityAutoincrement() ) {
			unset($data[$this->getIdentityName()]);
		}

		$result = db::insert(static::getTableName(), $data, static::$_tableStructure);

		if ( $result ) {
			if ( static::isIdentityAutoincrement() ) {
				$this->setId(db::insert_id());
			} elseif ( isset($data[$this->getIdentityName()]) ) {
				$this->setId($data[$this->getIdentityName()]);
			}

			if ( static::$_caching ) {
				cache::clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(static::$_modelName.'_find'));
				cache::set(static::$_modelName, $this->getId(), $this, array(static::$_modelName));
			}

			return true;
		}

		return false;
	}

	/**
	 * Update existing row in table with current model info
	 * @return bool
	*/
	public function update() {
		$data = $this->toArray();
		if ( isset($data[$this->getIdentityName()]) && static::isIdentityAutoincrement() ) {
			unset($data[$this->getIdentityName()]);
		}

		$result = db::update(static::getTableName(), $data, static::getIdentityName()."='".$this->getId()."'", static::$_tableStructure);
		if ( $result ) {
			if ( static::$_caching ) {
				cache::clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(static::$_modelName.'_find'));
				cache::set(static::$_modelName, $this->getId(), $this, array(static::$_modelName));
			}
		}

		return $result;
	}

	/**
	 * Delete row from table
	 * @return bool
	*/
	public function delete() {
		cache::clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(static::$_modelName.'_find'));
		return db::query('DELETE FROM '.static::getTableName().' WHERE '.static::getIdentityName()."='".$this->getId()."'");
	}

	/**
	 * Current model identity
	 * @return integer
	*/
	public function getId() {
		return $this->_identity;
	}

	/**
	 * Sets identity for current model
	*/
	public function setId($identity) {
		$this->_identity = $identity;
		$this->_rowData[$this->getIdentityName()] = $identity;
	}

	/**
	 * Set model info (table row fields) from array
	*/
	public function setFromArray($data) {
		foreach(static::$_tableStructure as $field=>$type) {
			if ( !isset($data[$field]) ) continue;

				$this->_rowData[$field] = $this->formatValue($type, $data[$field]);
			}

		if ( isset($this->_rowData[$this->getIdentityName()]) ) {
			$this->setId($this->_rowData[$this->getIdentityName()]);
		}
	}

	public function formatValue($type, $value) {

		if ( $value===' ' || $value==='' ) {
			$formatedValue='';
		} elseif ( $type=='bit' ) {
			if ( $value==='false' || $value==='off' ) {
				$formatedValue = 0;
			} elseif ( strtolower($value)==='true' || (bool)$value ) {
				$formatedValue = 1;
			} else {
				$formatedValue = 0;
			}
		} elseif ( $type=='int' || $type=='tinyint' ) {
			$formatedValue = ( !empty($value) ) ? (int)$value : 0;
		} elseif ( $type=='float' || $type=='money' || $type=='bigint' ) {
			$formatedValue = ( !empty($value) ) ? (float)$value : 0;
		} elseif ( $type=='datetime' || $type=='date' || $type=='time' ) {
			if ( empty($value) ) {
				$formatedValue = '';
			} elseif ( is_object($value) ) {
				$formatedValue = $value;
			} elseif ( is_numeric($value) ) {
				$formatedValue = new DateTime(date('d.m.Y H:i:s', $value));
			} else {
				$formatedValue = new DateTime($value);
			}
		} elseif ( is_a($value, 'file') ) {
			$formatedValue = $value;
		} elseif ( is_array($value) ) {
			$formatedValue = $value;
		} else {
				// если переменная - json строка, то раскодируем в массив
			if ( (mb_substr($value, 0, 1)=='{' || mb_substr($value, 0, 1)=='[') && false!==($decoded_value = json_decode($value, true)) ) {
				$formatedValue = $decoded_value;
				if ( isset($formatedValue['path']) ) {
					$formatedValue = new file($formatedValue);
				}
			} else {
				$formatedValue = ( !empty($value) ) ? $value : '';
			}
		}

		return $formatedValue;
	}

	/**
	 * Get model info (db row fields) as an array
	 * @return	array
	*/
	public function toArray() {
		$data = $this->_rowData;
		return $data;
	}

	/**
	 * Get model info (db row fields) as an array
	 * @return	array
	*/
	public function toJsonArray() {

		$data = $this->toArray();

			// переводим поля даты в таймстамп для js, или ансетим, если пустые
		if ( !empty(static::$_dateFields) ) {
			foreach(static::$_dateFields as $name) {
				if ( isset($data[$name]) ) {
					if ( $data[$name] ) {
						$data[$name] = $data[$name]->getTimestamp();
					} else {
						unset($data[$name]);
					}
				}
			}
		}

		return $data;
	}


	/**
	 * Getter returns identity, field from db row or model variable
	 * @param string field name from table
	 * @return mixed
	*/
	public function __get($varname) {
		return ( isset($this->_rowData[$varname]) ) ? $this->_rowData[$varname] : '';
	}

	/**
	 * Setter for model variables (field row or model variable)
	*/
	public function __set($varname, $value) {
		if ( isset(static::$_tableStructure[$varname]) ) {
			$this->_rowData[$varname] = $this->formatValue(static::$_tableStructure[$varname], $value);
		} else {
				//TODO: throw exeption if try to set not declared variable in model
			print_arr('Фатальная ошибка: Попытка присвоить объекту "'.static::$_modelName.'" не объявленную переменную "'.$varname.'" в этом объекте', 1);
		}
	}



	public static function prepare_args($args = array()) {
		$aditional_args = array();
		$placeholder_args = array();

		foreach ($args as $key=>$value) {
			if ( is_array($value) ) {
				$aditional_args = $value;
			} else {
				$placeholder_args[] = $value;
			}
		}
		$where = db::placeholder($placeholder_args);

		return array(
			'where'=>$where,
			'order'=>( isset($aditional_args['order']) ? $aditional_args['order'] : NULL),
			'offset'=>( isset($aditional_args['offset']) ? $aditional_args['offset'] : NULL),
			'limit'=>( isset($aditional_args['limit']) ? $aditional_args['limit'] : NULL),
		);
	}


	public static function getModelList($query) {

		if ( false === static::$_caching || false === ($model_list = cache::get(static::$_modelName, $query)) ) {
			$rowset = db::rows($query);
			$model_list = array();
			if ( $rowset ) {
				foreach($rowset as $data) {
					$model = new static::$_modelName;
					$model->setFromArray($data);
					$model_list[$model->getId()] = $model;
				}
			if ( static::$_caching ) {
				cache::set(static::$_modelName, $query, $model_list, array(static::$_modelName.'_find'));
			}
		}
		}

		return $model_list;
	}

	public static function count($where='') {

		$where = ( !empty($where) ) ? ' WHERE ' . $where : '';
		$query = 'SELECT COUNT(*) FROM ' . static::getTableName() . $where;

		if ( false === static::$_caching || false === ($count = cache::get(static::$_modelName, $query)) ) {
			$count = db::field($query);
			if ( false === $count && static::$_caching ) {
				cache::set(static::$_modelName, $query, $count, array(static::$_modelName.'_find'));
			}
		}

		return $count;
	}

	/**
	 * Current model table name
	 * @return string
	*/
	public static function getTableName() {
		return static::$_tableName;
	}

	/**
	 * Current model table name
	 * @return string
	*/
	public static function setTableName($tableName) {
		static::$_tableName = $tableName;
	}

	/**
	 * Current model identity field name
	 * @return string
	*/
	public static function getIdentityName() {
		return static::$_rowIdentity['name'];
	}

	/**
	 * Current model identity field name
	 * @return string
	*/
	public static function isIdentityAutoincrement() {
		return static::$_rowIdentity['auto_increment'];
	}

	/**
	 * return true is given field name is date type
	 * @return bool
	*/
	public static function isDateField($field) {
		return isset(static::$_tableStructure[$field]) && ( static::$_tableStructure[$field]=='datetime' || static::$_tableStructure[$field]=='time' || static::$_tableStructure[$field]=='date' );
	}

}