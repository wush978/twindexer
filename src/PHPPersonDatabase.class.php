<?php

require_once( __DIR__ . '/PersonDatabase.class.php' );

class PHPPersonDatabase extends PersonDatabase {
	
	/**
	 *
	 * @var Logger
	 */
	private $logger;
	
	
	/**
	 * 
	 * @var Parser
	 */
	private $parser;
	
	/**
	 * is apply filter or not
	 * @var bool
	 */
	static public $is_filter = true;
	
	private $person_db = array();
	
	public function __construct() {
		parent::__construct();
		$this->logger = Logger::getLogger(__CLASS__);		
	}
	
	public function add($type, $ad, $session, $sitting, $line, $person, Parser $parser = NULL, $filter = TRUE) {
		$this->parser = $parser;
		if (self::$is_filter && $filter) {
			$person = $this->filter($person);
		}
		if ($person === false) {
			return;
		}
		$this->check_db($person);
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
	
	public function clean_db() {
		$this->person_db = array();
	}
	
	protected function add_title($person, $title) {
		$this->check_db($person);
		$db = &$this->person_db[$person];
		if (!array_key_exists('title', $db)) {
			$db['title'] = array();
		}
		$db['title'][$title] = true;
	}
	
	protected function query_chairman() {
		return $this->parser->get_speaker();
	}
	
	private function check_db($person) {
		if (!array_key_exists($person, $this->person_db)) {
			$this->person_db[$person] = array();
			$ad = $this->parser->get_ad();
			$session = $this->parser->get_session();
			$sitting = $this->parser->get_sitting();
			$line_num = $this->parser->get_line_num();
			$this->logger->trace("ad:$ad session:$session sitting:$sitting line:$line_num add person: $person");
		}
	}
}