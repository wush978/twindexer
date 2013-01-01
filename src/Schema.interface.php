<?php

require_once( __DIR__ . '/../log4php/src/main/php/Logger.php');

interface Schema {
	
	public function __construct(Parser $parser);
	
	public function __invoke(stdClass $json_content);
	
}