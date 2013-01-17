<?php

$last_name = file('last_name.txt');

$retval = array();
$index = 0;
for($i = 0;$i < count($last_name);$i++) {
	if (preg_match('#^(?<index>\w{1,2})劃：(?<last_name>.*)。#ui', $last_name[$i], $match)) {
// 		echo $match['index'] . ' : ' . $match['last_name'] . "\n";
		foreach(explode('、', $match['last_name']) as $word) {
			if (preg_match('#（.*#ui', $word))
				continue;
			if (strlen($word) == 0) 
				continue;
			$retval[$word] = strlen($word);			
		}
	}
}

arsort($retval);

foreach ($retval as $word => $word_len) {
	echo $word . '|';
}
echo "\n";