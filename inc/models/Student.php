<?php
	class Student extends ActiveRecord\Model {
		static $validates_presence_of = array(
			array('name'),
			array('id'), //matriculation number
		);
		
		static $has_many = array(
			array('studentmodules'),
			array('modules', 'through' => 'studentmodules'),
			array('impressions')
		);
		
		public static function make($name, $id) {
			$student = new Student();
			$student->name = $name;
			$student->id = $id;
			if ($student->is_valid()) {
				$student->save();
				return $student;
			} else {
				return false;
			}
		}
		
		public static function delete_by_id($id) {
			$student = Student::find($id);
			if ($student) {
				$student->delete();
				return true;
			} else {
				return false;
			}
		}
	}
?>