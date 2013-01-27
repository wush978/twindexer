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
	std::map<std::string, std::string> retval1, retval2;
//	int n = (generalPre1.size() * generalPre2.size() * titleList.size() + 1) * nameList.size();
	std::string pre, suf;
	for(int i = 0;i < nameList.size();i++) {
		std::string name(nameList[i]);
		if (retval1.end() != retval1.find(name)) {
			Rcout << name << std::endl;
			throw std::logic_error("repeat key");
		}
		retval1[name] = name;
		retval2[name] = "";
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
						std::string result1(pre), result2(pre1);
						result1.append(pre1).append(pre2).append(title).append(suf);
						if (retval1.end() != retval1.find(result1)) {
							Rcout << result1 << std::endl;
							throw std::logic_error("repeat key");
						}
						retval1[result1] = name;
						result2.append(pre2).append(title);
						retval2[result1] = result2;
					}
				}
			}
		}
	}
	List retval;
	retval["name"] = wrap(retval1);
	retval["title"] = wrap(retval2);
	return retval;
}


