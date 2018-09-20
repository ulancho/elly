<?
/**
 * ELLY Framework
 * @version 16.09.2016
 */ 
class CTheme 
{
    private $varsType   = '';
    protected $varsArr    = array();
    private $tableName;                 // хранилище названий таблиц для циклов
    
    static protected $_instance;    
        
    
    static function &getInstance()
    {
        if (null === self::$_instance) self::$_instance = new static();
        return self::$_instance;
    }

    
    function __set($name, $value)
    {
        if(is_bool($this->varsType)) //условные блоки
        {
            if($this->varsType){
                $this->varsArr['tag']['['.  $name .']'] = '';
                $this->varsArr['tag']['[/'. $name .']'] = '';
            }
            else $this->varsArr['regx']["'\\[$name\\].*?\\[/$name\\]'si"] = "";
            $this->varsType = 'tag';
        }
        else switch($this->varsType)
        {
            case '':
            case 'tag' : $this->varsArr['tag'][$name]  = $value; break;
            case 'post': $this->varsArr['post'][$name] = $value; break;
            case 'php' : $this->varsArr['php'][$name]  = $value; break;
        }
    }
    function __get($name)
    {
        switch($this->varsType)
        {
            case '':
            case 'tag' : return $this->varsArr['tag'][$name];
            case 'post': return $this->varsArr['post'][$name];
            case 'php' : return $this->varsArr['php'][$name];
        }
        return false;
    }

