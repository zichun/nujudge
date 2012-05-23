<?php
	class Studentmodule extends ActiveRecord\Model {
		static $table_name = 'student_modules';
		static $validates_presence_of = array(
			array('module_id'),
			array('student_id')
		);	
		
		static $belongs_to = array(
			array('module'),
			array('student')
		);
		
		public static function make($module_id, $student_id) {
			$studentmodule = new Studentmodule();
			$studentmodule->module_id = $module_id;
			$studentmodule->student_id = $student_id;
			if ($studentmodule->is_valid()) {
				$studentmodule->save();
				return $studentmodule;
			} else {
				return false;
			}
		}
		
		public static function delete_by_module($module_id, $student_id) {
			$studentmodule = Studentmodule::find(array('conditions'=> array("module_id = $module_id", "student_id = $student_id")));
			
			if (count($studentmodule) > 0) {
				foreach ($studentmodule as $entry) {
					$entry->delete();
				}
				return true;
			} else {
				return false;
			}
		}
	}
?>