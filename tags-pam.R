library(cluster)
tags.dist <- readRDS(file="result/exmotion-tags-dist.Rds")
k <- nrow(tags.dist)%/%100
k.diff <- k
g.pam <- pam(x=tags.dist, k=k, diss=TRUE)
#while(summary(g.pam)$silinfo$avg.width < 0.5 && k < nrow(tags.dist)%/% 2) {
while(FALSE) {
	k <- k + k.diff
	g.pam <- pam(x=tags.dist, k=k, diss=TRUE)
	print(k)
}
saveRDS(g.pam, file="result/tags-pam.Rds")