    function block($clear = false)
    {
        $this->varsType = (bool)$clear;
        return $this;
    }
    function post($arr = NULL)
    {
        if(is_null($arr)) $this->varsType = 'post';
        else $this->varsArr['post'] = $arr;
        return $this;
    }
    function php($arr = NULL)
    {
        if(is_null($arr)) $this->varsType = 'php';
        else 
        {
            if(is_object($arr))
                 $this->varsArr['php'] = $arr->table;
            else $this->varsArr['php'] = $arr;            
        }
        return $this;
    }
    function table($name, $arr = array(), $params = NULL)
    {
        if(!is_null($params))
        $this->varsArr['for'][$name] = $params;
        $this->varsArr['for'][$name]['data'] = $arr;
        $this->tableName = $name;
        return $this;
    }
    function tableSetDate($field, $format = 'd.m.Y')
    {
        $this->varsArr['for'][$this->tableName]['types'][$field]  = 'date';
        $this->varsArr['for'][$this->tableName]['params'][$field] = $format;
        return $this;
    }
    function tableSetCheck($field)
    {
        $this->varsArr['for'][$this->tableName]['types'][$field] = 'check';
        return $this;
    }
    
    
    function parse($fileName) 
    {   
		$tpl = file_get_contents($fileName);
        /*
        if(isset($this->varsArr['php']))
        {
            extract($this->varsArr['php'], EXTR_SKIP); */
        	ob_start();
            $tpl = (eval(' ?>'.$tpl.'<?php '));
            $tpl = ob_get_clean();    
        //}

        // Замена своих переменных
		if (isset($this->varsArr['tag']) && is_array($this->varsArr['tag'])) {
			foreach ($this->varsArr['tag'] as $id => $var) {
				if (substr($id,0,1) == '[') {
					$tpl = str_replace($id, $var, $tpl);
				}
				else {
					$tpl = str_replace('{'.$id.'}', $var, $tpl);
				}
			}
		}
        
        // Построение таблиц
        if(isset($this->varsArr['for']) && count($this->varsArr['for']) > 0)
        if (preg_match_all('/\[for=(.+?)\](.+?)\[\/for\]/is', $tpl, $parr)) {  
            
            //echo '<pre>'.print_r($this->varsArr['for'] ,1).'</pre>';
            foreach ($parr[1] as $k => $v) // переменные, содержащие названия Таблиц
            { 
                $html = '';
                if(isset($this->varsArr['for'][$v]))
                {
                    $types  = isset($this->varsArr['for'][$v]['types'])  ? $this->varsArr['for'][$v]['types'] : array();
                    $params = isset($this->varsArr['for'][$v]['params']) ? $this->varsArr['for'][$v]['params']: array();
                    
                    if(is_array($this->varsArr['for'][$v]['data']))
                    foreach($this->varsArr['for'][$v]['data'] as $for)
                    {
                        $block = $parr[2][$k];                            
                        foreach($for as $x => $r)
                        {
                            if(isset($types[$x]) && $types[$x] == 'date') 
                            {
                                if(!is_numeric($r)) $r = strtotime($r);
                                $r = date($params[$x], $r);
                            }
                            elseif(isset($types[$x]) && $types[$x] == 'check' && $r) $block = str_replace('name="'.$x.'[]"', 'name="'.$x.'[]" checked', $block);
                            $block = str_replace('{'.$v.'.'.$x.'}', $r, $block);
                        }
                        $html .= $block;
                    }                    
                } 
                $tpl = str_replace($parr[0][$k], $html, $tpl);                   
            }
        }
        
        // Авто подстановка в инпуты значений из переданного массива $this->varsArr['post']
        // {val=nameid}
        if(isset($this->varsArr['post']) && count($this->varsArr['post']) > 0)
        if (preg_match_all('/\{val=(.*?)\}/i', $tpl, $parr)) {
            foreach ($parr[1] as $k => $v)
            {
                if(isset($this->varsArr['post'][$v])) 
                     $tpl = str_replace($parr[0][$k], $this->varsArr['post'][$v], $tpl); //CCore::validation()
                else $tpl = str_replace($parr[0][$k], '', $tpl); 
            }
        }
        
        /*
        // Замена конструкций вида {url=par1+par2} на шифрованные ссылки
        if (preg_match_all('/\{url=(.*?)\}/i', $tpl, $parr)) {
            foreach ($parr[0] as $k => $v)
            {
                $arr = explode('+', $parr[1][$k]);
                if(isset($arr[0])) $link  = 'module='.$arr[0];  else continue;
                if(isset($arr[1])) $link .= '&action='.$arr[1];
                if(isset($arr[2])) 
                {
                    for($i=2; $i<count($arr); $i++) 
                    if(strpos($arr[$i],'=')) $link .= '&'.$arr[$i];
                }
                $tpl = str_replace($v, CCore::encrypt($link), $tpl);
			}
		}            
        
        // Вывод плагинов {plugin=name}
        if (preg_match_all('/\{plugin=(.*?)\}/i', $tpl, $parr)) {
            
            global $map;
            
            $run = Run::getInstance();
            
            foreach ($parr[0] as $k => $v)
            {
                $arr = explode('+', $parr[1][$k]);
                if(empty($arr[0])) continue;
                if(isset($arr[1])) //$map['action'] = $arr[1];
                {
                    $plugin = $run->router($arr[0], $arr[1], array(), 2);
                    $tpl = str_replace($v, $plugin, $tpl); 
                }
			}
		}*/
        
        if(isset($this->varsArr['post']) && count($this->varsArr['post']) > 0)
        {
            // Авто выделение выбранного радио-баттона       
            if (preg_match_all('/\[radio=(.+?)\](.+?)\[\/radio\]/is', $tpl, $parr)) {            
                foreach ($parr[1] as $k => $v) // переменные, содержащие названия POST'ов
                {
                    if(isset($this->varsArr['post'][$v]))
                    {
                        $post = CCore::validation($this->varsArr['post'][$v]);
                        //$parr[2][$k] - html код, выхваченный регуляркой
                        if(preg_match_all('/value="(.*?)"/is', $parr[2][$k], $larr))
                            foreach($larr[1] as $x => $r)
                            if($r == $post) $parr[2][$k] = str_replace($larr[0][$x], $larr[0][$x].' checked', $parr[2][$k]);                    
                    } 
                    $tpl = str_replace($parr[0][$k], $parr[2][$k], $tpl);                   
                }
            }
            
            // Авто выделение активных чек-боксов
            if (preg_match_all('/\[check\](.+?)\[\/check\]/is', $tpl, $parr)) {  
                foreach ($parr[1] as $k => $v)
                {
                    if(preg_match_all('/name="(.*?)"/is', $v, $larr))
                        foreach($larr[1] as $x => $r)
                        if(isset($this->varsArr['post'][$r]) && $this->varsArr['post'][$r] == 1) 
                            $parr[1][$k] = str_replace($larr[0][$x], $larr[0][$x].' checked', $parr[1][$k]);
                    $tpl = str_replace($parr[0][$k], $parr[1][$k], $tpl);
                }
            }
        }
        

        // Обработка блоков условного вывода
		if (isset($this->varsArr['regx']) && is_array($this->varsArr['regx'])) {
			foreach ($this->varsArr['regx'] as $id => $var) {
				$tpl = preg_replace($id, $var, $tpl);
			}
		}
        /*
        // Обработка ссылок (из относительных в абсолютные)
        if(preg_match_all('~ ((?:href|src)=["\'])([^"\']*)(["\'])~i', $tpl, $parr))
        {
            //die('<pre>.'.print_r($parr,1));
            foreach($parr[2] as $k => $link)
            {
                if($link[0] == '#') continue;
                if(preg_match('~^javascript:.*~i',$link)) continue;
                if(preg_match('~^http.*~i',$link)) continue;
                
                $linkFinish = ($link[0] == '/')? substr($link, 1) : $link;
                $link = preg_quote($link);  //экранирует спец-символы
                $tpl = preg_replace("~ {$parr[1][$k]}{$link}{$parr[3][$k]}~i", ' '. $parr[1][$k] . HOME .'/'. $linkFinish . $parr[3][$k], $tpl);
            }
        }*/
        
        return $tpl;
	}
}
?>