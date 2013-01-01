<?php

require_once( __DIR__ . '/PersonDatabase.class.php' );

class PHPPersonDatabase extends PersonDatabase {
	
	static public $is_filter = true;
	
	private $person_db = array();
	
	public function __construct() {
		
	}
	
	public function add($type, $ad, $session, $sitting, $line, $person, Parser $parser = NULL, $filter = TRUE) {
		if (self::$is_filter && $filter) {
			$person = $this->filter($person);
		}
		if ($person === false) {
			return;
		}
		if (!array_key_exists($person, $this->person_db)) {
			$this->person_db[$person] = array();
		}
		$db = &$this->person_db[$person];
		array_push($db, array(
			'type' => $type,
			'ad' => $ad,
			'session' => $session,
			'sitting' => $sitting,
			'line' => $line,	
			));
	}
	
	public function list_people() {
		return array_keys($this->person_db);
	}
	
	public function get_db() {
		return $this->person_db;
	}
}