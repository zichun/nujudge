<?php
	class User extends ActiveRecord\Model {
		static $validates_presence_of = array(
			array('id'),
			array('name'),
			array('email'),
			array('role')
		);
		
		static $has_one = array(
			array('cycle')
		);
		
		
		public static function make($id, $name, $email, $role) {
			
			$user = new User();
			$user->id = $id;
			$user->name = $name;
			$user->email = $email;
			$user->role = $role;
			if ($user->is_valid()) {
				$user->save();
				return $user;
			} else {
				return false;
			}
		}
		
		public static function delete_by_id($id) {
			$user = User::find($id);
			if ($user) {
				$user->delete();
				return true;
			} else {
				return false;
			}
		}
		
		public static function get_by_role($role) {
			return User::find('all',array('conditions'=>"role = '$role'"));	
		}
		
	}
?>