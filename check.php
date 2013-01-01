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

function check_pattern($pattern, $person, &$ok) {
	if (preg_match("#$pattern#u", $person)) {
		$ok = false;
		echo "person: $person \n";
		$info = report($person);
		$info = $info[0];
		$md_path = 'raw/' . search($info['ad'], $info['session'], $info['sitting']);
		echo "md_path: $md_path \n";
		$md = file($md_path);
		echo $md[$info['line'] - 1];
	}
}

function check($index, $people_list, &$ok) {
	
	$pattern_list = yaml_parse_file(__DIR__ . '/rule/check.yml');
	
	foreach($people_list as $person) {
		foreach($pattern_list as $pattern) {
			check_pattern($pattern, $person, $ok);
		}
	}
}

$ok = true;

$index = json_decode(file_get_contents('result/interp.json'), true);
$people_list = json_decode(file_get_contents('result/people-list.interp.json'),true);
check($index, $people_list, $ok);

$index = json_decode(file_get_contents('result/exmotion.json'), true);
$people_list = json_decode(file_get_contents('result/people-list.exmotion.json'),true);
check($index, $people_list, $ok);

require_once(__DIR__ . '/src/PersonDatabase.class.php');
$check_log = '.';
$filter_out = shell_exec('grep "filter out:" test.log');
$filter_out = explode("\n", $filter_out);
foreach($filter_out as $str) {
	if (preg_match('#' . PersonDatabase::get_rule('title') . '#ui', $str, $matches)) {
		$check_log .= $str . "\n";
	}
}
file_put_contents('check_log.log', $check_log );

exit(0);
if (!$ok) 
	exit(1);