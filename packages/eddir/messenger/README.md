# Мессенджер

Пакет для Laravel и компоненты Vue.js.

## Установка

### Предварительные требования

* Laravel 9
* Vue 2
* MySQL 8

Можно использовать шаблон [laravel9-vite-vue2-starter](https://github.com/logue/laravel9-vite-vue2-starter):
```bash
git clone https://github.com/logue/laravel9-vite-vue2-starter.git spa-with-messenger
cd spa-with-messenger
composer install
composer update
cp .env.example .env
echo "Укажите данные для подключения к БД" && sleep 2
vim .env
php artisan key:generate
php artisan migrate --seed
```

Для работы с hot module replacement при необходимости указать origin в vite.config.js:
```js
export default defineConfig({
    server: {
        fs: {
            // Allow serving files from one level up to the project root
            allow: ['..'],
        },
        origin: 'http://your-laravel-domain.com/',
        hmr: {
            host: 'your-laravel-domain.com',
        },
    },
    ...
});
```

Выполнить настройки веб-сервера Apache / Nginx или запускать встроенный сервер:
```bash
php artisan serve
```

### Настройка сокета

Установка Laravel-websockets:
```bash
composer require pusher/pusher-php-server beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```

Отредактировать файл .env: заменить значение BROADCAST_DRIVER на pusher и добавить PUSHER (вымышленные данные, но свой ip):
```ini
BROADCAST_DRIVER=pusher
...

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

PUSHER_APP_ID=12345
PUSHER_APP_KEY=12345
PUSHER_APP_SECRET=12345
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
```

В config/app.php раскомментировать строку:
```php
App\Providers\BroadcastServiceProvider::class,
```

Для запуска веб-сокетов использовать команду:
```bash
php artisan websockets:serve
```

Открыть порт 6001.

Проверить работу веб сокета можно по адресу http://your-laravel-domain.com/laravel-websockets/

В клиентской части добавить подключение к сокету в bootstrap.ts:

```js
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
  wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
  wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
  enabledTransports: ['ws', 'wss'],
});
```

Установить зависимости:
```shell
npm install --save laravel-echo pusher-js 
```

Настроить авторизацию в routes/channels.php:
```php
Broadcast::channel('private-messenger', function () {
    return true;
});
````

### Установка пакета чата
Переместить каталог **packages** в корень проекта:
```bash
mv ./packages ./spa-with-messenger
```

Добавить пакет в composer.json строкой в конце:
```
"autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/",
            ...
            "Eddir\\Messenger\\": "packages/eddir/messenger/src/"
        }
    },
```

Применить изменения в composer.json:
```bash
composer dump-autoload
```

Зарегистрировать пакет и фасад в **config/app.php**:
```php
'providers' => [
    ...
    /*
     * Package Service Providers...
     */
    Eddir\Messenger\MessengerServiceProvider::class,
    ...
],

'aliases' => Facade::defaultAliases()->merge([
    ...
    'MessengerFacade' => Eddir\Messenger\Facades\MessengerFacade::class,
])->toArray(),
```

Запустить установку пакета:
```bash
php artisan messenger:install
```

# Клиентская часть

Выполнить установку зависимостей и запустить приложение:
```bash
npm install && npm run dev
```

Добавить компонент чата в существующий компонент приложения:
```js
import ChatApp from '@/Components/Chat/ChatApp.vue';
```
```vue
<template>
    <ChatApp />
</template>
<script>
</script>
```

## Credits

-   [Eduard Rostkov](https://github.com/eddir)

## License

The GNU GPLv3. Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
