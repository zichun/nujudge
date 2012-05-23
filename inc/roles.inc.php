<?php
	if (!defined('INCLUDE_SECURE')) die();

	class Roles {
		/* IT Unit guys. Has su permissions*/
		const SUPERUSER = 'superuser';
		/* Create and runs the cycle (spam email etc) */
		const COORDINATOR = 'coordinator';
		/* Has access to aggregate data */
		const PRIVILEGED_PROF = 'privileged_prof';
		/* Ensures that the data for the cycle is correct */
		const VETTER = 'vetter';
		/* Input and view his/her past impressions */
		const PROFESSOR = 'professor';
	};

	function is_role($role, $roles) {
		return in_array($role, $roles);
	}

?>