<?php

require_once( __DIR__ . '/index.php');

$ly_index = get_index();
function search($ad, $session, $sitting) {
	global $ly_index;
	foreach($ly_index as $md_info) {
		if ($md_info['id'] === "$ad 屆 $session 會期 $sitting") {
			return $md_info['file_name'];
		}
	}
}

function report($person) {
	global $index;
	return $index[$person];
}

$index = json_decode(file_get_contents('result/index.json'), true);
$people_list = json_decode(file_get_contents('result/people-list.json'),true);

$ok = true;
foreach($people_list as $person) {
	if (preg_match('#委員#u', $person)) {
		$ok = false;
		echo "person: $person \n"; 
		$info = report($person);
		$info = $info[0];
		$md_path = 'raw/' . search($info['ad'], $info['session'], $info['sitting']);
		echo "md_path: $md_path \n";
		$md = file($md_path);
		echo $md[$info['line'] - 1];
	}
	if (preg_match('#員委#u', $person)) {
		$ok = false;
		echo "person: $person \n"; 
		$info = report($person);
		print_r($info);
		$info = $info[0];
		$md_path = 'raw/' . search($info['ad'], $info['session'], $info['sitting']);
		echo "md_path: $md_path \n";
		$md = file($md_path);
		echo $md[$info['line'] - 1];
	}
}

$filter_out = shell_exec('grep "filter out:" test.log');
$filter_out = explode("\n", $filter_out);
require_once(__DIR__ . '/src/PersonDatabase.class.php');
$check_log = '.';
foreach($filter_out as $str) {
	if (preg_match('#' . PersonDatabase::get_rule('title') . '#ui', $str, $matches)) {
		$check_log .= $str . "\n";
	}
}
file_put_contents('check_log.log', $check_log );

if (!$ok) 
	exit(1);