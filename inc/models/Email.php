<?php
	class Email extends ActiveRecord\Model {
		static $validates_presence_of = array(
			array('subject'),
			array('content')
		);
		
	}
?>