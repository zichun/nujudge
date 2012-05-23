<?php
	if (!defined('INCLUDE_SECURE')){
		hack();
	}

    /**
     * Checks if the required POST request variables are set
     *
     * @param {array} $r	array of POST variables to check
	 * @param {mixed} [$e=false]	error code to throw if required POST variable is missing
	 * @return {string} $result	string with slashes stripped
     */
	function check_post_required($r, $e=false){
		for ($i=0;$i<sizeof($r);++$i){
			if(!isset($_POST[$r[$i]])){
				hack();
				return false;
			}
		}
		return true;
	}
	function check_and_get_post($r,$e=false){
		check_post_required($r,$e);
		for ($i=0;$i<sizeof($r);++$i){
			global $$r[$i];
			$$r[$i] = get_post($r[$i]);
		}
		return true;
	}
	/**
	 * Remove slashes from a string if gpc magic quotes is on
	 *
	 * @param {string} $var1	string to removeslash
	 * @return {string} $result	string with slashes stripped
	**/
	function rmslashes(&$var){
		if (get_magic_quotes_gpc()==1) {
			return stripslashes($var);
		}else {
			return $var;
		}
	}
	/**
	* Get Post variable
	*
	* @param {string} $field	Field name to get
		* @return {string} $result	value of the post variable or false if its not defined
	**/
	function get_post($field){
		if (isset($_POST[$field])) return rmslashes($_POST[$field]);
		return false;
	}
	/**
	 * Redirect function using header location
	 *
	 * @param {string} $path	Relative path to redirect to current page
	 */
	function redirect($path, $save=false){
		global $_G,$URL_BASE_DIR;
		if ($save){
			$_SESSION['redirect_from'] = $_G['URL'];//$_G['controller'] . '/' . $_G['action'];
		}
		if (substr($path, 0, 5) != 'http:') $path = $URL_BASE_DIR.$path;
		header('Location: '. $path);
		die();
	}
	function time_diff($since){
		global $_G;
		// array of time period chunks
		$chunks = array(
			array(60 * 60 * 24 * 365 , ('year')),
			array(60 * 60 * 24 * 30 , ('month')),
			array(60 * 60 * 24 * 7, ('week')),
			array(60 * 60 * 24 , ('day')),
			array(60 * 60 , ('hour')),
			array(60 , ('minute')),
			array(1, ('second')),
		);
				
		// $j saves performing the count function each time around the loop
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			
			// finding the biggest chunk (if the chunk fits, break)
			if (($count = floor($since / $seconds)) != 0) {
				// DEBUG print "<!-- It's $name -->\n";
				break;
			}
		}
		
		$print = ($count == 1) ? '1 '.$name : ("$count {$name}s");
		
		if ($i + 1 < $j) {
			// now getting the second item
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];
			
			// add second item if it's greater than 0
			if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
				$print .= ($count2 == 1) ? ', 1 '.$name2 : (", $count2 {$name2}s");
			}
		}
		return $print;		
	}
	function time_friendly($sql_time){
		return date('d/m/Y g:i A',$sql_time);
	}
	function date_friendly($sql_time){
		return date('d/m/Y',$sql_time);
	}
	function time_friendly2($sql_time){
		return date('g:i A',$sql_time) . ' ('.time_since($sql_time).' ago)';
	}
	function parse_order($order){
		$order = strtoupper($order);
		if ($order == '') return false;

		preg_match( '/([A-Z][A-Z]-|[A-Z][A-Z])-(.*)/', $order, $match);
		return $match;
	}

	function time_since($original) {
		global $_G;
		// array of time period chunks
		$chunks = array(
			array(60 * 60 * 24 * 365 , ('year')),
			array(60 * 60 * 24 * 30 , ('month')),
			array(60 * 60 * 24 * 7, ('week')),
			array(60 * 60 * 24 , ('day')),
			array(60 * 60 , ('hour')),
			array(60 , ('minute')),
			array(1, ('second')),
		);
		
		$today = time(); /* Current unix time  */
		$since = $today - $original;
		
		// $j saves performing the count function each time around the loop
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			
			// finding the biggest chunk (if the chunk fits, break)
			if (($count = floor($since / $seconds)) != 0) {
				// DEBUG print "<!-- It's $name -->\n";
				break;
			}
		}
		
		$print = ($count == 1) ? '1 '.$name : ("$count {$name}s");
		
		if ($i + 1 < $j) {
			// now getting the second item
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];
			
			// add second item if it's greater than 0
			if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
				$print .= ($count2 == 1) ? ', 1 '.$name2 : (", $count2 {$name2}s");
			}
		}
		return $print;
	}

	function unformat_money($price){
		if ($price[0] != '$') $price = '$' . $price;
		$k = sscanf($price,'$%d.%d');
		if ($k[0] == NULL) $k[0] = 0;
		if (!isset($k[1]) || $k[1] == NULL) $k[1] = 0;
		$p = $k[0] * 100 + $k[1];
		return $p;
	}
	function format_money($value){
		if ($value < 0){
			return sprintf('($%.2f)',-$value/100);
		}else{
			return sprintf('$%.2f',$value/100);
		}
	}

	function render_links($module_name){
		global $INC_PREFIX, $ModuleHandler;
		$module = $ModuleHandler->get_module($module_name);
		$links = $module->get_links();
		echo('<div id="module_links">');
			echo('<ul>');
			for ($i=0;$i<sizeof($links);++$i){
				echo('<li><a href="'.$INC_PREFIX.MOD_DIR.'/'.$module_name.'/'.$links[$i][1].'">'.$links[$i][0].'</a></li>');
			}
			echo('</ul>');
		echo('</div>');
	}
	

	function getip() {
		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
		$ip = getenv('HTTP_CLIENT_IP');
		else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
		$ip = getenv('HTTP_X_FORWARDED_FOR');
		else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
		$ip = getenv('REMOTE_ADDR');
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
		$ip = $_SERVER['REMOTE_ADDR'];
		else
		$ip = 'unknown';
		return($ip);
	}
	// Function maketime
	// param:
	//		$month (int) required
	//		$day (int) required
	//		$year (int) required
	// description:
	//		gets the timestamp of the date in the parameter list
	// returns:
	//		timestamp
	function maketime($month, $day, $year){
		return mktime(0,0,0,$month, $day, $year);
	}

	// Function makeformatedTime
	// param:
	//		$string (string) required
	// description:
	//		get a timestamp of a given string in the format dd/mm/yyyy
	// returns:
	//		timestamp
	function makeformatedTime($string){
		$string = split('/', $string);
		return maketime($string[1],$string[0],$string[2]);
	}

	// Function makedate
	// param:
	//		$timestamp (timestamp) [optional]
	// description:
	//		retrieves the individual date, month and year component of a given timestamp
	// returns:
	//		an assosiative array of the DMY of a given timestamp
	function makedate($timestamp=''){
		if ($timestamp == '') $timestamp = time();
		$d = date('d',$timestamp);
		$m = date('m',$timestamp);
		$y = date('Y', $timestamp);
		return array($d,$m,$y,'day'=>$d,'month'=>$m, 'year'=>$y, 'd'=>$d, 'm'=>$m, 'y'=>$y);
	}
	// Function reformatDOB
	// param:
	//		$timestamp (timestamp)  [optional]
	// description:
	//		formats a given timestamp into the format mm/dd/yyyy
	// returns:
	//		string in the format mm/dd/yyyy of the given timestamp
	function reformatDOB($timestamp = ''){
		if ($timestamp == '') $timestamp = time();
		$a = makedate($timestamp);
		return $a['d'].'/'.$a['m'].'/'.$a['y'];
	}

	// Function reformatShortDOB
	// param:
	//		$timestamp (timestamp)  [optional]
	// description:
	//		formats a given timestamp into the format mm/dd/yyyy
	// returns:
	//		string in the format mm/dd of the given timestamp
	function reformatShortDOB($timestamp = ''){
		if ($timestamp == '') $timestamp = time();
		$a = makedate($timestamp);
		return $a['d'].'/'.$a['m'];
	}
	// Fuction verifyDate
	// param: 
	//		$date (string)  required
	// description:
	//		verifies if a date (in the format mm/dd/yyyy) is valid
	// returns
	//		-1 if date is not valid
	//		timestamp of date if valid
	function verifyDate($date){ 
		$date = split('/', $date);
		if (sizeof($date) != 3) return -1;
		if (!checkdate($date[1], $date[0], $date[2]) ) return -1;
		return maketime($date[1], $date[0], $date[2]);
	}

	
	// function toInt
	// param:
	//		$number (string) required
	// description:
	//		goes through character by character and remove all non-integral characters
	// returns:
	//		a positive integer or zero
	function toInt($number){
		$tr = '';
		for ($i=0;$i<strlen($number);++$i){
			if ((int)$number[$i] == $number[$i]){
				$tr .= $number[$i];
			}
		}
		return (int)$tr;
	}

	// function timestamp2age
	//		@ timestamp  (int:timestamp) required
	// description:
	//		calculates the number of years from given timestamp to current timestamp
	// returns:
	//		an integer (number of years)
	function timestamp2age($timestamp){
		return date('Y') - date('Y', $timestamp);
	}
	function makeShortName($name){
		return preg_replace('/[^a-zA-Z0-9\-]/','',strtolower(str_replace(' ','-',trim($name))));
	}
	function formatText($text){
		return htmlspecialchars($text);
	}
	function purify($text){
		$config = HTMLPurifier_Config::createDefault();
		$purifier = new HTMLPurifier();
		return $purifier->purify( $text );
	}

	function nice_value($value){
		$d = floor($value / 100);
		$c = $value % 100;

		if ($d < 100){
			if (strlen($c) == 1){
				$c = '0' . $c;
			}
			return '$'.$d.'.'.$c;
		}else if($d < 1000){
			return '$'.$d;
		}else if($d < 1000000){
			return('$'.sprintf('%.1f',$d/1000).'k');
		}else{
			return '$'.sprintf('%.1f',$d/1000000).'m';
		}
	}
	function snippet($str, $length=10, $trailing='...'){
		$length-=mb_strlen($trailing);
		if (mb_strlen($str)> $length){
			return mb_substr($str,0,$length).$trailing;
		}else{
			$res = $str;
		}
		return $res;
	}

	function makelink($controller, $action='', $param=''){
		global $_G;
		return DOMAIN_BASE.$controller.'/'.$action . ($param == '' ? '' : '/' . $param);
	}
	function makeajaxlink($controller, $action=''){
		global $_G;
		return DOMAIN_BASE.$controller.'/ajax/'.$action;
	}
	function makeimgpath($shortname){
		return IMG_URL.$shortname.'/'.'o/';
	}
	function makeimglink($shortname, $img){
		return makeimgpath($shortname).$img;
	}
	function makeimgdir($shortname){
		return IMG_DIR . $shortname.'/o/';
	}
	
	function include_template($t, $var=false) {
		global $INC_PREFIX, $_G, $URL_BASE_DIR;
		require_once($INC_PREFIX.'r/templates/'.$t.'.php');
	}

?>