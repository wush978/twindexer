<?php

require_once( __DIR__ . '/../src/Schema.interface.php' );

class interp implements Schema {
	
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
	 * @var PersonDatabase
	 */
	private $db;
	
	public function __construct(Parser $parser) {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->parser = $parser;
		$this->db = $parser->get_db();
	}
	
	public function __invoke(stdClass $json_content) {
// 		$people = $json_content->people;
		foreach($json_content->people as $person) {
			$this->db->add(
				$json_content->type, 
				$this->parser->get_ad(), 
				$this->parser->get_session(), 
				$this->parser->get_sitting(), 
				$this->parser->get_line_num(), 
				$person,
				$this->parser);
		}
	}
}