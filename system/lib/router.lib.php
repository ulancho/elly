<?php
class router {

	function delegate() {

        $_GET_KEYS = array_keys($_GET);
		$module = array_shift($_GET_KEYS);
		$mapTmp = explode('@', $module);
			// если в массиве $map есть первый элемент, то используем его как имя контроллера
			// если в масиве $map есть второй элемент, то записываем его как имя экшена
			// если массив $map пустой, то имя контроллера и имя экшена будет index
		$map['module'] = ( !empty($mapTmp[0]) ) ? $mapTmp[0] : config::get('defaultController', 'index');
		$map['action'] = ( !empty($mapTmp[1]) ) ? $mapTmp[1] : '';
        
        /*        
        // get requested controller and action from encrypted or normal request
		if ( config::get('urlEncrypt', false) ) {
			$map = decrypt(request::req('go'));
		} else {
			$map['module'] = request::req('module');
			$map['action'] = request::req('action');
		}

			// get defaultController
		if ( empty($map['module']) ) {
			$map['module'] = config::get('defaultController', 'index');
		}*/
			// if no action, and there is default module, get default action from config, or set index action
		if ( empty($map['action']) && $map['module']==config::get('defaultController') ) {
			$map['action'] = config::get('defaultAction', 'index');
		} elseif ( empty($map['action']) ) {
			$map['action'] = 'index';
		}
		$action = $map['action'];
        
        
        // [ELLY+]
        $pDebug = CDebug::getInstance();
        if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
            $pDebug->Enable = true;            
            } 
        else $pDebug->Enable = config::get('DEBUG', 1);
        //if($pDebug->Enable) include("system/mod/backup.php");
        $pDebug->group('$map', $map);


		$template_folder = 'templates';
		$widget_folder = 'widgets';
        
        
		if ( substr($map['module'], -6) == 'widget' ) {
            $widget = substr($map['module'], 0, -7);            
            $controller_class = $widget.'Widget';
            $template_path = SITE_ROOT.'/'.$widget_folder.'/'.$widget.'/'.$action.'.tpl';
        }
        // check if module is widget, then render widget (get widget name from "action" variable, set action=index and set template path to widget template)
        elseif ( $map['module']=='widget' ) {
			$widget = $map['action'];
			$controller_class = $widget.'Widget';
			$action = 'index';
			$template_path = SITE_ROOT.'/'.$widget_folder.'/'.$widget.'/'.$widget.'.tpl';
		} else {
			$controller_class = $map['module'].'Controller';
			$template_path = SITE_ROOT.'/'.$template_folder.'/'.$map['module'].'/'.$action.'.tpl';
		}

			// create controller instance
		$view =& template::getInstance();
		$view->setCurrentPage($map['module'], $action);

		$controller = new $controller_class($view);
		// [ELLY+] !Отключил чтоб корректно работали Виджеты
        //$controller->setContext(request::req('context', 'html'));
        

		if ( !is_callable(array($controller_class, $action)) ) {
			//header('Content-type: application/json'); TODO: 404 header
			//$controller->setTemplateName('404');
            $template_path = SITE_ROOT.'/'.$template_folder.'/404.tpl';
			$tpl_vars = array();
		} else {            
            // take action
			$tpl_vars = $controller->$action();
		}
                
        // [ELLY+]        
        if($controller->getSubTemplateName()) $template_path = SITE_ROOT.'/'.$template_folder.'/'.$map['module'].'/'.$controller->getSubTemplateName().'.tpl';
        $pDebug->setContext($controller->getContext());
        
