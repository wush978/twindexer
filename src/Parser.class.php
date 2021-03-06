<?php

require_once( __DIR__ . '/../log4php/src/main/php/Logger.php');
require_once( __DIR__ . '/PersonDatabase.class.php');
require_once( __DIR__ . '/Schema.interface.php');

class Parser {
	
	/**
	 * 
	 * @var Logger
	 */
	private $logger;
	
	/**
	 * 
	 * @var PersonDatabase 
	 */
	private $db;
	
	/**
	 * 
	 * @var integer
	 */
	private $line_num;
	
	/**
	 * 
	 * @var integer 0: normal text, 1: between ```json and ```
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
	 * path to schema
	 * @var string 
	 */
	private $schema_path_prefix;
	
	/**
	 * suffix of schema
	 * @var string
	 */
	private $schema_path_suffix;
	
	/**
	 * 
	 * @var string applied schema
	 */
	private $schema = '';
	
	/**
	 * 屆
	 * @var int
	 */
	private $ad;
	
	/**
	 * 會期
	 * @var int
	 */
	private $session;
	
	/**
	 * 次
	 * @var int
	 */
	private $sitting;
	
	/**
	 * 主席
	 * @var string
	 */
	private $speaker;
	
	/**
	 * 上一個檔案的主席
	 * @var string
	 */
	private $ex_speaker = '';
	
	/**
	 * If first ```json block or not
	 * 
	 * @var bool
	 */
	private $is_first;
	
	/**
	 * 
	 * @param PersonDatabase $db
	 */
	public function __construct(PersonDatabase $db, $schema = '') {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->db = $db;
		$this->schema_path_prefix = __DIR__ . '/../schema/';
		$this->schema_path_suffix = '.class.php';
		$this->schema = $schema;
	}
	
	public function parse($file_name) {
		$this->logger->trace("Start parsing $file_name");
		$this->file_content = file($file_name);
		$this->state = 0;
		$this->is_first = true;
		for($this->line_num = 0;$this->line_num < count($this->file_content);$this->line_num++) {
			$this->str = $this->file_content[$this->line_num];
			switch($this->state) {
			case 0:
				$this->start_json();
				break;
			case 1:
				$this->handle_idention();
				if ($this->end_json()) {
					$this->is_first = false;
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
			if ($this->is_first) {
				$this->set_gazette_info($json_content);
			}
			if(!property_exists($json_content, 'type')) {
// 				$this->set_gazette_info($json_content);
				return true;
			}
			/* @var $schema Schema */
// 			echo $json_content->type . "\n";
			if ($this->schema !== $json_content->type) { 
				return true;
			}
			$schema = $this->get_type_callback($json_content->type);
			if ($schema === false) {
				return true;
			}
			try {
				$schema($json_content, $this);
			}
			catch (Exception $e) {
				echo $e->getTraceAsString() . "\n";
				throw new Exception($e->getMessage() . ' (' . $this->get_line_num() .')');
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
	
	/**
	 * Storing current type_callback
	 * @var array
	 */
	static private $type_callback_list = array();
		
	/**
	 * @param string $type_name
	 * @return Schema or bool
	 */
	private function get_type_callback($type_name) {
		if (!array_key_exists($type_name, self::$type_callback_list)) {
			$file_name = $this->schema_path_prefix . $type_name . $this->schema_path_suffix;
			if (file_exists($file_name)) {
				require_once($file_name);
				self::$type_callback_list[$type_name] = new $type_name($this);
			}
			else {
				self::$type_callback_list[$type_name] = false;
			}
		}
		return self::$type_callback_list[$type_name];
	}
	
	/**
	 * 設定公報資訊
	 * @param stdClass $json_content
	 */
	private function set_gazette_info(stdClass $json_content) {
// 		if (!property_exists($json_content, 'ad') || 
// 				!property_exists($json_content, 'session') ||
// 				!property_exists($json_content, 'sitting') ||
// 				!property_exists($json_content, 'speaker')) {
// 			return;
// 		}
// 		$this->ad = $json_content->ad;
// 		$this->session = $json_content->session;
// 		$this->sitting = $json_content->sitting;
		if (property_exists($json_content, 'speaker')) {
			$this->ex_speaker = $this->speaker;
			$this->speaker = $json_content->speaker;			
		}
		else {
			$this->speaker = $this->ex_speaker;
			$this->info('no speaker is provieded. use ' . $this->ex_speaker . ' instead');
		}
	}
	
	public function set_parser_info($ad, $session, $sitting) {
		$this->ad = $ad;
		$this->session = $session;
		$this->sitting = $sitting;
	}
	
	/**
	 * 
	 * @return PersonDatabase
	 */
	public function get_db() {
		return $this->db;
	}
	
	/**
	 * @return string
	 */
	public function get_speaker() {
		return $this->speaker;
	}
	
	/**
	 * @return int
	 */
	public function get_ad() {
		return $this->ad; 
	}
	
	/**
	 * @return int
	 */
	public function get_session() {
		return $this->session;
	}
	
	/**
	 * @return int
	 */
	public function get_sitting() {
		return $this->sitting;
	}
	
	/**
	 * @return int
	 */
	public function get_line_num() {
		return $this->line_num;
	}
	
	/**
	 * log message
	 * @param unknown_type $str
	 */
	private function info($str) {
		$this->logger->info($str);
	}
}