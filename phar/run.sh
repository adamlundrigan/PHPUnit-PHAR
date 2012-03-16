#!/usr/bin/env bash

PHPUNIT_VER=${1:-"3.4.16"}

## Copy all the files
yes | rm -R ./PHPUnit 2>/dev/null
cp -R ../PHPUnit .

## Strip out require/include calls
find PHPUnit -name "*.php" -print0 | xargs -0 sed --regexp-extended --in-place "s/\s*(require|include)(_once)?[ (]+['\"][^'\"]+['\"];[\s]*//g"

## Replace @package_version@ 
find PHPUnit -name "*.php" -print0 | xargs -0 sed --regexp-extended --in-place "s/@package_version@/${PHPUNIT_VER}/g"

## Create the PHAR
/usr/bin/env php make_phar.php

## Remove all the modified source files
yes | rm -R ./PHPUnit 2>/dev/null
