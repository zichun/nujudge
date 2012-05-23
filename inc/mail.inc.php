<?php
	if (!defined('INCLUDE_SECURE')) die();
	
	class mail_engine {
		private static $_mail_templates = array(
			'cycle-created'	=> array(
				'from'		=> 'operis@operis.comp.nus.edu.sg',
				'subject'	=> '[Operis] Cycle Created',
				'template'	=> 'cycle-created'
			),
			'data-imported'	=> array(
				'from'		=> 'operis@operis.comp.nus.edu.sg',
				'subject'	=> '[Operis] Data Imported',
				'template'	=> 'data-imported'
			),
			'data-vetted'	=> array(
				'from'		=> 'operis@operis.comp.nus.edu.sg',
				'subject'	=> '[Operis] Data Imported',
				'template'	=> 'data-vetted'
			)
		);
		
		
		var $_template;
		var $_subject;
		var $_body;
		var $_from;
		
		private static function read_template($template_file){
			global $INC_PREFIX;
			$file = dirname(__FILE__).'/mail_templates/'.$template_file.'.php';
			if (!file_exists($file)) return '';
			return file_get_contents( $file);
		}
		private static function apply_replacement( & $body, $replace) {
			foreach($replace as $field=>$val){
				$body = str_ireplace( '[['.$field.']]', $val, $body );
			}
			return $body;
		}
		
		function __construct($template, $subject=false, $from=false){
			if( isset( self::$_mail_templates[$template] ) ){
				$this->_template = $template;
			}else{
				$this->_template = false;
				$this->_subject = $subject;
				$this->_from = $from;
				$this->_body = $template;
			}
		}

		function send($to, $replace, $attachment='', $filename='attachment.htm') {
			$mail = new mime_mail();
			$mail->to = $to;

			if ($this->_template) {
				$t = self::$_mail_templates[$this->_template];
				$body = self::read_template($t['template']);				
				self::apply_replacement($body, $replace);

				$mail->from = $t['from'];
				$mail->subject = $t['subject'];
			} else {
				$body = $this->_body;
				self::apply_replacement($body, $replace);
				$mail->from = $this->_from;
				$mail->subject = $this->_subject;
			}

			if ($attachment != ''){
				$attach = fread(fopen($attachment, "r"), filesize($attachment)); 
				$mail->add_attachment($attach, $filename, "Content-Transfer-Encoding: base64 /9j/4AAQSkZJRgABAgEASABIAAD/7QT+UGhvdG9zaG");
			}

			$mail->body = $body;
			$mail->send();
		}
	}
?>
