/*
 * gen_name_title.cpp
 *
 *  Created on: Jan 27, 2013
 *      Author: wush
 */

#include <Rcpp.h>

using namespace Rcpp;

Function nchar("nchar");
Function substr("substr");

typedef std::vector<std::string> StrVec;

// [[Rcpp::export]]
SEXP genNameTitle(
		const StrVec& generalPre1,
		const StrVec& generalPre2,
		const StrVec& titleList,
		const StrVec& nameList) {
	std::map<std::string, std::string> retval;
//	int n = (generalPre1.size() * generalPre2.size() * titleList.size() + 1) * nameList.size();
	std::string pre, suf;
	for(int i = 0;i < nameList.size();i++) {
		std::string name(nameList[i]);
		if (retval.end() != retval.find(name)) {
			Rcout << name << std::endl;
			throw std::logic_error("repeat key");
		}
		retval[name] = name;
		int m = as<int>(nchar(nameList[i]));
		for(int j = 0;j < m;j++) {
			pre.assign(as<std::string>(substr(wrap(nameList[i]), wrap(1), wrap(j+1))));
			suf.assign(as<std::string>(substr(wrap(nameList[i]), wrap(j+2), wrap(m))));
			for(int i1 = 0;i1 < generalPre1.size();i1++) {
				std::string pre1((generalPre1[i1]));
				for(int i2 = 0;i2 < generalPre2.size();i2++) {
					std::string pre2((generalPre2[i2]));
					for(int i3=0;i3 < titleList.size();i3++) {
						std::string title((titleList[i3]));
						std::string result(pre);
						result.append(pre1).append(pre2).append(title).append(suf);
						if (retval.end() != retval.find(result)) {
							Rcout << result << std::endl;
							throw std::logic_error("repeat key");
						}
						retval[result] = name;
					}
				}
			}
		}
	}
	return wrap(retval);
}


