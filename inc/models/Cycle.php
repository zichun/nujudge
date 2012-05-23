<?php
	class Cycle extends ActiveRecord\Model {
		const STAGE_CREATE_CYCLE = 'create cycle';
		const STAGE_POPULATE = 'populate database';
		const STAGE_VET = 'vet database';
		const STAGE_SEND_EMAIL = 'send emails';
		const STAGE_COMPLETED = 'cycle completed';

		static $validates_presence_of = array(
			array('name'),
			array('user_id') //creator of cycle
		);
		
		/* Other attributes
		
		id
		date_created
		
		*/
		
		static $has_many = array(
			array('modules')
		);
		
		static $belongs_to = array (
			array('user')
		);

		public static function vet($cycle_id, $vetted_by_id) {
			$cycle = Cycle::find($cycle_id);
			if ($cycle) {
				$cycle->vetted_by_id = $vetted_by_id;
				$cycle->save();
				Cycle::update_stage($cycle, CYCLE::STAGE_VET);
			}
		}
		
		public static function update_stage($cycle, $stage) {
			$cycle->stage = $stage;
			$cycle->save();

			switch ($stage) {
				case Cycle::STAGE_CREATE_CYCLE:
					$creator_name = $cycle->user->name;
					$creator_role = $cycle->user->role;
					$created_time = date(DATE1, $cycle->date_created);

					//
					// Send emails to all coordinators that a new cycle has been created
					//					
					$engine = mail_engine('cycle_created');
					$coords = User::get_by_role(Roles::Coordinator);
					foreach($coords as $coord) {
						$cname = ($creator_name == $coord->name ? 'yourself' : $creator_name);
						$engine->send($coords, array(
							'name'=>$coord->name,
							'creator_name'=>$cname,
							'creator_role'=>$creator_role,
							'created_time'=>$created_time
						));
					}

					break;
				case Cycle::STAGE_POPULATE:
					break;
			}
		}

		public static function make($name, $user_id) {
			$cycle = new Cycle();
			$cycle->name = $name;
			$cycle->user_id = $user_id;
			if ($cycle->is_valid()) {
				$cycle->save();

				Module::make($cycle->id, 'Others', 'others');

				return $cycle;
				// May need to warn users of having cycles of the same name.
			}		
		}
		
		public static function delete_by_id($id) {
			$cycle = Cycle::find($id);
			if ($cycle) {
				$cycle->delete();
				return true;
			} else {
				return false;
				// TODO: Log error
			}
		}
		
		public function get_impressions() {
			$impressions = array();
			foreach ($this->modules as $module) {
				//echo $module->name;
				foreach ($module->impressions as $impression) {
					$impressions[] = $impression;
				}	
			}
			return $impressions;
		}
		/*
		public function get_professors() {
			$professors = array();
			foreach ($this->modules as $module) {
				//echo $module->name;
				foreach ($module->professors as $professor) {
					$professors[] = $professor;
				}	
			}
			return $professors;		
		}
		*/
		
		public function get_students() {
			$students = array();
			foreach ($this->modules as $module) {
				foreach ($module->students as $student) {
					$students[] = $student;
				}	
			}
			return $students;		
		}
		
		
		
			
	}
?>