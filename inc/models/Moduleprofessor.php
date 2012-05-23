<?php

	class Moduleprofessor extends ActiveRecord\Model {
		static $table_name = 'module_professors';
		
		static $validates_presence_of = array(
			array('module_id'),
			array('professor_id')
		);
		
		static $belongs_to = array(
			array('module'),
			array('professor')
		);
		
	
	
		public static function make($module_id, $professor_id) {
			$moduleprofessor = new Moduleprofessor();
			$moduleprofessor->module_id = $module_id;
			$moduleprofessor->professor_id = $professor_id;
			if ($moduleprofessor->is_valid()) {
				$moduleprofessor->save();
				return $moduleprofessor;
			} else {
				return false;
			}
		}
		
		public static function delete_by_module($module_id, $professor_id) {
			$moduleprofessor = Moduleprofessor::find(array('conditions'=> array("module_id=? AND professor_id=?", $module_id, $professor_id)));

			if (count($moduleprofessor) > 0) {
				$moduleprofessor->delete();
				return true;
			} else {
				return false;
			}
		}
	}

?>