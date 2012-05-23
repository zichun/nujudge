<?php
	if (!defined('INCLUDE_SECURE')) die();
	
	class event {
		static public function log($subject, $message){
			global $_FP_ADMIN;
			$mail = new mime_mail();
				$mail->from = 'event@fireplace.sg';
				$mail->subject = '[Fireplace-N] '. $subject;
				$mail->body = $message;
				
			for ($i=0;$i<sizeof($_FP_ADMIN);++$i){
				$mail->to = $_FP_ADMIN[$i]->email;
				$mail->send();
			}
		}
	}
?>