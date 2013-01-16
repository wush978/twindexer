<?php

require_once( __DIR__ . '/log4php/src/main/php/Logger.php');

Logger::configure('config.xml');

function my_warning_handler($errno, $errstr) {
	
	throw new Exception($errstr);
}

set_error_handler("my_warning_handler", E_ALL);

require_once( __DIR__ . '/index.php');

$index = get_index();

require_once( __DIR__ . '/src/PHPPersonDatabase.class.php');
require_once(__DIR__ . '/src/Parser.class.php');

$db = new PHPPersonDatabase();
$parser = new Parser($db, 'interp');

/* debugging block start */
/* debugging block end */

PHPPersonDatabase::$is_filter = true;

/* for specific md file start */
// $index = array(array('file_name' => '3599.md', 'id' => 123));
/* for specific md file end */

foreach($index as $index_element) {
	$file_name = $index_element['file_name'];
	$id = $index_element['id'];
	$parser->set_parser_info($index_element['ad'], $index_element['session'], $index_element['sitting']);
	$parser->parse("raw/$file_name");
}
file_put_contents('result/people-list.interp.json', json_encode($db->list_people(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents('result/interp.json', json_encode($db->get_db(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

$db->set_name_db($db->list_people());
$db->clean_db();
$parser = new Parser($db, 'exmotion');
foreach($index as $index_element) {
	$file_name = $index_element['file_name'];
	$id = $index_element['id'];
	$parser->set_parser_info($index_element['ad'], $index_element['session'], $index_element['sitting']);
	$parser->parse("raw/$file_name");
}
file_put_contents('result/people-list.exmotion.json', json_encode($db->list_people(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents('result/exmotion.json', json_encode($db->get_db(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));