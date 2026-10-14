# Мессенджер

Пакет для Laravel и компоненты Vue.js.

## Установка

### Предварительные требования

* Laravel 9
* Vue 2.6
* MySQL 8

Можно использовать шаблон [mazfreelance/laravel-9-boilerplate](https://github.com/mazfreelance/laravel-9-boilerplate):

### Настройка сокета

Установка Laravel-websockets:

```bash
composer require pusher/pusher-php-server:7.0.2 beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```

Отредактировать файл .env: заменить значение BROADCAST_DRIVER на pusher и добавить PUSHER (PUSHER_APP_ID строго 12345):

```ini
BROADCAST_DRIVER = pusher
...

PUSHER_APP_ID = 12345
PUSHER_APP_CLUSTER = mt1
PUSHER_APP_KEY = 12345
PUSHER_APP_SECRET = 12345
PUSHER_HOST = 127.0.0.1
PUSHER_PORT = 6001
PUSHER_SCHEME = https

VITE_PUSHER_APP_KEY = "${PUSHER_APP_KEY}"
VITE_PUSHER_HOST = "${PUSHER_HOST}"
VITE_PUSHER_PORT = "${PUSHER_PORT}"
VITE_PUSHER_SCHEME = "${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER = "${PUSHER_APP_CLUSTER}"

# указать путь до сертификата для работы в режиме wss, иначе голосовые сообщения работать не будут
LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT=
LARAVEL_WEBSOCKETS_SSL_LOCAL_PK=
```

В некоторых случаях SSL сертификат может помещать работе веб-сокетов (например, с let's encrypt). Решить проблему
можно надстройкой verify_peer в config/websockets.php:

```php
'ssl' => [
    'local_cert' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT'),
    'local_pk' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_PK'),
    'passphrase' => null,
    'verify_peer' => false,
],
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

В клиентской части добавить подключение к сокету в bootstrap.ts (forceTLS и wss необходим для доступа к возможностям navigator.mediaDevices, которые доступны только в https + wss):

```js
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  authEndpoint: '/messenger/api/chat/auth',
  key: process.env.MIX_PUSHER_APP_KEY,
  wsHost: (process).env.MIX_PUSHER_HOST ?? `ws-${process.env.MIX_PUSHER_APP_CLUSTER}.pusher.com`,
  wsPort: process.env.MIX_PUSHER_PORT ?? 443,
  wssHost: (process).env.MIX_PUSHER_HOST ?? `ws-${process.env.MIX_PUSHER_APP_CLUSTER}.pusher.com`,
  wssPort: process.env.MIX_PUSHER_PORT ?? 443,
  wsPath: process.env.MIX_PUSHER_PATH ?? '/messenger',
  wssPath: process.env.MIX_PUSHER_PATH ?? '/messenger',
  forceTLS: (process.env.MIX_PUSHER_SCHEME ?? 'http') === 'https',
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

Для файлов выполнить:

```bash
php artisan storage:link
```

# Клиентская часть

Выполнить установку зависимостей и запустить приложение:

```bash
npm install && npm run dev
```

## Компоненты

Добавить компонент чата в существующий компонент приложения. ChatApp не должен быть ограничен родительским элементов в
возможности растянуться на весь экран:

```js
import ChatApp from '@/components/Chat/ChatApp.vue';
```

```vue

<template>
  <ChatApp/>
</template>
<script>
</script>
```

Добавить компонент бокового меню чата взамен div.header__right-messages:

```js
import ChatSidePanel from '@/components/Chat/ChatSidePanel/ChatSidePanel.vue';
```

```vue
<ChatSidePanel></ChatSidePanel>
```

Добавить компонент кнопки поиска внутрь div.header__right-nav:

```js
import ChatSearchButton from '@/components/Chat/ChatSearchButton/ChatSearchButton.vue';
```

```vue
<ChatSearchButton></ChatSearchButton>
```

## Credits

- [Eduard Rostkov](https://github.com/eddir)

## License

The GNU GPLv3. Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
