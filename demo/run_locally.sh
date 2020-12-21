#!/bin/sh

# This script replaces downloaded composer sourcecode of the stuart-php-library with the local one
rm -rf vendor/stuartapp/stuart-client-php/src
cp -rf ../src vendor/stuartapp/stuart-client-php/src

php index.php