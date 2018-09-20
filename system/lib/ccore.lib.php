<?php
/**
 * ELLY Framework
 * @version 16.09.2016
 */ 
class CCore //extends db
{       
    static private $_instance;
    
    static function getInstance()
    {
        if (null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }       
    
    /**
     * Валидация массива
     * @return array
     */
    static function validationArr($arr)
    {
        foreach ($arr as $f => $v) {    
            if(is_array($v)) $mas[$f] = self::validationArr($v);
            else $mas[$f] = ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? $v : self::validation($v);		
        }
        return $mas;
    }
    
    /**
     * Валидация глобального $_POST
     * @return array
     */
    static function validationPOST()
    {		
        return self::validationArr($_POST);
	}
    
    /**
     * Валидация глобального $_POST с возвращением объекта
     * @return object(stdClass)
     */
    static function getPOST()
    {
        $obiect = new StdClass();        
        foreach ($_POST as $k => $v) $obiect->$k = self::validation($v);
        
        return $obiect;
    }
    
    /**
     * Преобразование даты к виду дд.мм.гггг
     * @date    String дата
     * @type    String для обратного преобразования к виду гггг-мм-дд
     * @return  String
     */
    static function getFormatDate($date,$type="")
    {
        switch($type)
        {
            case "base":
                {
                    $d    = explode('.',$date);
                    $date = $d[2]."-".$d[1]."-".$d[0];
                    return $date;
                }break;
            default:
                {
                    $date = explode(" ",$date);
                    $d    = explode('-',$date[0]);
                    $date = $d[2].".".$d[1].".".$d[0]." ".$date[1];
                    return $date;
                }break;
        }
    }
    
    /**
     * Форматирование даты
     * @return  String
     */
    static function formatDate($date,$type='d.m.Y')
    {   
        return date($type,strtotime($date));
    }    
   
	/**
	 * Шифрование
     * @return  String
	 */
    static function encrypt($string, $key = KEY)
    {
        $result = '';
        for($i=0; $i<strlen($string); ++$i)
        {
            $char    = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char    = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return strtr( base64_encode( $result ), '+/=', '-.,' ); //Кодирование в base64 с заменой url-несовместимых символов
    }

	/**
	 * Дешифровывание
     * @return  String
	 */
    static function decrypt($string, $key = KEY)
    {
        $result = '';
        
        $string = base64_decode( strtr( $string, '-.,', '+/=' ) ); //Декодирование из base64 с заменой url-несовместимых символов 
        for($i=0; $i<strlen($string); ++$i)
        {
            $char    = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char    = chr(ord($char) - ord($keychar));
            $result .= $char;
        }                
        return $result;
    } 
    
    function sendEmail($email,$msg)
    {
        include_once "mail.class.php";
        $m= new Mail('UTF-8');  // можно сразу указать кодировку, можно ничего не указывать ($m= new Mail;)
        $m->From( MAIL_NAME.";".MAIL_LGN ); // от кого Можно использовать имя, отделяется точкой с запятой
        $m->To($email);   // кому, в этом поле так же разрешено указывать имя            
        $m->Subject(MAIL_NAME);
        $m->Body($msg, 'html');
        $m->Priority(4) ;	// установка приоритета
        $m->smtp_on(MAIL_SMTP, MAIL_LGN, MAIL_PSS, MAIL_PORT); // используя эу команду отправка пойдет через smtp                        
        $m->Send();
    }
    
    
    
    
        
    private function _clear()
    {
        $this->sqlArr = array();
    }
        
    function table($value)  { $this->_clear(); $this->sqlArr[__function__] = $value; return $this; }    
    function where($value)  { $this->sqlArr[__function__] = $value; return $this; } 
    function sort($value)   { $this->sqlArr[__function__] = $value; return $this; }     
    function group($value)  { $this->sqlArr[__function__] = $value; return $this; }
    function sql($sql)      { $this->sql = $sql; return $this; }
    //function limit($value)  { $this->sqlArr[__function__] = $value; return $this; } 
    function limit($start,$count)  
    { 
        $this->sqlArr[__function__ .'_start'] = (int)$start; 
        $this->sqlArr[__function__ .'_count'] = (int)$count;
        return $this; 
    }
    
    private function _select($fields)
    {
        //if(!empty($this->sql)) return $this->query($this->sql);
        if(!empty($this->sql)) return db::query($this->sql);
        
        $sql = "SELECT $fields FROM ". $this->sqlArr['table'];
        if(!empty($this->sqlArr['where'])) $sql .= ' WHERE '. $this->sqlArr['where'];
        if(!empty($this->sqlArr['group'])) $sql .= ' GROUP BY '. $this->sqlArr['group'];
        if(!empty($this->sqlArr['sort']))  $sql .= ' ORDER BY '. $this->sqlArr['sort'];         
        //if(!empty($this->sqlArr['limit_start']) && !empty($this->sqlArr['limit_count'])) $sql .= ' LIMIT '. $this->sqlArr['limit_start'] .','. $this->sqlArr['limit_count'];        
        
        if(isset($this->sqlArr['limit_start']) && !empty($this->sqlArr['limit_count']))
        {
            $where = empty($this->sqlArr['where']) ? '' : ' WHERE '. $this->sqlArr['where'];
            $and   = empty($where) ? ' WHERE ' : ' AND ';
            $atr   = '';
            if(!empty($this->sqlArr['group'])) $atr .= ' GROUP BY '. $this->sqlArr['group'];
            if(!empty($this->sqlArr['sort']))  $atr .= ' ORDER BY '. $this->sqlArr['sort'];
            
            $sql = "SELECT TOP ". $this->sqlArr['limit_count'] ." $fields FROM ". $this->sqlArr['table'] .
                   $where . $and ."codeid NOT IN( ".
                        "SELECT top ". $this->sqlArr['limit_start'] ." codeid FROM ". $this->sqlArr['table'] . $where . $atr .
                   ")". $atr;
        }
        //return $this->query($sql);
        return db::query($sql);
    }
    
    function range($fields = '')
    {        
        $fields = ($fields) ? $fields : '*';
        
        $result = array();
        if($table = $this->_select($fields))
        {
            while ($tmp = mssql_fetch_assoc($table)) {
                foreach($tmp as $key=>$val) $value[$key] = trim(iconv("cp1251","utf-8",$val)); 
                $result[] = $value;
            }          
            mssql_free_result($table);    
        }
        return $result;
    } 
    
    function row($fields = '')
    {
        $fields = ($fields) ? $fields : '*';
        
        $result = array();
        if($table = $this->_select($fields))
        {
            if($tmp = mssql_fetch_assoc($table))
            foreach($tmp as $key=>$val) $result[$key] = trim(iconv("cp1251","utf-8",$val)); 
            mssql_free_result($table);
        }        
        return $result;
    }
    
    function col($field, $key = NULL)
    {
        $result = array();
        if(is_null($key)) {
            if($table = $this->_select($field))
                while ($tmp = mssql_fetch_assoc($table)) $result[] = trim(iconv("cp1251","utf-8",$tmp[$field]));
            }
        else {
            if($table = $this->_select($field .','. $key))
                while ($tmp = mssql_fetch_assoc($table)) $result[$tmp{$key}] = trim(iconv("cp1251","utf-8",$tmp[$field]));
            }                  
        if($table) mssql_free_result($table);
        return $result;
    }
    
    function cell($field)
    {
        $result = '';
        if($table = $this->_select($field))
        {
            if (mssql_num_rows($table)>0) $result = mssql_result($table,0,0);
            mssql_free_result($table);    
        }        
        return trim(iconv("cp1251","utf-8",$result));  
    }
    
    function add($data)
    {           
        $fields = '';
        $values = '';
        $data   = $this->_security($this->sqlArr['table'], $data);
        foreach ($data as $f => $v)
        {
            //if(is_string($v)) $v = iconv("utf-8", "cp1251", $v);
            $fields .= "[$f],";
            $values .= "'$v',";
        }
        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);
        $insert = "INSERT INTO ". $this->sqlArr['table'] ." ({$fields}) VALUES({$values})";
        
        if(db::query($insert) > 0) return $this->mssql_insert_id();
        else return 0;
    }
    
    function mssql_insert_id() { 
        $id = 0; 
        $res = mssql_query("SELECT @@identity AS id"); 
        if ($row = mssql_fetch_array($res, MSSQL_ASSOC)) { 
            $id = $row["id"]; 
        } 
        return $id; 
    } 
    
    function delete()
    {
        $sql = "DELETE FROM ". $this->sqlArr['table'];
        $sql = empty($this->sqlArr['where']) ? $sql : $sql .' WHERE '.    $this->sqlArr['where'];
        $sql = empty($this->sqlArr['group']) ? $sql : $sql .' GROUP BY '. $this->sqlArr['group'];
        $sql = empty($this->sqlArr['sort'])  ? $sql : $sql .' ORDER BY '. $this->sqlArr['sort'];        
        $sql = empty($this->sqlArr['limit']) ? $sql : $sql .' LIMIT '.    $this->sqlArr['limit'];
        
        //return $this->query($sql);
        return db::query($sql);
    }
    
    function update($data)
    {
        $sql  = "UPDATE ". $this->sqlArr['table'] ." SET ";
        $data = $this->_security($this->sqlArr['table'], $data);
        foreach( $data as $field => $value ) $sql .= "[".$field."]='$value',";        
        $sql = substr($sql, 0, -1);
        $sql = empty($this->sqlArr['where']) ? $sql : $sql .' WHERE '. $this->sqlArr['where'];
        
        //return $this->query($sql);
        return db::query($sql);
    }
    
    /**
     * Валидация переменной
     * @return String 
     */
    function validation($data)
    {           
        if(get_magic_quotes_gpc()) $data = stripslashes($data);
        return str_replace("'", "''", $data);      
    }
        
    function _security($table, $array)
    {        
        if(!empty($this->tables[$table]))
        {
            foreach($array as $k => $v)
            {
                $typeField = $this->tables[$table][$k];
                switch($typeField)
                {
                    case 'INT':
                    case 'TINYINT':
                    case 'BIGINT':
                        $array[$k] = (int)$v;
                        break;
                    case 'FLOAT':
                    case 'DOABLE':
                        $array[$k] = (double)$v;
                        break;
                    case 'CHAR':
                    case 'VARCHAR':
                        $quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!" );
                        $v = trim( strip_tags($v) );
                        $v = str_replace( $quotes, '', $v );                        
                        $array[$k] = $v ;//mysql_real_escape_string($v);
                        break;
                    case 'TEXT':
                        $search = array(
                                            '@<script[^>]*?>.*?</script>@si',   // javascript
                                            '@<[\/\!]*?[^<>]*?>@si',            // HTML теги
                                            '@<style[^>]*?>.*?</style>@siU'     // теги style
                                        );                        
                        $array[$k] = mysql_real_escape_string(preg_replace($search, '', $v));
                        break;
                    case 'DATE':
                        if(!empty($v)) $array[$k] = date('Y-m-d', strtotime($v));
                        break;
                    case 'TIME':
                        if(!empty($v)) $array[$k] = date('H:i:s', strtotime($v));
                        break;
                    case 'DATETIME':
                        if(!empty($v)) $array[$k] = date('Y-m-d H:i:s', strtotime($v));
                        break;
                }
            }
        }
        return $array;
    }   
    
    
    
    /**
     * Вызов хранимой процедуры MSSQL через EXEC
     * @name   String название процедуры
     * @data   array значения в виде field => value
     * @out    array выходные параметры (при необходимости), название переменной => тип данных
     * @return mixed
     */
    function exec( $name, $data, $out=false )
    {
        $sql = array();
        foreach($data as $field => $value)
        {
            $value = $this->validation($value);
            $value = (substr($value, 0, 2) == '0x' || $value == 'NULL')? $value : "'$value'";
            $sql[] = '@'. $this->validation($field) ."=". $value;
        } 
        $exec = "EXEC $name ". implode(',', $sql);
        //$exec = iconv("utf-8","cp1251",$exec);

        if(is_array($out))
        {
            foreach($out as $name=>$type) {
                $exec_declare[] = '@'. $name .' '. $type;
                $exec_select[]  = '@'. $name .' AS '. $name;
                $exec .= ", @$name=@$name OUTPUT";
            }
            $exec = "DECLARE ". implode(',', $exec_declare) ."; ". $exec ."; SELECT ". implode(',', $exec_select) .";";  
        } 
        //die($exec);
        //$table = $this->query($exec);
        $table = db::query($exec);
        if(is_array($out))
        {
            $result = mssql_fetch_assoc($table);
            foreach($result as $key=>$val) $result[$key] = iconv("cp1251","utf-8",$val); 
            mssql_free_result($table);
        }
        return $result;   
    }
    
    function _securityField($v, $typeField, $hard = false)
    {
        switch($typeField)
        {
            case 'INT':
            case 'TINYINT':
            case 'BIGINT':
                $result = (int)$v;
                break;
            case 'FLOAT':
            case 'DOABLE':
                $result = (double)$v;
                break;
            case 'CHAR':
            case 'VARCHAR':
                if($hard) {                    
                    $v = trim(strip_tags($v));
                    $v = str_replace( array("\x27","\x22","\x60","\t","\n","\r","*","%","<",">","?","!" ), '', $v );
                }                        
                $result = $v;//mysql_real_escape_string($v);
                break;
            case 'TEXT':
                if($hard) $v = preg_replace(array(
                                                    '@<script[^>]*?>.*?</script>@si',   // javascript
                                                    /*'@<[\/\!]*?[^<>]*?>@si',*/            // HTML теги
                                                    '@<style[^>]*?>.*?</style>@siU'     // теги style
                                                ), '', $v);
                                   
                $result = $v; //mysql_real_escape_string($v);
                break;
            case 'DATE':
                if(!empty($v)) $result = date('Y-m-d', strtotime($v));
                break;
            case 'TIME':
                if(!empty($v)) $result = date('H:i:s', strtotime($v));
                break;
            case 'DATETIME':
                if(!empty($v)) $result = date('Y-m-d H:i:s', strtotime($v));
                break;
        }
        
        return $result;
    }
    
    /**
     * Эксперементальная функция подготавливающая данные для отдачи в JSON 
     */
    function toJson($data, $tableName)
    {       
        /*
        $text   = array('CHAR', 'VARCHAR', 'NVARCHAR', 'TEXT');
        $date   = array('DATETIME', 'DATE', 'TIME');
        */
        if(is_array($data))  
        foreach($data as $k => $v)
        {
            if(is_array($v)) $data[$k] = $this->toJson($v, $tableName);
            else
            {
                $type = $this->tables[$tableName][$k];
                
                if(empty($type)) if(is_numeric($v)) $data[$k] = floatval($v);                
                if(in_array($type, array('INT', 'TINYINT', 'BIGINT'))) $data[$k] = intval($v);
                elseif(in_array($type, array('FLOAT', 'DOUBLE', 'MONEY'))) $data[$k] = floatval($v);
            }
                       
        }
        return $data;
    }

}
?>