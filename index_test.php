<?php

require_once 'index.php';

$index = get_index();

foreach($index as $md_info) {
	echo $md_info['id'] . "\n";
}