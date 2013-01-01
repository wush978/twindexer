<?php

/**
 * 
 * TODO: fix variable name: period->ad, ...
 */

function get_index() {
	$retval = array();
	
	$index_content = file('raw/index.md');
	
	function match($pattern, $subject, &$out) {
		preg_match("/$pattern/ui", $subject, $matches);
		if (count($matches) > 0) {
			$out = $matches['out'];
		}
	}
	
	$period = '';
	$ad = '';
	$index_pattern = '(?<index>\d+)';
	$file_name_pattern = '(?<file_name>\d+.md)';
	$retval['period_range'] = array();
	$retval['ad_range'] = array();
	$retval['index_range'] = array();
	foreach($index_content as $index_line) {
		$matches = array();
		match('# 第 (?<out>\d+) 屆', $index_line, $period);
		match('# 第 (?<out>\d+) 會期', $index_line, $ad);
		preg_match_all('/(?<md>\[' . $index_pattern . '\]\(' . $file_name_pattern . '\))+/ui', $index_line, $matches);
		if (count($matches['md']) > 0) {
			for($i = 0;$i < count($matches['md']);$i++) {
				$index = $matches['index'][$i];
				$file_name = $matches['file_name'][$i];
				$retval["$period 屆 $ad 會期 $index"] = $file_name;
				array_push($retval['period_range'], $period);
				array_push($retval['ad_range'], $ad);
				if (array_key_exists("$period 屆 $ad 會期", $retval['index_range'])) {
					array_push($retval['index_range']["$period 屆 $ad 會期"], $index);
				} 
				else {
					$retval['index_range']["$period 屆 $ad 會期"] = array($index);
				}
			}
		}
	}
	$retval['period_range'] = array_values(array_unique($retval['period_range'], SORT_NUMERIC));
	$retval['ad_range'] = array_values(array_unique($retval['ad_range'], SORT_NUMERIC));
	sort($retval['period_range']);
	sort($retval['ad_range']);
	$retval_file_name = array();
	foreach($retval['period_range'] as $period) {
		foreach($retval['ad_range'] as $ad) {
			if (!array_key_exists("$period 屆 $ad 會期", $retval['index_range']))
				continue;
			foreach($retval['index_range']["$period 屆 $ad 會期"] as $index) {
				array_push($retval_file_name, array(
						'id' => "$period 屆 $ad 會期 $index", 
						'file_name' => $retval["$period 屆 $ad 會期 $index"],
						'ad' => $period,
						'session' => $ad,
						'sitting' => $index,
						));
			}
		}
	}
	return($retval_file_name);
}

