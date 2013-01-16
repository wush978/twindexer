PHP := ~/bin/bin/php -c ~/etc 

all :

test :
	-rm test.log
	$(PHP) test.php
