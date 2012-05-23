<?php
class Tracker extends ActiveRecord\Model {


	static $validates_presence_of = array(
		array('professor_id'),
		array('cycle_id'), //creator of cycle
	);
	
	
	public static function make($professor_id, $cycle_id) {
		$tracker = new Tracker();
		$tracker->professor_id = $professor_id;
		$tracker->cycle_id = $cycle_id;
		if ($tracker->is_valid()) {
			$tracker->save();
			return true;
		} else {
			return false;
		}
	}
	

	public static function delete_by_ids($professor_id, $cycle_id) {
		$tracker = Tracker::find(
			array('conditions'=>array(
				'professor_id=? AND cycle_id=?',
				$professor_id,
				$cycle_id)
			)
		);
		if ($tracker) {
			$tracker->delete();
			return $tracker;
		} else {
			return false;
		}
	}	
	

}
?>