        //echo $controller->getContext();
        // [ELLY+]
        //if($tpl_vars === false) {
        //    $content = jsonResponse(0, array('controller'=>$controller_class, 'action'=>$action), 'Error 404');
        //    $pDebug->warn('Запрошена страница с ошибкой');
        //}
			// context JSON == render all tpl variables as json object
			// context AJAX == render only html for current module (or widget)
			// context HTML == render main.tpl (with header, footer and content - DEFAULT VIEW)
		//else
        if ( $controller->getContext()=='json' ) {

			// [ELLY+] {
            if($controller->vars)
            {                
                //$tpl_vars = array_merge($controller->vars['json'], array('controller'=>$controller_class, 'action'=>$action));
			    $tpl_vars= isset($controller->vars['json']) ? $controller->vars['json'] : null;
                $html    = isset($controller->vars['html']) ? $controller->vars['html'] : null;
                $script  = isset($controller->vars['script']) ? $controller->vars['script'] : '';
                $script .= $pDebug->debugEND();
                $content = jsonResponse(1, $tpl_vars, '', $html, $script);
                
            }
            else // [ELLY+] }
            {
                if ( isset($tpl_vars['result']) && $tpl_vars['result']==0 ) {
    				$content = ( isset($tpl_vars['message']) ) ? jsonResponse(0, array('controller'=>$controller_class, 'action'=>$action), $tpl_vars['message']) : jsonResponse(0);
    			} else {
    				if ( empty($tpl_vars) ) {
    					$tpl_vars = array();
    				} else {
    					foreach ($tpl_vars as $key => $value) {
    						if ( gettype($tpl_vars[$key])=='object' && is_subclass_of($tpl_vars[$key], 'model') ) {
    							$tpl_vars[$key] = $tpl_vars[$key]->toJsonArray();
    						} elseif ( gettype($tpl_vars[$key])=='array' ) {
    							foreach ($tpl_vars[$key] as $model_key => $model_value) {
    								if ( !is_subclass_of($tpl_vars[$key][$model_key], 'model') ) {
    									break;
    								} else {
    									$tpl_vars[$key][$model_key] = $tpl_vars[$key][$model_key]->toJsonArray();
    								}
    							}
    						}
    					}
    				}
    				$tpl_vars = array_merge($tpl_vars, array('controller'=>$controller_class, 'action'=>$action));
    				$content = jsonResponse(1, $tpl_vars, '', null, $pDebug->debugEND());
    			}
            }
			header('Content-type: application/json; charset=utf-8');

		} elseif ( $controller->getContext()=='ajax' ) {

            header('Content-type: text/html; charset=utf-8');
			$view->setTemplate($template_path);
			$view->setVars($tpl_vars);
			$content = $view->render();

            /*
			header('Content-type: application/json; charset=utf-8');
            //header('Content-type: text/html; charset=utf-8');
			/*$view->setTemplate($template_path);
			$view->setVars($tpl_vars);
			$html = $view->render();
            */
            /*
            $view->setContent($template_path);
			$view->setVars($tpl_vars);
			$html = $view->content();
            // [ELLY+] {
		    $tpl_vars= isset($controller->vars['json']) ? $controller->vars['json'] : null;
            $html    = isset($html) ? $html : null;
            $script  = isset($controller->vars['script']) ? $controller->vars['script'] : '';
            $script .= $pDebug->debugEND();
            $content = jsonResponse(1, $tpl_vars, '', $html, $script);

            // [ELLY+] }
            */
		} elseif ( $controller->getContext()=='html' ) {

			// [ELLY+]
            if($tpl_vars === false) {
                //$controller->setTemplateName('404');
                $tpl_vars = array();
                $template_path = SITE_ROOT.'/'.$template_folder.'/404.tpl';
            }
            
            header('Content-type: text/html; charset=utf-8');
			$view->setContent($template_path);
			$view->setTemplate(SITE_ROOT.'/'.$template_folder.'/'.$controller->getTemplateName().'.tpl');
			$view->setVars($tpl_vars);
			$content = $view->render();
            
            
            //ob_start();
            //$pDebug->debugEND();
            //$content = $content . ob_get_clean();
            

		}

		// if debug, output content, else gzip and output
		if ( config::get('DEBUG', 1) ) {
			echo $content;
		} else {
			header('content-encoding: gzip');
			echo gzencode($content);
            //echo $content;
		}
	}
}