<?php
	if (!defined('INCLUDE_SECURE')) die();

	class mime_mail {
		var $parts;
		var $to;
		var $from;
		var $headers;
		var $subject;
		var $body;

		function mime_mail() {
			$this->parts = array();
			$this->to =  "";
			$this->from =  "";
			$this->subject =  "";
			$this->body =  "";
			$this->headers =  "";
		}

		function add_attachment($message, $name = "", $ctype = "application/octet-stream") {
			$this->parts [] = array (
			 "ctype" => $ctype,
			 "message" => $message,
			 "encode" => '',
			 "name" => $name
			);
		}

		function build_message($part) {
			$message = $part["message"];
			$message = chunk_split(base64_encode($message));
			$encoding = "base64";
			return "Content-Type: ".$part["ctype"].($part["name"]? "; name = \"".$part["name"]."\"" : "")."\nContent-Transfer-Encoding: $encoding\n\n$message\n";
		}
		function build_multipart() {
			$boundary = "b".md5(uniqid(time()));
			$multipart = "Content-Type: multipart/mixed; boundary = $boundary\n\nThis is a MIME encoded message.\n\n--$boundary";
			for($i = sizeof($this->parts)-1; $i>=0; $i--) $multipart .= "\n".$this->build_message($this->parts[$i]). "--$boundary";
			return $multipart.=  "--\n";
		}

		function send() {
			$mime = "";
			if (!empty($this->from)) $mime .= "From: ".$this->from. "\n";
			if (!empty($this->headers)) $mime .= $this->headers. "\n";
			if (!empty($this->body)) $this->add_attachment($this->body, "", "text/html");   
			$mime .= "MIME-Version: 1.0\n".$this->build_multipart();
			if (!defined('PRODUCTION')) @mail($this->to, $this->subject, "", $mime);
		}
	}

?>
