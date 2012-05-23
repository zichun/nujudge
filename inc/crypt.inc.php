<?php
	if (!defined('INCLUDE_SECURE')){
		hack();
	}
	define('E_KEY','8HJNIU83JIFNKLDJkljnKLfEIU2qwerq');
	define('SALT', 'qwQWEFwwefe3r9wREjewkrRid23fdhnd');
        
	class EncDec{
		var $hash;
		function hexToInt($s, $i){
			(int)$j = $i * 2;
			(string)$s1 = $s;
			(string)$c = substr($s1, $j, 1);  // get the char at position $j, length 1 
			(string)$c1 = substr($s1, $j+1, 1); // get the char at postion $j + 1, length 1
			(int)$k = 0;
		switch ($c){
			case "A":
				$k += 160;
				break;
			case "B":
				$k += 176;
				break;
			case "C":
				$k += 192;
				break;
			case "D":
				$k += 208;
				break;
			case "E":
				$k += 224;
				break;
			case "F":
				$k += 240;
				break;
			case "G":
				$k += 0;
				break;
			default:
			(int)$k = $k + (16 * (int)$c);
			break;
		}   
		switch ($c1){
			case "A":
				$k += 10;
				break;
			case "B":
				$k += 11;
				break;
			case "C":
				$k += 12;
				break;
			case "D":
				$k += 13;
				break;
			case "E":
				$k += 14;
				break;
			case "F":
				$k += 15;
				break;
			case "G":
			$k += 0;
				break;
			default:
				$k += (int)$c1;    
				break;
			}
			
			return $k;
		}

		function hexToIntArray($s){
			(string)$s1 = $s;
			(int)$i = strlen($s1);
			(int)$j = $i / 2;
			for($l = 0; $l < $j; $l++)
			{
				(int)$k = $this->hexToInt($s1,$l);
				$ai[$l] = $k;
			}
			return $ai;
		}

		function charToInt($c){
			$ac[0] = $c;
			return $ac;
		}

		function xorString($ai){
			$s = $this->hash; // 
			(int)$i = strlen($s);
			$ai1 = $ai;
			(int)$j = count($ai1);
			for($i = 0; $i < $j; $i = strlen($s))
				$s = $s.$s;

			for($k = 0; $k < $j; $k++){
				(string)$c = substr($s,$k,1);
				$ac[$k] = chr($ai1[$k] ^ ord($c));
			}
			(string)$s1 = implode('', $ac);
			return $s1;
		}

		function phpDecrypt($s){
			{
				$ai = $this->hexToIntArray($s);
				(string)$s1 = $this->xorString($ai);
				return $s1;
			}
		}
		function intToHex($i){
			(int)$j = (int)$i / 16;
			if ((int)$j == 0) {
				(string)$s = "G";
			}else{
				(string)$s = strtoupper(dechex($j));
			}   
			(int)$k = (int)$i - (int)$j * 16;
			(string)$s = $s.strtoupper(dechex($k));
			return $s;	
		}
		function xorCharString($s){
			$ac = preg_split('//', $s, -1, PREG_SPLIT_NO_EMPTY);
			(string)$s1 = $this->hash;
			(int)$i = strlen($s1); 
			(int)$j = count($ac); 
			for($i=0; $i < $j; $i = strlen($s1)){
				$s1 = $s1.$s1;
			}
			for($k = 0; $k < $j; $k++){
			$c = substr($s1,$k,1);
			$ai[$k] = ord($c) ^ ord($ac[$k]);
			}

			return $ai;
		}
		function phpEncrypt($s){
			$ai = $this->xorCharString($s);
			$s1 = "";
			for($i = 0; $i < count($ai); $i++)
				$s1 = $s1.$this->intToHex((int)$ai[$i]);
			return $s1;
		}
	} 

	function encode($msg, $key=E_KEY){
		$ed = New EncDec;
		$ed->hash = $key;
		return $ed->phpEncrypt($msg);
	}
	function decode($msg, $key=E_KEY){
		$ed = New EncDec;
		$ed->hash = $key;
		return $ed->phpDecrypt($msg);
	}

	function encrypt($msg, $key=E_KEY) {
		$msg .= '';
		return rawurlencode(base64_encode(md5ctrencrypt($msg, base64_decode($key))));
	}
	function decrypt($msg, $key=E_KEY) {
		//Decode msg and key
		$msg = str_replace(" ", "+", $msg);
		return md5ctrdecrypt(base64_decode(rawurldecode($msg)), base64_decode($key));
	}
	function md5ctrencrypt($msg, $key) {
		//Generate IV
		$iv = "";
		for($i=0; $i<16; $i++) $iv .= chr(mt_rand(0, 255));
		
		//Make 128-bit MD5 key concatenated to 128-bit IV
		$md5_key = pack("H*", md5($key)) . $iv;
		
		//Generate keystream
		$md5_ks = "";
		for($i=0; $i<strlen($msg); $i+=16) {
			$md5_ks .= pack("H*", md5(pack("V*", $i) . $md5_key));
		}
		$md5_ks = substr($md5_ks, 0, strlen($msg));
		$ct = ($md5_ks ^ $msg);
		
		//Hash ciphertext
		$hash = pack("H*", md5($md5_key . pack("H*", md5($md5_key . $ct))));
		
		return $iv . $hash . $ct;
	}
	function md5ctrdecrypt($msg, $key) {
		
		//Extract IV and hash
		$iv = substr($msg, 0, 16);
		$hash = substr($msg, 16, 16);
		$msg = substr($msg, 32);
		
		//Make 128-bit MD5 key concatenated to 128-bit IV
		$md5_key = pack("H*", md5($key)) . $iv;
		
		//Verify hash
		if ($hash != pack("H*", md5($md5_key . pack("H*", md5($md5_key . $msg))))) {
//			error("Invalid ciphertext. Please try again and if the problem persists, please report it to your ELF administrator.", 1, "Hack attempt detected! User: " . $_SESSION['user_id']);
		}
		
		//Generate keystream
		$md5_ks = "";
		for($i=0; $i<strlen($msg); $i+=16) {
			$md5_ks .= pack("H*", md5(pack("V*", $i) . $md5_key));
		}
		$md5_ks = substr($md5_ks, 0, strlen($msg));
		$msg ^= $md5_ks;
		
		return $msg;
	}

	function rc4($msg, $key) {
		for($i=0; $i<256; $i++) $state[$i] = $i;
		$j = 0;
		for($i=0; $i<256; $i++) {
			$j = ($j + $state[$i] + ord(substr($key, $i % strlen($key), 1))) & 0xFF;
			$tmp = $state[$i]; $state[$i] = $state[$j]; $state[$j] = $tmp;
		}
		$ciphertext = "";
		$i = 0; $j = 0;
		for($a=0; $a<strlen($msg); $a++) {
			$i = ($i + 1) & 0xFF;
			$j = ($j + $state[$i]) & 0xFF;
			$tmp = $state[$i]; $state[$i] = $state[$j]; $state[$j] = $tmp;
			$n = $state[($state[$i] + $state[$j]) & 0xFF];
			$ciphertext .= chr(ord(substr($msg, $a, 1)) ^ $n);
		}
		return $ciphertext;
	}

	function pure_md5($msg) {
		return pack('H*', md5($msg));
	}

	function md5_hmac($text, $k) {		
		$b = 64;
		$ipad = ""; for($i=0; $i<$b; $i++) $ipad .= chr(0x36);
		$opad = ""; for($i=0; $i<$b; $i++) $opad .= chr(0x5c);
		
		if (strlen($k) == $b) {
			$k0 = $k;
		}
		else if (strlen($k) > $b) {
			$k = pack("H*", md5($k));
			while(strlen($k) < $b) $k .= "\0";
			$k0 = $k;
		}
		else if (strlen($k) < $b) {
			while(strlen($k) < $b) $k .= "\0";
			$k0 = $k;
		}
		$tmp = pack("H*", md5(($k0 ^ $ipad) . $text));
		$tmp2 = pack("H*", md5(($k0 ^ $opad) . $tmp));
		return $tmp2;
	}


	function generate_session_key($keylength=32){
		srand(microtime()*10000000);
		$str = '';
		for ($i=1;$i<=$keylength;$i++){
			$str .= chr(rand(0,255));
		}
		return encode($str);
	}
?>