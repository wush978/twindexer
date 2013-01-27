general_prefix1 = c('', '代理', '代', '兼')
general_prefix2 = c('', '副')

title_list = c(
		'政務委員',
		'主任委員',
		'委員',
		'院長',
		'次長',
		'政務次長',
		'部長',
		'署長',
		'局長',
		'主計長',
		'長計長',
		'秘書長',
		'執行長',
		'總裁',
		'主任',
		'召集人')

library(rjson)
people_list = fromJSON(file='result/people-list.interp.json')

# ppl_title_list = c()
# for(name in people_list) {
# 	ppl_title_list = c(ppl_title_list, name)
# 	for(i in 1:nchar(name)) {
# 		pre <- substr(name, 1, i)
# 		suf <- substr(name, i+1, nchar(name))
# 		for(pre1 in general_prefix1) {
# 			for(pre2 in general_prefix2) {
# 				for(title in title_list) {
# 					title <- sprintf("%s%s%s", pre1, pre2, title)
# 					result <- sprintf('%s%s%s', pre, title, suf)
# 					ppl_title_list = c(ppl_title_list, result)
# 				}
# 			}
# 		}
# 	}
# }
# print(result)

library(Rcpp)
sourceCpp("gen_name_title.cpp")
result <- genNameTitle(general_prefix1, general_prefix2, title_list, people_list)
for(i in 1:length(result)) {
	cat(sprintf("%s,%s\n", names(result)[i], result[i]))
}