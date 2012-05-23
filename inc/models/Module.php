<?php
	class Module extends ActiveRecord\Model {
		static $validates_presence_of = array(
			array('name'),
			array('module_code')
		);
		
		static $has_many = array(
			array('moduleprofessors'),
			array('studentmodules'),
			array('impressions'),
			array('professors', 'through' => 'moduleprofessors'),
			array('students', 'through' => 'studentmodules')
		);
		
		static $belongs_to = array(
			array('cycle')
		);
		
		public static function make($cycle_id, $name, $module_code) {
			$module = new Module();
			$module->cycle_id = $cycle_id;
			$module->name = $name;
			$module->module_code = $module_code;
			if ($module->is_valid()) {
				$module->save();
				return $module;
			} else {
				return false;
			}
		}
		
		public static function delete_by_id($id) {
			$module = Module::find($id);
			if ($module) {
				$module->delete();
			}
		}
	}
?>