PHP := ~/bin/bin/php -c ~/etc 

all : result/exmotion.json result/people-dictionary.json result/people-dictionary.zip
	
# result/exmotion-dictionary.json result/exmotion-explanation.json result/exmotion-tags.json result/exmotion-tags-dist.Rds result/tags-pam.Rds result/exmotion-people-dist.Rds result/people-pam.Rds

result/people-pam.Rds: people-pam.R result/exmotion-people-dist.Rds
	Rscript people-pam.R

result/exmotion-people-dist.Rds: people-dist.R result/exmotion-dictionary.json
	Rscript people-dist.R

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

result/people-dictionary.json: gen_name_title.php result/gen_name_title.txt
	$(PHP) gen_name_title.php

result/gen_name_title.txt: gen_name_title.R gen_name_title.cpp
	Rscript gen_name_title.R > result/gen_name_title.txt

result/people-dictionary.zip: result/people-dictionary.json
	-rm $@
	7z a $@ $<

test :
	-rm test.log
	$(PHP) test.php
