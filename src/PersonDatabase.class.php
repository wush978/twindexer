<?php

require_once( __DIR__ . '/../log4php/src/main/php/Logger.php');

abstract class PersonDatabase {
	
	/**
	 *
	 * @var Logger
	 */
	private $logger;
	
	
	static private $rule_list = array();
	
	/**
	 * add an action to database
	 * @param string $type
	 * @param string $ad
	 * @param string $session
	 * @param string $sitting
	 * @param string $line
	 * @param string $person
	 * @param Parser $parser
	 * @param bool $filter
	 */
	abstract public function add($type, $ad, $session, $sitting, $line, $person, Parser $parser = NULL, $filter = TRUE);
	
	/**
	 * @return string[]
	 */
	abstract public function list_people();
	
	/**
	 * @return db result
	 */
	abstract public function get_db();
	
	public function __construct() {
		Logger::getLogger(__CLASS__);
	}
	
	protected function filter($person) {
		if (preg_match('#' . self::get_rule('last_name') . self::get_rule('title') . self::get_rule('first_name') . '$#u', $person, $match)) {
			$this->add_title($match['last_name'] . $match['first_name'], $match['title']);
			return $match['last_name'] . $match['first_name'];
		}
		if (preg_match('#^主\s{0,1}席$#u', $person, $match)) {
			$chairman = $this->filter($this->query_chairman());
			return $chairman;
		}
		if (preg_match('#' . self::get_rule('aborigine') . self::get_rule('title') . '$#u', $person, $match)) {
			$this->add_title($match['aborigine'], $match['title']);
			return $match['aborigine'];
		}
		return false;
	} 
	
	static private function get_rule($rule_name) {
		if (array_key_exists($rule_name, self::$rule_list)) {
			return self::$rule_list[$rule_name];
		}
		$rule = yaml_parse_file(__DIR__ . '/../rule/' . $rule_name . '.yml');
		self::$rule_list[$rule_name] = $rule;
		return self::$rule_list[$rule_name];
	}
	
	/**
	 * 
	 * @param string $person
	 * @param string $title
	 */
	abstract protected function add_title($person, $title);
	
	/**
	 * @return string name of chairman
	 */
	abstract protected function query_chairman();
	
}