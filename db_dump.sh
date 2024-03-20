#!/bin/sh

# переход в каталог сервиса
cd /var/www/job

# чтение переменных окружения из .env
set -a
source ./.env
set +a

# получение дампов
MYSQL_PWD=$DB_PASSWORD mysqldump -u root jobtron > jobtron.sql
MYSQL_PWD=$DB_PASSWORD mysqldump -u root tenantbp > tenantbp.sql
MYSQL_PWD=$DB_PASSWORD mysqldump -u root tenantadmin > tenantadmin.sql

# отправка дампов на тестовый
sshpass -p $TEST_PASSWORD rsync -ae "ssh -p $TEST_PORT" ./jobtron.sql $TEST_USER@$TEST_HOST:$TEST_PATH
sshpass -p $TEST_PASSWORD rsync -ae "ssh -p $TEST_PORT" ./tenantbp.sql $TEST_USER@$TEST_HOST:$TEST_PATH
sshpass -p $TEST_PASSWORD rsync -ae "ssh -p $TEST_PORT" ./tenantadmin.sql $TEST_USER@$TEST_HOST:$TEST_PATH

# удаление дампов
rm ./jobtron.sql
rm ./tenantbp.sql
rm ./tenantadmin.sql