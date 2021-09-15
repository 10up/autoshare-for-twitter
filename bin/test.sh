set -e

php -v
composer install
composer run lint
composer run setup-local-tests
composer run test

if [[ '7.2' == $PHP_VERSION ]]; then
    WP_SNAPSHOTS_DIR=$GITHIB_WORKSPACE/.wpsnapshots/ ./vendor/bin/wpsnapshots configure --aws_key=$AWS_ACCESS_KEY --aws_secret=$SECRET_ACCESS_KEY --user_name="wp-acceptance" --user_email=travis@10up.com 10up
    composer run test:acceptance
fi;
