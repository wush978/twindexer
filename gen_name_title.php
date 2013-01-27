<?php

//system("Rscript gen_name_title.R > temp.txt");
$content = file("result/gen_name_title.txt");
$retval = array();
for($i = 0;$i < count($content);$i++) {
	$dictionary_element = explode(',', $content[$i]);
// 	print_r($dictionary_element);
	$retval[$dictionary_element[0]] = str_replace("\n", "", $dictionary_element[1]);
}
file_put_contents('result/people-dictionary.json', json_encode($retval, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));