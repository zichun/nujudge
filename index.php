<?php
	$INC_PREFIX = '';
	$_find_base_name = 'nu';
	require_once($INC_PREFIX.'config.inc.php');
	if (!isset($_GET['controller'])){
		hack();
	}
	if (!isset($_GET['action'])){
		hack();
	}
	
	$param = isset($_GET['param']) ? $_GET['param'] : '';
	$ajax = isset($_GET['ajax']);
	$controller = $_GET['controller'];
	$action = $_GET['action'];

	
	$pattern = '/[a-zA-Z0-9_#]+/';
	if (!preg_match($pattern, $controller)){
		invalid_url();
	}
	if (!preg_match($pattern, $action)){
		invalid_url();
	}
	
	$param = explode('/',$param);
	$_G['param'] = $param;
	$_G['controller'] = $controller;
	$_G['action'] = $action;
	
	if (!$ajax){
		$file = $controller . '/'.$action.'.php';
	}else{
		$file = $controller . '/ajax/'.$action.'.php';
	}

	if (!file_exists($file)){
		if (!$ajax){
			$_G['param'] = $param = $action;
			if (!file_exists($controller.'/default.php')) {
				_404();
			}
	
			$_G['action'] = $action = 'default';
			$file = $controller . '/'.$action.'.php';
		}else{
			_404();
		}
	}
	
	if (file_exists($controller . '/index.php')){
		require($controller . '/index.php');
		$t = array();
		if (isset($_options) && isset($_options[$action])){
			$t = array_merge($t, $_options[$action]);
		}
		if (isset($_options) && isset($_options['all'])){
			$t = array_merge($t, $_options['all']);
		}
		foreach($t as $option => $value){
			switch($option){
				case 'auth': case 'admin':
					require($INC_PREFIX.'inc/auth.inc.php');
					break;
				case 'deny':
					_404(); break;
			}
		}
	}

	
	unset($t);
	ob_start();
	include $file;
	$content = ob_get_contents();
	ob_clean();
	
	echo($content);
?>