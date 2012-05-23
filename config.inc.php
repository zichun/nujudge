<?php
	function error($action){
		// todo: log depending on error
		redirect('error/'.$action);
		die();
	}
	function hack(){ error('403'); die(); }
	function invalid_url() { error('404'); die(); }
	function _404() { error('404'); die(); }
	function login() { redirect('web/',1); die(); }
	function restricted() { error('restricted'); die(); }
	
	if (!isset($INC_PREFIX)) hack();
	session_start();

	define('INCLUDE_SECURE','1');
	define('DATE1','M j, g:ia');

	///
	/// Include common library files
	///
	require_once($INC_PREFIX.'inc/php-activerecord/ActiveRecord.php');
	require_once($INC_PREFIX.'inc/function.inc.php');
	require_once($INC_PREFIX.'inc/event.inc.php');
	require_once($INC_PREFIX.'inc/crypt.inc.php');
	require_once($INC_PREFIX.'inc/mail.php');
	

	///
	/// Define environment
	///
	if (file_exists($INC_PREFIX.'development')) {
		define('ENVIRONMENT', 'development');
	}else{
		define('ENVIRONMENT', 'deployment');
	}
	require_once('config_'.ENVIRONMENT.'.php');


	///
	/// Include common include files pertaining to operis
	///
	require_once($INC_PREFIX.'inc/mail.inc.php');
	require_once($INC_PREFIX.'inc/roles.inc.php');
	require_once($INC_PREFIX.'inc/operis.inc.php');
	
	///
	/// Connect to Database
	///
	//$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);

	ActiveRecord\Config::initialize(function($cfg)
	{
		$cfg->set_model_directory('inc/models');
		$cfg->set_connections(array('db' => DB));
			
		$cfg->set_default_connection('db');
	});

	
	/// 
	/// Set up Globals and Relative Path for URL Re-Writing
	///
	
	$_G = array();
	if (isset($_SERVER['HTTP_HOST'])){
		$_G['BASE_DIR'] = 'http://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
	}
	
	$_G['REL_PATH'] = dirname($_SERVER['PHP_SELF']);
	$_G['FILE'] = basename($_SERVER['PHP_SELF']);
	$_G['PATH'] = $_SERVER['PHP_SELF'];
	$_G['URL'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		function find_base($dir) {
			global $_find_base_name;
			if (!isset($_find_base_name)){
				$_find_base_name = false;
			}
			$dir = explode('/', $dir);
			$result = '';
			while (count($dir) && $dir[count($dir) - 1] != $_find_base_name) {
				$result .= '../';
				array_pop($dir);
			}
			return $result;
		}
		$URL_BASE_DIR = find_base(dirname($_SERVER['REQUEST_URI'].'x'));	// Find URL base directory
		$BASE_DIR = find_base(dirname($_SERVER['SCRIPT_FILENAME']));					// Find file system base directory
	
	///
	/// User Sessions
	///
	/*
	if (isset($_SESSION['nusnet_id'])){
		$_G['user_login'] = true;
		
		$_SESSION['name'] = $user->name;
		$_SESSION['nusnet_id'] = $user->nusnet_id;
		$_SESSION['email'] = $user->email;
		$_SESSION['role'] = $user->role;
		$_G['user'] = new user;
		$_G['user']->find( new MongoId($_SESSION['user_id']) );
	}else{
		$_G['user_login'] = false;
	}
	*/
?>