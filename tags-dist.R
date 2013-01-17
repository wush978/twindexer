library(inline)
library(Rcpp)

intersect.cpp <- cxxfunction(sig=c(Rx1="character", Rx2="character"), body='
	typedef std::vector<std::string> StrVec;
	StrVec x1(as<StrVec>(Rx1)), x2(as<StrVec>(Rx2));
	int retval = 0;
	for(int i = 0;i < x1.size();i++) {
		for(int j = 0;j < x2.size();j++) {
			if (x1[i].compare(x2[j]) == 0)
				retval++;
		}
	}
	return wrap(retval);
	', plugin='Rcpp')


library(rjson)
explanation <- fromJSON(file="result/exmotion-explanation.json")
tags <- fromJSON(file="result/exmotion-tags.json")
exmotion <- names(tags)

retval <- matrix(NA, length(tags), length(tags))
colnames(retval) <- names(tags)
rownames(retval) <- names(tags)
for(i in 1:length(tags)) {
	for(j in 1:i) {
		x <- tags[[i]]
		y <- tags[[j]]
		retval[i,j] <- intersect.cpp(x, y)
		retval[j,i] <- retval[i,j]
	}
	if (i %% 100 == 0) {
		cat(i)
		cat("\n")
	}
}
table(retval[lower.tri(retval, diag=FALSE)])
retval <- 20 - retval
saveRDS(retval, file="result/exmotion-tags-dist.Rds")
