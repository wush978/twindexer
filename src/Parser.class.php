<?php

require_once( __DIR__ . '/PersonDatabase.interface.php');

class Parser {
	
	/**
	 * 
	 * @var PersonDatabase 
	 */
	public $db;
	
	/**
	 * 
	 * @var integer
	 */
	private $line_num;
	
	/**
	 * 
	 * @var integer
	 */
	private $state;
	
	/**
	 * 
	 * @var string[]
	 */
	private $file_content;
	
	/**
	 * 
	 * @var string
	 */
	private $json_str;
	
	/**
	 * 
	 * @var string
	 */
	private $str;
	
	/**
	 * 
	 * @param PersonDatabase $db
	 */
	public function __construct(PersonDatabase $db) {
		$this->db = $db;
	}
	
	public function parse($file_name) {
		$this->file_content = file($file_name);
		$this->state = 0;
		for($this->line_num = 0;$this->line_num < count($this->file_content);$this->line_num++) {
			$this->str = $this->file_content[$this->line_num];
			switch($this->state) {
			case 0:
				$this->start_json();
				break;
			case 1:
				$this->handle_idention();
				if ($this->end_json()) {
					break;
				}
				$this->json_str .= $this->str;
				break;
			default:
				throw new Exception('TODO');
			}
		}
	}
			
	/**
	 * 
	 */
	private function start_json() {
		if (preg_match('#```json#ui', $this->str)) {
			$this->state = 1;
			$this->json_str = '';
		}
	}
	
	/**
	 * 
	 * @return bool
	 * @throws Exception
	 */
	private function end_json() {
		if (preg_match('#```#ui', $this->str)) {
			$this->state = 0;
			$json_content = json_decode($this->json_str);
			if (gettype($json_content)  !== 'object' || get_class($json_content) !== 'stdClass') {
				throw new Exception('failed to parse string at ' . $this->line_num . ' : (' . $this->json_str . ')');
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 */
	private function handle_idention() {
		if (preg_match('#^(\s{0,5}>)+\s+(?<out>\{.*)$#ui', $this->str, $matches)) {
			$this->str = $matches['out'];
		}
	}
	
}