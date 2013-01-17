<?php

ini_set('memory_limit', '-1');

require_once( __DIR__ . '/index.php');

$index = get_index();

$exmotion_raw = (file_get_contents(__DIR__ . '/result/exmotion.json'));

$exmotion = json_decode($exmotion_raw);

require_once( __DIR__ . '/src/ExmotionDatabase.class.php');

$db = new ExmotionDatabase();

foreach($exmotion as $person => $exmotion_obj_list) {
	foreach($exmotion_obj_list as $exmotion_obj) {
		$db->add_exmotion($exmotion_obj, $person);
	}
}

$retval = $db->get_exmotion_db();

foreach($retval as $key => $exmotion) {
	// modifying $exmotion
	
	// add file_name
	$ad = (int) explode(":", $key)[0];
	$session = (int) explode(":", $key)[1];
	$sitting = (int) explode(":", $key)[2];
	$file_name = query_file_name($index, $ad, $session, $sitting);
	$exmotion['file_name'] = $file_name;
		
	// locate txt
	$md = file(__DIR__ . '/raw/' . $file_name);
	$line = (int) explode(":", $key)[3];
	$start_line = $line;
	while ($start_line > $line - 10 && $start_line >= 0) {
		if (preg_match("/### 第.{1,2}案/u", $md[$start_line])) {
			break;
		}
		$start_line--;
	}
	$end_line = $line;
	while( $end_line < count($md)) {
		if (preg_match("/請問院會，有無異議？/u", $md[$end_line])) {
			break;
		}
		$end_line++;
	} 
	$exmotion['content'] = implode("\n", array_slice($md, $start_line, $end_line - $start_line + 1));
	
	// extract 說明
	
	
	$retval[$key] = $exmotion;
}

file_put_contents(__DIR__ . '/result/exmotion-dictionary.json', json_encode($retval, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
