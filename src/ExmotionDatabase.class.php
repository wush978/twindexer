<?php

class ExmotionDatabase {

	private $exmotion_db = array();

	public function add_exmotion(stdClass $exmotion_obj, $person) {
		$ad = $exmotion_obj->ad;
		$session = $exmotion_obj->session;
		$sitting = $exmotion_obj->sitting;
		$line = $exmotion_obj->line;
		$key = $this->get_exmotion_name($ad, $session, $sitting, $line);
		if (array_key_exists($key, $this->exmotion_db)) {
			array_push($this->exmotion_db[$key][$exmotion_obj->type], $person);
			return;
		}
		$this->exmotion_db[$key] = array('exmotion-proposer' => array(), 'exmotion-petitioner' => array());
		array_push($this->exmotion_db[$key][$exmotion_obj->type], $person);
	}

	private function get_exmotion_name($ad, $session, $sitting, $line) {
		return "$ad:$session:$sitting:$line";
	}
	
	public function get_exmotion_db() {
		return $this->exmotion_db;
	}
}
