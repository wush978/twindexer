<?php

$a = "范主任委員良銹";
preg_match('#^范主任委員\w{1,4}$#ui', $a, $matches);
print_r($matches); 