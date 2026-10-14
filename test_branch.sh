#!/bin/bash

hostname=$(hostname)
testhost="VM-001"

# только для тестового сервера
if [ $hostname != $testhost ]; then
  echo "только для тестового сервера"
  exit
fi

# переход в каталог сервиса
  echo "переход в каталог сервиса"
cd /var/www/job

# удаление лишнего
  echo "удаление лишнего"
git reset --hard
git clean -df

# обновление main
  echo "обновление main"
git checkout main
git pull

# обновление dev
  echo "обновление dev"
git checkout dev
git pull

# забрать в dev изменения из main
  echo "забрать в dev изменения из main"
git merge main

# обновление ветки фичи
  echo "обновление ветки фичи"
git checkout $1
git pull

# забрать в dev изменения из фичи
  echo "забрать в dev изменения из фичи"
git checkout -
git merge $1

# бэк
  echo "бэк запуск artisan команд"
composer install --no-interaction
composer dump-autoload --no-interaction
php artisan migrate
php artisan tenants:migrate --tenants=bp
php artisan tenants:migrate --tenants=admin
php artisan optimize:clear
php artisan config:cache

# фронт
  echo "фронт запуск команд для сборки"
npm i
npm run development
cd /var/www/job/resources/js/admin
npm i
npx vite build --mode development
cd /var/www/job

# удаление лишнего
  echo "удаление лишнего"
git reset --hard
git clean -df
git push
echo "успешная сборка ... тестите на здаровие"