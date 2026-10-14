#!/bin/bash

hostname=$(hostname)
testhost="jobtron"

# только для прода
if [ $hostname != $testhost ]; then
  echo "the script should be executed in the prod server"
  exit
fi

# переход в каталог сервиса
cd /var/www/job

# чтение переменных окружения из .env
set -a
source ./.env
set +a

# очистка истории телескопа, чтобы не тащить лишних 5гб на тетовую базу
php artisan telescope:clear

# копирование бд на тестовую
MYSQL_PWD=$DB_PASSWORD mysqldump -u $DB_USERNAME -B jobtron tenantbp tenantadmin > mysql -h '188.94.155.150' -P 3308 -u root -p'u96VqBrA'

# composer и миграции
sshpass -p $TEST_PASSWORD ssh $TEST_USER@$TEST_HOST 'cd /var/www/job && composer install --no-interaction && composer dump-autoload --no-interaction && php artisan migrate && php artisan tenants:migrate --tenants=bp && php artisan tenants:migrate --tenants=admin && php artisan optimize:clear && php artisan config:cache'

# front
sshpass -p $TEST_PASSWORD ssh $TEST_USER@$TEST_HOST 'cd /var/www/job && npm i && npm run development && cd /var/www/job/resources/js/admin && npm i && npx vite build --mode development'