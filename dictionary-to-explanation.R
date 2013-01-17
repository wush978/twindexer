library(rjson)
exmotion_list <- fromJSON(file='result/exmotion-dictionary.json')
retval <- list()
for(exmotion_name in names(exmotion_list)) { # exmotion_name <- "4:4:14:1750"
	exmotion <- exmotion_list[[exmotion_name]]
	src <- exmotion$content
	src <- gsub(pattern="\n", replacement="$", x=src, fixed=TRUE)
	src
 	greg_result <- gregexpr(pattern="```(\\${1,4}.+)(提案人|主席：)", text=src, perl=TRUE)[[1]]
	#greg_result
	stopifnot(length(attr(greg_result, "capture.start")) == 2)
	stopifnot(greg_result != -1)
	stopifnot(attr(greg_result, "capture.start")[1] + attr(greg_result, "capture.length")[1] == attr(greg_result, "capture.start")[2])
	i <- 1
	retval[[exmotion_name]] <- substring(src, attr(greg_result, "capture.start")[i], attr(greg_result, "capture.start")[i] + attr(greg_result, "capture.length")[i] - 1)
	greg_result <- gregexpr(pattern="\\<table.*\\</table\\>", retval[[exmotion_name]], perl=TRUE)[[1]]
	if (greg_result != -1) {
		stopifnot(length(greg_result) == 1)
		retval[[exmotion_name]] <- paste(
			substring(retval[[exmotion_name]], 1, greg_result[1] - 1), 
			substring(retval[[exmotion_name]], greg_result[1] + attr(greg_result, "match.length")[1], nchar(retval[[exmotion_name]]))
			, sep="")
	}
	stopifnot(nchar(retval[[exmotion_name]]) > 10)
# retval[[exmotion_name]]
#	retval[[exmotion_name]] <- gsub("\\s+", "", x=retval[[exmotion_name]], perl=TRUE)
}
retval <- sapply(retval, function(a) paste(strsplit(a, split="\\s?\\${0,4}\\s?\\>+\\s?", perl=TRUE)[[1]], collapse=""))
retval.json <- toJSON(retval, method="C")
cat(retval.json, file="result/exmotion-explanation.json")