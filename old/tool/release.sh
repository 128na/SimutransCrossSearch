#!/bin/sh
cd `dirname $0`
cd ../

echo "|--------------------------------------------------------------------------"
echo "| current git information."
echo "|--------------------------------------------------------------------------"
git status
echo ""
echo "--------------"
git branch
echo ""
echo "--------------"
git log|head

echo ""
echo "|--------------------------------------------------------------------------"
echo "| execute git pull."
echo "|--------------------------------------------------------------------------"
git pull

echo ""
echo "|--------------------------------------------------------------------------"
echo "| updated git information."
echo "|--------------------------------------------------------------------------"
git log|head

echo ""
echo "|--------------------------------------------------------------------------"
echo "| update dependencies."
echo "|--------------------------------------------------------------------------"
php /home/simutrans/bin/composer.phar install --optimize-autoloader --no-dev

echo ""
echo "|--------------------------------------------------------------------------"
echo "| optimize app."
echo "|--------------------------------------------------------------------------"
php artisan optimize:clear
php artisan optimize
php artisan view:cache
php artisan event:cache
# ルートをキャッシュするとトップが405になる不具合
php artisan route:clear

echo ""
echo "|--------------------------------------------------------------------------"
echo "| migration status."
echo "|--------------------------------------------------------------------------"
php artisan migrate:status
