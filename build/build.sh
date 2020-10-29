#!/bin/sh

rm -rf build/templates/library/vendor
rm build/templates/library/composer.lock
mv build/templates/library/composer.json build/templates/library/composer-prod.json
mv build/templates/library/composer-dev.json build/templates/library/composer.json

composer install -d build/templates/library --no-dev

mv build/templates/library/composer.json build/templates/library/composer-dev.json
mv build/templates/library/composer-prod.json build/templates/library/composer.json

npm run build
