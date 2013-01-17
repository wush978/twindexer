<?php

$exmotion_list = json_decode(file_get_contents('result/exmotion-explanation.json'));

echo <<<EOF
<html>
<body>
EOF;

foreach($exmotion_list as $title => $exmotion) {
	$exmotion = str_replace('$', '<br/>', $exmotion);
	echo <<< EOF
	<h1 id="$title"> $title </h1><br/>
	$exmotion <br/>
	<hr/> 
EOF;
	
}

echo <<<EOF
</body>
</html>
EOF;
