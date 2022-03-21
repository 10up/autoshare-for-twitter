set -e

php -v
composer install
composer run lint
composer run setup-local-tests
composer run test
