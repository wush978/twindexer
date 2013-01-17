PHP := ~/bin/bin/php -c ~/etc 

all : result/exmotion.json result/exmotion-dictionary.json result/exmotion-explanation.json result/exmotion-tags.json result/exmotion-tags-dist.Rds result/tags-pam.Rds

result/exmotion.json: exec.php
	$(PHP) exec.php

result/exmotion-dictionary.json: extract-exmotion.php result/exmotion.json
	$(PHP) extract-exmotion.php

result/exmotion-explanation.json: dictionary-to-explanation.R result/exmotion-dictionary.json
	Rscript dictionary-to-explanation.R

result/exmotion-tags.json: nltk-exmotion.py result/exmotion-explanation.json
	python nltk-exmotion.py

result/exmotion-tags-dist.Rds: tags-dist.R result/exmotion-explanation.json result/exmotion-tags.json
	Rscript tags-dist.R

result/tags-pam.Rds: tags-pam.R result/exmotion-tags-dist.Rds
	Rscript tags-pam.R

test :
	-rm test.log
	$(PHP) test.php
