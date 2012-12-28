<?php

require_once( __DIR__ . '/PersonDatabase.interface.php' );

class PHPPersonDatabase implements PersonDatabase {
	
	private $person_db = array();
	
	public function __construct() {
		
	}
	
	public function add_approval($period, $ad, $index, $line, $person) {
		if (!array_key_exists($person, $person_db)) {
			$person_db[$person] = array();
		}
		$db = &$person_db[$person];
		array_push($db, array(
			'period' => $period,
			'ad' => $ad,
			'index' => $index,
			'line' => $line	
			));
	}
}