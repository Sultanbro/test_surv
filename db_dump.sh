#!/bin/bash

# переход в каталог сервиса
cd /var/www/job

# чтение переменных окружения из .env
set -a
source ./.env
set +a

# получение дампов
MYSQL_PWD=$DB_PASSWORD mysqldump -u jobtron --no-tablespace jobtron > jobtron.sql
MYSQL_PWD=$DB_PASSWORD mysqldump -u jobtron --no-tablespace tenantbp > tenantbp.sql
MYSQL_PWD=$DB_PASSWORD mysqldump -u jobtron --no-tablespace tenantadmin > tenantadmin.sql

# отправка дампов на тестовый
sshpass -p $TEST_PASSWORD rsync -ae "ssh -p $TEST_PORT -o StrictHostKeyChecking=no" ./jobtron.sql $TEST_USER@$TEST_HOST:$TEST_PATH
sshpass -p $TEST_PASSWORD rsync -ae "ssh -p $TEST_PORT -o StrictHostKeyChecking=no" ./tenantbp.sql $TEST_USER@$TEST_HOST:$TEST_PATH
sshpass -p $TEST_PASSWORD rsync -ae "ssh -p $TEST_PORT -o StrictHostKeyChecking=no" ./tenantadmin.sql $TEST_USER@$TEST_HOST:$TEST_PATH

# удаление дампов
rm ./jobtron.sql
rm ./tenantbp.sql
rm ./tenantadmin.sql