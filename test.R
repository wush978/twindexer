library(rjson)
exmotion_list <- fromJSON(file='result/exmotion-dictionary.json')
retval <- list()
for(exmotion_name in names(exmotion_list)) {
	exmotion <- exmotion_list[[exmotion_name]]
	src <- exmotion$content
	src <- gsub(pattern="\n", replacement="$", x=src, fixed=TRUE)
	src
 	greg_result <- gregexpr(pattern="```(\\${1,4}\\>?\\s+.+)+提案人", text=src, perl=TRUE)[[1]]
	#greg_result
	stopifnot(length(attr(greg_result, "capture.start")) == 1)
	i <- 1
	retval[[exmotion_name]] <- substring(src, attr(greg_result, "capture.start")[i], attr(greg_result, "capture.start")[i] + attr(greg_result, "capture.length")[i] - 1)
}
retval <- sapply(retval, function(a) paste(strsplit(a, split="\\s?\\${0,4}\\s?\\>+\\s?", perl=TRUE)[[1]], collapse=""))
retval.json <- toJSON(retval, method="C")
cat(retval.json, file="result/exmotion-explanation.json")