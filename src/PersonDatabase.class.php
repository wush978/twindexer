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
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	public function filter($person) {
		$this->debug("filter: $person");
		/* remove space */
		$person = preg_replace('#\s#ui', '', $person);
		/* filter out typo */
		if ($this->is_src_typo($person)) {
			$this->info("src typo: $person");
			return false;
		}
		/* check aborigine with title */
		if (preg_match('#' . self::get_rule('aborigine') . self::get_rule('title') . '$#u', $person, $match)) {
			$this->add_title($match['aborigine'], $match['title']);
			$this->info("aborigine: $person with title");
			return $match['aborigine'];
		}
		/* check aborigine */
		if (preg_match('#' . self::get_rule('aborigine') . '#u', $person, $match)) {
			$this->info("aborigine: $person");
			return $match['aborigine'];
		}		
		/* check person with title */
		if (preg_match('#^' . self::get_rule('last_name') . self::get_rule('title') . self::get_rule('first_name') . '$#u', $person, $match)) {
			$this->add_title($match['last_name'] . $match['first_name'], $match['title']);
			return $match['last_name'] . $match['first_name'];
		}
		/* check chairman */
		if (preg_match('#^主\s{0,1}席$#u', $person, $match)) {
			$chairman = $this->filter($this->query_chairman());
			$this->info("$person -> $chairman");
			return $chairman;
		}
		$this->info("filter out: $person");
		return false;
	} 
	
	static public function get_rule($rule_name) {
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
	 * @return bool
	 */
	private function is_src_typo(&$person) {
		$src_typo = self::get_rule('src_typo');
		for( $i = 0;$i < count($src_typo['from']);$i++) {
			$from = $src_typo['from'][$i];
			$to = $src_typo['to'][$i];
			if (preg_match("#^$from$#ui", $person)) {
				if ($to === false) {
					return true;
				}
				$person = $to;
			}
		}
		return false;		
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
	
	/**
	 * 
	 * @param logging message $str
	 */
	private function info($str) {
		$this->logger->info($str);		
	}
	
	/**
	 * 
	 * @param logging message $str
	 */
	private function debug($str) {
		$this->logger->debug($str);
	}
	
	
}