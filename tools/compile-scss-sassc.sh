#!/bin/sh

common_arguments="--style compressed --load-path ./"

mkdir -p static/css

sassc ${common_arguments} scss/style.scss static/css/style.css

# Compress compiled stylesheets for nginx gzip_static
gzip -fk static/css/style.css
