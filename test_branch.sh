#!/bin/bash

hostname=$(hostname)
testhost="VM-001"

# только для тестового сервера
if [ $hostname != $testhost ]; then
  echo "not test server"
  exit
fi

# переход в каталог сервиса
cd /var/www/job

# удаление лишнего
git reset --hard
git clean -df

# обновление main
git checkout main
git pull

# обновление dev
git checkout dev
git pull

# забрать в dev изменения из main
git merge main

# обновление ветки фичи
git checkout $1
git pull

# забрать в dev изменения из фичи
git checkout -
git merge $1

# бэк
composer install
composer dump-autoload
php artisan migrate
php artisan tenants:migrate --tenants=bp
php artisan tenants:migrate --tenants=admin
php artisan optimize:clear
php artisan config:cache

# фронт
npm i
npm run development
cd /var/www/job/resources/js/admin
npm i
npx vite build --mode development
cd /var/www/job

# удаление лишнего
git reset --hard
git clean -df
