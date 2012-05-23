<?php
	class Professor extends ActiveRecord\Model {
		static $validates_presence_of = array(
			array('name'),
			array('id')
		);
		
		static $has_many = array(
			array('impressions'),
			array('moduleprofessors'),
			array('modules', 'through' => 'moduleprofessors')
		);
		
		
		public static function make($name, $id, $friendly_name="") {
			if ($friendly_name == "") {
				$friendly_name = $name;
			}
			$professor = new Professor();
			$professor->name = $name;
			$professor->id = $id;
			$professor->friendly_name = $friendly_name;
			if ($professor->is_valid()) {
				$professor->save();
				return $professor;
			} else {
				return false;
			}
		}
		
		public static function delete_by_id($id) {
			$professor = Professor::find($id);
			if ($professor) {
				$professor->delete();
				return true;
			} else {
				return false;
			}
		}
	}
?>