#!/bin/sh

composer update -d build/templates/library --no-dev

npm run build
