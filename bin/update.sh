#!/bin/sh

git pull;
php MetaBuilder.php;
git add ../data;
git add ../meta.json;

now=`date`
git commit -m "Upload on ${now}";

git push;
