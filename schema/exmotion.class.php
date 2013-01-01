<?php

require_once( __DIR__ . '/../src/Schema.interface.php' );

class exmotion implements Schema {

	/**
	 * 
	 * @var Logger
	 */
	private $logger;
	
	public function __construct(Parser $parser) {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->parser = $parser;
		$this->db = $parser->get_db();
	}
	
	public function __invoke(stdClass $json_content) {
// 		$people = $json_content->people;
		if (property_exists($json_content, 'proposer'))
			$this->add_people($json_content->proposer, $json_content->type . '-proposer');
		if (property_exists($json_content, 'petitioner'))
			$this->add_people($json_content->petitioner, $json_content->type . '-petitioner');
	}
	

	private function add_people($people, $type_name){ 
		foreach($people as $person) {
			$this->db->add(
					$type_name,
					$this->parser->get_ad(),
					$this->parser->get_session(),
					$this->parser->get_sitting(),
					$this->parser->get_line_num(),
					$person,
					$this->parser,
					FALSE);
		}
	}
	
}