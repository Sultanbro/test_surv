#!/bin/bash

# переход в каталог сервиса
cd /var/www/job

# чтение переменных окружения из .env
set -a
source ./.env
set +a

# восстановление баз
MYSQL_PWD=$DB_PASSWORD mysql -u root jobtron < jobtron.sql
MYSQL_PWD=$DB_PASSWORD mysql -u root tenantbp < tenantbp.sql
MYSQL_PWD=$DB_PASSWORD mysql -u root tenantadmin < tenantadmin.sql