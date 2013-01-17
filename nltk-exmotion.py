#!/usr/bin/python
# -*- coding: utf-8 -*-

import json
import jieba
import jieba.analyse

file_name = 'result/exmotion-explanation.json'
json_data = open(file_name)
data = json.load(json_data)
result = { }
jieba.load_userdict("result/people-ldf.txt")
for key, value in data.iteritems():
    content = json.dumps(data[key], ensure_ascii=False)
    tags = jieba.analyse.extract_tags(content,topK=20)
    result[key] = tags
f = open('result/exmotion-tags.json', 'w')
f.write(json.dumps(result))
f.close()
#for i in range(len(data)):
#    print(json.dumps(data[i], ensure_ascii=False))

