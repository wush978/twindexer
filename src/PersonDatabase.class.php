<?php

abstract class PersonDatabase {
	
	/**
	 * add an action to database
	 * @param string $type
	 * @param string $ad
	 * @param string $session
	 * @param string $sitting
	 * @param string $line
	 * @param string $person
	 * @param Parser $parser
	 */
	abstract public function add($type, $ad, $session, $sitting, $line, $person, Parser $parser = NULL);
	
	/**
	 * @return string[]
	 */
	abstract public function list_people();
	
	protected function filter($person) {
		return $person;
	} 
	
}