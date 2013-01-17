library(cluster)
tags.dist <- readRDS(file="result/exmotion-tags-dist.Rds")
g.pam <- pam(x=retval, k=length(tags)%/%100, diss=TRUE)
explanation[[g.pam$medoids[1]]]
i <- which(g.pam$clustering==1)[2]
explanation[[i]]