<?php
	class Impression extends ActiveRecord\Model {
		static $validates_presence_of = array(
			array('module_id'),
			array('student_id'),
			array('professor_id'),
			array('role'),
			array('comments'),
			array('recommend_for_ta'),
			array('rank')
		);
		
		static $belongs_to = array(
			array('module'),
			array('student'),
			array('professor')
		);

		//Other fields: impresssion_id
		
		public static function make($module_id, $student_id, $professor_id, $role, $comments, $recommend_for_ta, $rank) {
			$impression = new Impression();
			$impression->module_id = $module_id;
			$impression->student_id = $student_id;
			$impression->professor_id = $professor_id;
			$impression->role = $role;
			$impression->comments = $comments;
			$impression->recommend_for_ta = $recommend_for_ta;
			$impression->rank = $rank;		
			
			if ($impression->is_valid()) {
				$impression->save();
				return $impression;
			}
		}
		
		public static function delete_by_id($id) {
			$impression = Impression::find($id);
			if ($impression) {
				$impression->delete();
				return true;
			} else {
				return false;
			}
		}
		
		
		
		
	}
?>