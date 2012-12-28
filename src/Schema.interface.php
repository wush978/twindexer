<?php

interface Schema {
	
	public function __construct(Parser $parser);
	
	public function __invoke(stdClass $json_content);
	
}