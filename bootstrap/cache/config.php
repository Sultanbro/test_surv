<?php return array (
  'app' => 
  array (
    'name' => 'jobtron',
    'file' => 
    array (
      'path' => 'files',
    ),
    'pagination' => 
    array (
      'articles' => 
      array (
        'page' => 1,
        'per_page' => 5,
      ),
      'birthdays' => 
      array (
        'page' => 1,
        'per_page' => 10,
      ),
    ),
    'upload' => 
    array (
      'path' => 'uploads',
    ),
    'env' => 'dev',
    'debug' => 'true',
    'url' => 'http://localhost',
    'domain' => 'localhost',
    'timezone' => 'UTC',
    'locale' => 'ru',
    'fallback_locale' => 'en',
    'key' => 'base64:HV4hwYYthwlGg8L9jsF5+FN1J9q5Fw6fu+Vyfvmmg+c=',
    'cipher' => 'AES-256-CBC',
    'log' => 'single',
    'log_level' => 'emergency',
    'debug_blacklist' => 
    array (
      '_COOKIE' => 
      array (
      ),
      '_SERVER' => 
      array (
        0 => 'TERM_SESSION_ID',
        1 => 'CommonProgramW6432',
        2 => 'ProgramW6432',
        3 => 'PYENV_ROOT',
        4 => 'USERNAME',
        5 => 'ALLUSERSPROFILE',
        6 => 'USERPROFILE',
        7 => 'PROCESSOR_REVISION',
        8 => 'PYENV_HOME',
        9 => 'IDEA_INITIAL_DIRECTORY',
        10 => 'FPS_BROWSER_APP_PROFILE_STRING',
        11 => 'PUBLIC',
        12 => 'Path',
        13 => 'DriverData',
        14 => 'HOMEDRIVE',
        15 => 'SESSIONNAME',
        16 => 'LOGONSERVER',
        17 => 'TERMINAL_EMULATOR',
        18 => 'INTEL_DEV_REDIST',
        19 => 'MIC_LD_LIBRARY_PATH',
        20 => 'HOMEPATH',
        21 => 'SystemRoot',
        22 => 'LOCALAPPDATA',
        23 => 'APPDATA',
        24 => 'ADSK_3DSMAX_x64_2022',
        25 => 'PROCESSOR_IDENTIFIER',
        26 => 'PATHEXT',
        27 => 'PSModulePath',
        28 => 'ProgramFiles(x86)',
        29 => 'PYENV',
        30 => 'OS',
        31 => 'PROCESSOR_ARCHITECTURE',
        32 => 'NUMBER_OF_PROCESSORS',
        33 => 'ComSpec',
        34 => 'PROCESSOR_LEVEL',
        35 => 'RG_GPU_FRAMEWORK_ENGINE_RESOURCEDIR',
        36 => 'windir',
        37 => 'USERDOMAIN_ROAMINGPROFILE',
        38 => 'ProgramFiles',
        39 => 'TEMP',
        40 => 'TMP',
        41 => 'CommonProgramFiles(x86)',
        42 => 'OneDrive',
        43 => 'OneDriveConsumer',
        44 => 'USERDOMAIN',
        45 => 'SystemDrive',
        46 => 'COMPUTERNAME',
        47 => 'ProgramData',
        48 => 'FPS_BROWSER_USER_PROFILE_STRING',
        49 => 'KOMPAS_SDK',
        50 => 'CommonProgramFiles',
        51 => '__INTELLIJ_COMMAND_HISTFILE__',
        52 => 'PHP_SELF',
        53 => 'SCRIPT_NAME',
        54 => 'SCRIPT_FILENAME',
        55 => 'PATH_TRANSLATED',
        56 => 'DOCUMENT_ROOT',
        57 => 'REQUEST_TIME_FLOAT',
        58 => 'REQUEST_TIME',
        59 => 'argv',
        60 => 'argc',
        61 => 'SHELL_VERBOSITY',
        62 => 'APP_NAME',
        63 => 'APP_ENV',
        64 => 'APP_KEY',
        65 => 'APP_DEBUG',
        66 => 'DEBUGBAR_ENABLED',
        67 => 'APP_LOG_LEVEL',
        68 => 'LOG_CHANNEL',
        69 => 'APP_URL',
        70 => 'APP_DOMAIN',
        71 => 'DOMAIN_NAME',
        72 => 'TENANT_DEFAULT',
        73 => 'CALLIBRO_URL',
        74 => 'QUEUE_DRIVER',
        75 => 'DB_CONNECTION',
        76 => 'DB_PORT',
        77 => 'DB_HOST',
        78 => 'DB_DATABASE',
        79 => 'DB_USERNAME',
        80 => 'DB_PASSWORD',
        81 => 'DB_HOST_CALLIBRO',
        82 => 'DB_DATABASE_CALLIBRO',
        83 => 'DB_USERNAME_CALLIBRO',
        84 => 'DB_PASSWORD_CALLIBRO',
        85 => 'MAIL_DRIVER',
        86 => 'MAIL_HOST',
        87 => 'MAIL_PORT',
        88 => 'MAIL_USERNAME',
        89 => 'MAIL_PASSWORD',
        90 => 'MAIL_ENCRYPTION',
        91 => 'MAIL_FROM_ADDRESS',
        92 => 'MAIL_FROM_NAME',
        93 => 'CACHE_DRIVER',
        94 => 'MEDIASEND_SUPPORT_EMAIL',
        95 => 'ONEPAY_USERNAME',
        96 => 'ONEPAY_PASSWORD',
        97 => 'ONEPAY_SERVICEID',
        98 => 'ONEPAY_ACCOUNT',
        99 => 'VIEW_VERSION',
        100 => 'BROADCAST_DRIVER',
        101 => 'VITE_PUSHER_APP_KEY',
        102 => 'VITE_PUSHER_HOST',
        103 => 'VITE_PUSHER_PORT',
        104 => 'VITE_PUSHER_SCHEME',
        105 => 'VITE_PUSHER_APP_CLUSTER',
        106 => 'PUSHER_APP_ID',
        107 => 'PUSHER_APP_KEY',
        108 => 'PUSHER_APP_SECRET',
        109 => 'PUSHER_HOST',
        110 => 'PUSHER_PORT',
        111 => 'MESSENGER_STORAGE_DISK',
        112 => 'GOOGLE_RECAPTCHA_KEY',
        113 => 'GOOGLE_RECAPTCHA_SECRET',
        114 => 'YOOKASSA_MERCHANT_ID',
        115 => 'YOOKASSA_SECRET_KEY',
        116 => 'PAYMENT_FOR_ONE_PERSON',
        117 => 'PRODAMUS_SHOP_ID',
        118 => 'PRODAMUS_SHOP_KEY',
        119 => 'WALLET1_SHOP_ID',
        120 => 'WALLET1_SHOP_KEY',
      ),
      '_ENV' => 
      array (
        0 => 'SHELL_VERBOSITY',
        1 => 'APP_NAME',
        2 => 'APP_ENV',
        3 => 'APP_KEY',
        4 => 'APP_DEBUG',
        5 => 'DEBUGBAR_ENABLED',
        6 => 'APP_LOG_LEVEL',
        7 => 'LOG_CHANNEL',
        8 => 'APP_URL',
        9 => 'APP_DOMAIN',
        10 => 'DOMAIN_NAME',
        11 => 'TENANT_DEFAULT',
        12 => 'CALLIBRO_URL',
        13 => 'QUEUE_DRIVER',
        14 => 'DB_CONNECTION',
        15 => 'DB_PORT',
        16 => 'DB_HOST',
        17 => 'DB_DATABASE',
        18 => 'DB_USERNAME',
        19 => 'DB_PASSWORD',
        20 => 'DB_HOST_CALLIBRO',
        21 => 'DB_DATABASE_CALLIBRO',
        22 => 'DB_USERNAME_CALLIBRO',
        23 => 'DB_PASSWORD_CALLIBRO',
        24 => 'MAIL_DRIVER',
        25 => 'MAIL_HOST',
        26 => 'MAIL_PORT',
        27 => 'MAIL_USERNAME',
        28 => 'MAIL_PASSWORD',
        29 => 'MAIL_ENCRYPTION',
        30 => 'MAIL_FROM_ADDRESS',
        31 => 'MAIL_FROM_NAME',
        32 => 'CACHE_DRIVER',
        33 => 'MEDIASEND_SUPPORT_EMAIL',
        34 => 'ONEPAY_USERNAME',
        35 => 'ONEPAY_PASSWORD',
        36 => 'ONEPAY_SERVICEID',
        37 => 'ONEPAY_ACCOUNT',
        38 => 'VIEW_VERSION',
        39 => 'BROADCAST_DRIVER',
        40 => 'VITE_PUSHER_APP_KEY',
        41 => 'VITE_PUSHER_HOST',
        42 => 'VITE_PUSHER_PORT',
        43 => 'VITE_PUSHER_SCHEME',
        44 => 'VITE_PUSHER_APP_CLUSTER',
        45 => 'PUSHER_APP_ID',
        46 => 'PUSHER_APP_KEY',
        47 => 'PUSHER_APP_SECRET',
        48 => 'PUSHER_HOST',
        49 => 'PUSHER_PORT',
        50 => 'MESSENGER_STORAGE_DISK',
        51 => 'GOOGLE_RECAPTCHA_KEY',
        52 => 'GOOGLE_RECAPTCHA_SECRET',
        53 => 'YOOKASSA_MERCHANT_ID',
        54 => 'YOOKASSA_SECRET_KEY',
        55 => 'PAYMENT_FOR_ONE_PERSON',
        56 => 'PRODAMUS_SHOP_ID',
        57 => 'PRODAMUS_SHOP_KEY',
        58 => 'WALLET1_SHOP_ID',
        59 => 'WALLET1_SHOP_KEY',
      ),
    ),
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Illuminate\\View\\ViewServiceProvider',
      23 => 'App\\Providers\\AppServiceProvider',
      24 => 'App\\Providers\\AuthServiceProvider',
      25 => 'App\\Providers\\BroadcastServiceProvider',
      26 => 'App\\Providers\\EventServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
      28 => 'App\\Providers\\TenancyServiceProvider',
      29 => 'App\\Providers\\TelescopeServiceProvider',
      30 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      31 => 'App\\Providers\\HelperServiceProvider',
      32 => 'App\\Providers\\RepositoryServiceProvider',
      33 => 'Eddir\\Messenger\\MessengerServiceProvider',
      34 => 'App\\Providers\\ObserverServiceProvider',
      35 => 'App\\Providers\\MailingNotificationProvider',
      36 => 'App\\Providers\\RegisterFacadeProvider',
      37 => 'App\\Providers\\MailingNotificationProvider',
      38 => 'App\\Providers\\FacadeServiceProvider',
      39 => 'App\\Providers\\ReferralServicesProvider',
      40 => 'App\\Providers\\SalaryServicesProvider',
      41 => 'App\\Providers\\AnalyticServicesProvider',
      42 => 'App\\Providers\\SmsServiceProvider',
      43 => 'App\\Providers\\FileServiceProvider',
      44 => 'App\\Providers\\PaymentServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'MessengerFacade' => 'Eddir\\Messenger\\Facades\\MessengerFacade',
      'Referring' => 'App\\Facade\\Referring',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
  ),
  'bitrix' => 
  array (
    'token' => 'esm1j37wdgx6xh4r',
    'host' => 'https://infinitys.bitrix24.kz/rest/66/',
    'notification' => 
    array (
      'token' => 'esm1j37wdgx6xh4r',
      'host' => 'https://infinitys.bitrix24.kz/rest/66/',
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'pusher',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '12345',
        'secret' => '12345',
        'app_id' => '12345',
        'options' => 
        array (
          'cluster' => NULL,
          'host' => '127.0.0.1',
          'port' => '6001',
          'scheme' => 'https',
          'useTLS' => false,
          'encrypted' => false,
          'disableStats' => true,
          'curl_options' => 
          array (
            81 => 0,
            64 => 0,
          ),
          'client_options' => 
          array (
            'verify' => false,
            'verify_peer' => false,
          ),
        ),
        'curl_options' => 
        array (
          81 => 0,
          64 => 0,
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'array',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'central' => 
      array (
        'driver' => 'file',
        'path' => 'D:\\surv\\storage/tenantbp\\framework/cache/data',
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'D:\\surv\\storage/tenantbp\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
    'prefix' => 'jobtron_cache',
  ),
  'chunk-upload' => 
  array (
    'storage' => 
    array (
      'chunks' => 'chunks',
      'disk' => 'local',
    ),
    'clear' => 
    array (
      'timestamp' => '-3 HOURS',
      'schedule' => 
      array (
        'enabled' => true,
        'cron' => '25 * * * *',
      ),
    ),
    'chunk' => 
    array (
      'name' => 
      array (
        'use' => 
        array (
          'session' => true,
          'browser' => false,
        ),
      ),
    ),
    'handlers' => 
    array (
      'custom' => 
      array (
      ),
      'override' => 
      array (
      ),
    ),
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => '*',
    ),
    'allowed_methods' => 
    array (
      0 => 'GET',
      1 => 'POST',
      2 => 'PUT',
      3 => 'DELETE',
    ),
    'allowed_origins' => 
    array (
      0 => '*.jobtron.org',
      1 => 'job.bpartners.kz',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'database' => 
  array (
    'default' => 'tenant',
    'connections' => 
    array (
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'jobtron',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => NULL,
        'timezone' => '+00:00',
        'options' => 
        array (
          20 => true,
        ),
      ),
      'testing' => 
      array (
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'jobtron',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => NULL,
        'timezone' => '+00:00',
      ),
      'tenant' => 
      array (
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'tenantbp',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => NULL,
        'timezone' => '+00:00',
        'options' => 
        array (
          20 => true,
        ),
      ),
      'sqlsrv_anviz' => 
      array (
        'driver' => 'sqlsrv',
        'host' => '127.0.0.1',
        'port' => '1433',
        'database' => 'forge',
        'username' => 'forge',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
      ),
      'bpartners_db' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'forge',
        'username' => 'forge',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
      ),
      'callibro' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'forge',
        'username' => 'forge',
        'password' => NULL,
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
      ),
      'call_center' => 
      array (
        'driver' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => '5432',
        'database' => 'forge',
        'username' => 'forge',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => 6379,
        'database' => 0,
      ),
    ),
  ),
  'debugbar' => 
  array (
    'enabled' => false,
    'except' => 
    array (
    ),
    'storage' => 
    array (
      'enabled' => true,
      'driver' => 'file',
      'path' => 'D:\\surv\\storage/tenantbp\\debugbar',
      'connection' => NULL,
      'provider' => '',
    ),
    'include_vendors' => true,
    'capture_ajax' => true,
    'add_ajax_timing' => false,
    'error_handler' => false,
    'clockwork' => false,
    'collectors' => 
    array (
      'phpinfo' => true,
      'messages' => true,
      'time' => true,
      'memory' => true,
      'exceptions' => true,
      'log' => true,
      'db' => true,
      'views' => true,
      'route' => true,
      'auth' => true,
      'gate' => true,
      'session' => true,
      'symfony_request' => true,
      'mail' => true,
      'laravel' => false,
      'events' => false,
      'default_request' => false,
      'logs' => false,
      'files' => false,
      'config' => false,
      'cache' => false,
    ),
    'options' => 
    array (
      'auth' => 
      array (
        'show_name' => true,
      ),
      'db' => 
      array (
        'with_params' => true,
        'backtrace' => true,
        'timeline' => false,
        'explain' => 
        array (
          'enabled' => false,
          'types' => 
          array (
            0 => 'SELECT',
          ),
        ),
        'hints' => true,
      ),
      'mail' => 
      array (
        'full_log' => false,
      ),
      'views' => 
      array (
        'data' => false,
      ),
      'route' => 
      array (
        'label' => true,
      ),
      'logs' => 
      array (
        'file' => NULL,
      ),
      'cache' => 
      array (
        'values' => true,
      ),
    ),
    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_domain' => NULL,
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'D:\\surv\\storage/tenantbp/tenantbp/app/',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'D:\\surv\\storage/tenantbp/tenantbp/app/public/',
        'url' => 'http://localhost/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => 'O4493_admin',
        'secret' => 'nzxk4iNukQWx',
        'region' => 'us-east-1',
        'bucket' => 'video',
        'endpoint' => 'https://storage.oblako.kz:443',
        'use_path_style_endpoint' => true,
        'throw' => true,
        'visibility' => 'public',
        'scheme' => 'http',
      ),
      'ftp' => 
      array (
        'driver' => 'ftp',
        'host' => 'storage.oblako.kz',
        'username' => 'O4493.O4493_admin',
        'password' => 'nzxk4iNukQWx',
        'root' => '/',
        'port' => 21,
        'passive' => true,
        'ssl' => false,
        'timeout' => 900,
      ),
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'larecipe' => 
  array (
    'docs' => 
    array (
      'route' => '/docs',
      'path' => '/resources/docs',
      'landing' => 'overview',
      'middleware' => 
      array (
        0 => 'web',
      ),
    ),
    'versions' => 
    array (
      'default' => '1.0',
      'published' => 
      array (
        0 => '1.0',
      ),
    ),
    'settings' => 
    array (
      'auth' => false,
      'guard' => NULL,
      'ga_id' => '',
      'middleware' => 
      array (
        0 => 'web',
      ),
    ),
    'cache' => 
    array (
      'enabled' => false,
      'period' => 5,
    ),
    'search' => 
    array (
      'enabled' => false,
      'default' => 'algolia',
      'engines' => 
      array (
        'internal' => 
        array (
          'index' => 
          array (
            0 => 'h2',
            1 => 'h3',
          ),
        ),
        'algolia' => 
        array (
          'key' => '',
          'index' => '',
        ),
      ),
    ),
    'ui' => 
    array (
      'code_theme' => 'dark',
      'fav' => '',
      'fa_v4_shims' => true,
      'show_side_bar' => true,
      'colors' => 
      array (
        'primary' => '#787AF6',
        'secondary' => '#2b9cf2',
      ),
      'theme_order' => NULL,
    ),
    'seo' => 
    array (
      'author' => '',
      'description' => '',
      'keywords' => '',
      'og' => 
      array (
        'title' => '',
        'type' => 'article',
        'url' => '',
        'image' => '',
        'description' => '',
      ),
    ),
    'forum' => 
    array (
      'enabled' => false,
      'default' => 'disqus',
      'services' => 
      array (
        'disqus' => 
        array (
          'site_name' => '',
        ),
      ),
    ),
    'packages' => 
    array (
      'path' => 'larecipe-components',
    ),
    'blade-parser' => 
    array (
      'regex' => 
      array (
        'code-blocks' => 
        array (
          'match' => '/\\<pre\\>(.|\\n)*?<\\/pre\\>/',
          'replacement' => '<code-block>',
        ),
      ),
    ),
  ),
  'logging' => 
  array (
    'default' => 'daily',
    'deprecations' => 
    array (
      'channel' => 'null',
      'trace' => false,
    ),
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'slackNotification' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'level' => 'info',
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'D:\\surv\\storage/tenantbp\\logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'D:\\surv\\storage/tenantbp\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
          'connectionString' => 'tls://:',
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'D:\\surv\\storage/tenantbp\\logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'driver' => 'log',
    'host' => 'smtp.timeweb.ru',
    'port' => '465',
    'from' => 
    array (
      'address' => '',
      'name' => 'Jobtron',
    ),
    'encryption' => 'ssl',
    'username' => '',
    'password' => '',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'D:\\surv\\resources\\views/vendor/mail',
      ),
    ),
  ),
  'messenger' => 
  array (
    'name' => 'MessengerFacade',
    'storage_disk_name' => 's3',
    'routes' => 
    array (
      'prefix' => 'messenger/api',
      'middleware' => 
      array (
        0 => 'web',
        1 => 'auth',
        2 => 'tenant',
      ),
      'namespace' => 'Eddir\\Messenger\\Http\\Controllers',
    ),
    'pusher' => 
    array (
      'key' => '12345',
      'secret' => '12345',
      'app_id' => '12345',
      'options' => 
      array (
        'cluster' => NULL,
        'host' => '127.0.0.1',
        'port' => '6001',
        'scheme' => 'https',
        'useTLS' => false,
        'encrypted' => false,
        'disableStats' => true,
        'curl_options' => 
        array (
          81 => 0,
          64 => 0,
        ),
      ),
    ),
    'user_avatar' => 
    array (
      'folder' => 'users-avatar',
      'default' => NULL,
    ),
    'gravatar' => 
    array (
      'enabled' => false,
      'image_size' => 200,
      'imageset' => 'identicon',
    ),
    'attachments' => 
    array (
      'folder' => 'attachments',
      'download_route_name' => 'attachments.download',
      'allowed_images' => 
      array (
        0 => 'png',
        1 => 'jpg',
        2 => 'jpeg',
        3 => 'gif',
      ),
      'allowed_files' => 
      array (
        0 => 'zip',
        1 => 'rar',
        2 => 'txt',
      ),
      'max_upload_size' => 150,
    ),
    'colors' => 
    array (
      0 => '#2180f3',
      1 => '#2196F3',
      2 => '#00BCD4',
      3 => '#3F51B5',
      4 => '#673AB7',
      5 => '#4CAF50',
      6 => '#FFC107',
      7 => '#FF9800',
      8 => '#ff2522',
      9 => '#9C27B0',
    ),
  ),
  'payment' => 
  array (
    'payment_for_one_person_kzt' => 980.0,
    'payment_for_one_person_rub' => 200.0,
    'prodamus' => 
    array (
      'payment_url' => 'https://bp.proeducation.kz/',
      'secret_key' => 'ce5169b490209093ba24359f2beb6cf6b0914badc326c7788528645dd1fe6859',
      'success_url' => 'https://exmaple.com',
      'failed_url' => 'https://exmaple.com',
    ),
    'wallet1' => 
    array (
      'payment_url' => 'https://wl.walletone.com/checkout/checkout/Index',
      'merchant_id' => '164796334920',
      'shop_key' => '725769627b54636264666a30776b66565f5b7952556873514f674d',
      'success_url' => 'https://exmaple.com',
      'failed_url' => 'https://exmaple.com',
    ),
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'Spatie\\Permission\\Models\\Role',
    ),
    'table_names' => 
    array (
      'roles' => 'roles',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'role_pivot_key' => NULL,
      'permission_pivot_key' => NULL,
      'model_morph_key' => 'model_id',
      'team_foreign_key' => 'team_id',
    ),
    'register_permission_check_method' => true,
    'teams' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,
    'cache' => 
    array (
      'expiration_time' => 
      \DateInterval::__set_state(array(
         'from_string' => true,
         'date_string' => '24 hours',
      )),
      'key' => 'spatie.permission.cache',
      'store' => 'default',
    ),
  ),
  'queue' => 
  array (
    'default' => 'database',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'connection' => 'mysql',
        'queue' => 
        array (
          0 => 'default',
          1 => 'mail',
        ),
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
      ),
    ),
    'failed' => 
    array (
      'database' => 'mysql',
      'table' => 'failed_jobs',
      'driver' => 'database-uuids',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'model' => 'App\\User',
      'key' => NULL,
      'secret' => NULL,
    ),
    'onepay' => 
    array (
      'username' => 'user',
      'password' => 'asdasd',
      'serviceID' => '4321',
      'account' => 'MediaSend',
    ),
    'recaptcha' => 
    array (
      'key' => '',
      'secret' => '',
    ),
    'intellect' => 
    array (
      'message_webhook' => 'https://connect.intellectdialog.com/api/w/event/c10977c8-2b3b-400b-b870-b21c8953cd2e',
      'contract_link' => 'https://bpartners.kz/bcontract?hash=',
      'time_link' => 'https://bpartners.kz/btime?hash=',
    ),
    'u-call' => 
    array (
      'api_key' => '43f499f2afc066932de75ac379bb4688',
      'app_id' => 4570705,
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 1500,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => 'D:\\surv\\storage/tenantbp\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'jobtron_session_bp',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => NULL,
  ),
  'snipe' => 
  array (
    'snapshot-location' => 'D:\\surv\\vendor/drfraker/snipe-migrations/snapshots/snipe_snapshot.sql',
    'snipefile-location' => 'D:\\surv\\vendor/drfraker/snipe-migrations/snapshots/.snipe',
    'seed-database' => false,
    'seed-class' => 'DatabaseSeeder',
    'binaries' => 
    array (
      'mysql' => 'mysql',
      'mysqldump' => 'mysqldump',
    ),
  ),
  'tariffs' => 
  array (
    'free' => 
    array (
      'id' => 1,
      'users_limit' => 5,
      'prices' => 
      array (
        'kzt' => 0,
        'rub' => 0,
      ),
      'sale_percent' => 0,
    ),
    'base' => 
    array (
      'sale_percent' => 20,
      'id' => 2,
      'users_limit' => 20,
      'prices' => 
      array (
        'kzt' => 7158,
        'rub' => 1827,
      ),
    ),
    'standard' => 
    array (
      'sale_percent' => 20,
      'id' => 3,
      'users_limit' => 50,
      'prices' => 
      array (
        'kzt' => 22966,
        'rub' => 5863,
      ),
    ),
    'pro' => 
    array (
      'sale_percent' => 20,
      'id' => 4,
      'users_limit' => 100,
      'prices' => 
      array (
        'kzt' => 85004,
        'rub' => 21073,
      ),
    ),
  ),
  'telescope' => 
  array (
    'domain' => NULL,
    'path' => 'telescope',
    'driver' => 'database',
    'storage' => 
    array (
      'database' => 
      array (
        'connection' => 'mysql',
        'chunk' => 1000,
      ),
    ),
    'enabled' => true,
    'middleware' => 
    array (
      0 => 'web',
      1 => 'Laravel\\Telescope\\Http\\Middleware\\Authorize',
    ),
    'only_paths' => 
    array (
    ),
    'ignore_paths' => 
    array (
      0 => 'nova-api*',
    ),
    'ignore_commands' => 
    array (
    ),
    'watchers' => 
    array (
      'Laravel\\Telescope\\Watchers\\BatchWatcher' => true,
      'Laravel\\Telescope\\Watchers\\CacheWatcher' => 
      array (
        'enabled' => true,
        'hidden' => 
        array (
        ),
      ),
      'Laravel\\Telescope\\Watchers\\ClientRequestWatcher' => true,
      'Laravel\\Telescope\\Watchers\\CommandWatcher' => 
      array (
        'enabled' => true,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Telescope\\Watchers\\DumpWatcher' => 
      array (
        'enabled' => true,
        'always' => false,
      ),
      'Laravel\\Telescope\\Watchers\\EventWatcher' => 
      array (
        'enabled' => true,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Telescope\\Watchers\\ExceptionWatcher' => true,
      'Laravel\\Telescope\\Watchers\\GateWatcher' => 
      array (
        'enabled' => true,
        'ignore_abilities' => 
        array (
        ),
        'ignore_packages' => true,
      ),
      'Laravel\\Telescope\\Watchers\\JobWatcher' => true,
      'Laravel\\Telescope\\Watchers\\LogWatcher' => true,
      'Laravel\\Telescope\\Watchers\\MailWatcher' => true,
      'Laravel\\Telescope\\Watchers\\ModelWatcher' => 
      array (
        'enabled' => true,
        'events' => 
        array (
          0 => 'eloquent.*',
        ),
        'hydrations' => true,
      ),
      'Laravel\\Telescope\\Watchers\\NotificationWatcher' => true,
      'Laravel\\Telescope\\Watchers\\QueryWatcher' => 
      array (
        'enabled' => true,
        'ignore_packages' => true,
        'slow' => 500,
      ),
      'Laravel\\Telescope\\Watchers\\RedisWatcher' => true,
      'Laravel\\Telescope\\Watchers\\RequestWatcher' => 
      array (
        'enabled' => true,
        'size_limit' => 64,
        'ignore_http_methods' => 
        array (
        ),
        'ignore_status_codes' => 
        array (
        ),
      ),
      'Laravel\\Telescope\\Watchers\\ScheduleWatcher' => true,
      'Laravel\\Telescope\\Watchers\\ViewWatcher' => true,
    ),
  ),
  'tenancy' => 
  array (
    'tenant_model' => 'App\\Models\\Tenant',
    'id_generator' => 'Stancl\\Tenancy\\UUIDGenerator',
    'domain_model' => 'Stancl\\Tenancy\\Database\\Models\\Domain',
    'central_domains' => 
    array (
      0 => 'localhost',
    ),
    'bootstrappers' => 
    array (
      0 => 'Stancl\\Tenancy\\Bootstrappers\\DatabaseTenancyBootstrapper',
      1 => 'Stancl\\Tenancy\\Bootstrappers\\CacheTenancyBootstrapper',
      2 => 'Stancl\\Tenancy\\Bootstrappers\\FilesystemTenancyBootstrapper',
      3 => 'Stancl\\Tenancy\\Bootstrappers\\QueueTenancyBootstrapper',
    ),
    'database' => 
    array (
      'central_connection' => 'mysql',
      'template_tenant_connection' => NULL,
      'prefix' => 'tenant',
      'suffix' => '',
      'managers' => 
      array (
        'sqlite' => 'Stancl\\Tenancy\\TenantDatabaseManagers\\SQLiteDatabaseManager',
        'mysql' => 'Stancl\\Tenancy\\TenantDatabaseManagers\\MySQLDatabaseManager',
        'pgsql' => 'Stancl\\Tenancy\\TenantDatabaseManagers\\PostgreSQLDatabaseManager',
      ),
    ),
    'cache' => 
    array (
      'tag_base' => 'tenant',
    ),
    'filesystem' => 
    array (
      'suffix_base' => 'tenant',
      'disks' => 
      array (
        0 => 'local',
        1 => 'public',
      ),
      'root_override' => 
      array (
        'local' => '%storage_path%/app/',
        'public' => '%storage_path%/app/public/',
      ),
      'suffix_storage_path' => true,
      'asset_helper_tenancy' => true,
    ),
    'redis' => 
    array (
      'prefix_base' => 'tenant',
      'prefixed_connections' => 
      array (
      ),
    ),
    'features' => 
    array (
      0 => 'Stancl\\Tenancy\\Features\\UserImpersonation',
      1 => 'Stancl\\Tenancy\\Features\\TelescopeTags',
      2 => 'Stancl\\Tenancy\\Features\\TenantConfig',
    ),
    'routes' => true,
    'migration_parameters' => 
    array (
      '--force' => true,
      '--path' => 
      array (
        0 => 'D:\\surv\\database\\migrations/tenant',
      ),
      '--realpath' => true,
    ),
    'seeder_parameters' => 
    array (
      '--class' => 'DatabaseSeeder',
    ),
    'default_tenant' => 'bp',
    'testing' => 
    array (
      'id' => 'test_tenant_id',
      'name' => 'test_tenant_name',
      'domain' => 'test_tenant_domain',
      'db' => 'test_tenant_db_name',
    ),
  ),
  'trustedproxy' => 
  array (
    'proxies' => NULL,
    'headers' => 30,
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'D:\\surv\\resources\\views',
    ),
    'compiled' => 'D:\\surv\\storage\\tenantbp\\framework\\views',
  ),
  'wazzup' => 
  array (
    'token' => '4f44c83e4c0e4ddcac37ad9c6a94c654',
    'channel_id' => 'c1518ae8-8509-4e06-b476-528217d37de4',
  ),
  'websockets' => 
  array (
    'dashboard' => 
    array (
      'port' => 6001,
    ),
    'apps' => 
    array (
      0 => 
      array (
        'id' => '12345',
        'name' => 'jobtron',
        'key' => '12345',
        'secret' => '12345',
        'path' => NULL,
        'capacity' => NULL,
        'enable_client_messages' => false,
        'enable_statistics' => true,
      ),
    ),
    'app_provider' => 'BeyondCode\\LaravelWebSockets\\Apps\\ConfigAppProvider',
    'allowed_origins' => 
    array (
    ),
    'max_request_size_in_kb' => 250,
    'path' => 'laravel-websockets',
    'middleware' => 
    array (
      0 => 'web',
      1 => 'auth',
      2 => 'BeyondCode\\LaravelWebSockets\\Dashboard\\Http\\Middleware\\Authorize',
    ),
    'statistics' => 
    array (
      'model' => 'BeyondCode\\LaravelWebSockets\\Statistics\\Models\\WebSocketsStatisticsEntry',
      'logger' => 'BeyondCode\\LaravelWebSockets\\Statistics\\Logger\\HttpStatisticsLogger',
      'interval_in_seconds' => 60,
      'delete_statistics_older_than_days' => 60,
      'perform_dns_lookup' => false,
    ),
    'ssl' => 
    array (
      'local_cert' => NULL,
      'local_pk' => NULL,
      'passphrase' => NULL,
      'verify_peer' => false,
    ),
    'channel_manager' => 'BeyondCode\\LaravelWebSockets\\WebSockets\\Channels\\ChannelManagers\\ArrayChannelManager',
  ),
  'yookassa' => 
  array (
    'merchant_id' => '983846',
    'secret_key' => 'test_oBJ51_hczCE2OfZc-rW85D_83YrrRPLkhpxQRicXthU',
  ),
  'migrations-generator' => 
  array (
    'migration_template_path' => 'D:\\surv\\vendor\\kitloong\\laravel-migrations-generator\\config/../stubs/migration.generate.stub',
    'migration_anonymous_template_path' => 'D:\\surv\\vendor\\kitloong\\laravel-migrations-generator\\config/../stubs/migration.generate.anonymous.stub',
    'migration_target_path' => 'D:\\surv\\database/migrations',
    'filename_pattern' => 
    array (
      'table' => '[datetime]_create_[name]_table.php',
      'view' => '[datetime]_create_[name]_view.php',
      'procedure' => '[datetime]_create_[name]_proc.php',
      'foreign_key' => '[datetime]_add_foreign_keys_to_[name]_table.php',
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
        'output_encoding' => '',
        'test_auto_detect' => true,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => false,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => NULL,
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
      'cells' => 
      array (
        'middleware' => 
        array (
        ),
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
      'default_ttl' => 10800,
    ),
    'transactions' => 
    array (
      'handler' => 'db',
      'db' => 
      array (
        'connection' => NULL,
      ),
    ),
    'temporary_files' => 
    array (
      'local_path' => 'D:\\surv\\storage/tenantbp\\framework/cache/laravel-excel',
      'local_permissions' => 
      array (
      ),
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'flare' => 
  array (
    'key' => NULL,
    'flare_middleware' => 
    array (
      0 => 'Spatie\\FlareClient\\FlareMiddleware\\RemoveRequestIp',
      1 => 'Spatie\\FlareClient\\FlareMiddleware\\AddGitInformation',
      2 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddNotifierName',
      3 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddEnvironmentInformation',
      4 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddExceptionInformation',
      5 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddDumps',
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddLogs' => 
      array (
        'maximum_number_of_collected_logs' => 200,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddQueries' => 
      array (
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddJobs' => 
      array (
        'max_chained_job_reporting_depth' => 5,
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestBodyFields' => 
      array (
        'censor_fields' => 
        array (
          0 => 'password',
          1 => 'password_confirmation',
        ),
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestHeaders' => 
      array (
        'headers' => 
        array (
          0 => 'API-KEY',
        ),
      ),
    ),
    'send_logs_as_events' => true,
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'auto',
    'enable_share_button' => true,
    'register_commands' => false,
    'solution_providers' => 
    array (
      0 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\BadMethodCallSolutionProvider',
      1 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\MergeConflictSolutionProvider',
      2 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\UndefinedPropertySolutionProvider',
      3 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\IncorrectValetDbCredentialsSolutionProvider',
      4 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingAppKeySolutionProvider',
      5 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\DefaultDbNameSolutionProvider',
      6 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\TableNotFoundSolutionProvider',
      7 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingImportSolutionProvider',
      8 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\InvalidRouteActionSolutionProvider',
      9 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\ViewNotFoundSolutionProvider',
      10 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\RunningLaravelDuskInProductionProvider',
      11 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingColumnSolutionProvider',
      12 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownValidationSolutionProvider',
      13 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingMixManifestSolutionProvider',
      14 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingViteManifestSolutionProvider',
      15 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingLivewireComponentSolutionProvider',
      16 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UndefinedViewVariableSolutionProvider',
      17 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\GenericLaravelExceptionSolutionProvider',
    ),
    'ignored_solution_providers' => 
    array (
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => 'D:\\surv',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
    'settings_file_path' => '',
    'recorders' => 
    array (
      0 => 'Spatie\\LaravelIgnition\\Recorders\\DumpRecorder\\DumpRecorder',
      1 => 'Spatie\\LaravelIgnition\\Recorders\\JobRecorder\\JobRecorder',
      2 => 'Spatie\\LaravelIgnition\\Recorders\\LogRecorder\\LogRecorder',
      3 => 'Spatie\\LaravelIgnition\\Recorders\\QueryRecorder\\QueryRecorder',
    ),
  ),
  'chat' => 
  array (
    'name' => 'MessengerFacade',
    'storage_disk_name' => 's3',
    'routes' => 
    array (
      'prefix' => 'messenger/api',
      'middleware' => 
      array (
        0 => 'web',
        1 => 'auth',
      ),
      'namespace' => 'Eddir\\Messenger\\Http\\Controllers',
    ),
    'pusher' => 
    array (
      'key' => '12345',
      'secret' => '12345',
      'app_id' => '12345',
      'options' => 
      array (
        'cluster' => NULL,
        'encrypted' => false,
        'host' => '127.0.0.1',
        'port' => '6001',
        'scheme' => 'https',
      ),
    ),
    'user_avatar' => 
    array (
      'folder' => 'users-avatar',
      'default' => NULL,
    ),
    'gravatar' => 
    array (
      'enabled' => false,
      'image_size' => 200,
      'imageset' => 'identicon',
    ),
    'attachments' => 
    array (
      'folder' => 'attachments',
      'download_route_name' => 'attachments.download',
      'allowed_images' => 
      array (
        0 => 'png',
        1 => 'jpg',
        2 => 'jpeg',
        3 => 'gif',
      ),
      'allowed_files' => 
      array (
        0 => 'zip',
        1 => 'rar',
        2 => 'txt',
      ),
      'max_upload_size' => 150,
    ),
    'colors' => 
    array (
      0 => '#2180f3',
      1 => '#2196F3',
      2 => '#00BCD4',
      3 => '#3F51B5',
      4 => '#673AB7',
      5 => '#4CAF50',
      6 => '#FFC107',
      7 => '#FF9800',
      8 => '#ff2522',
      9 => '#9C27B0',
    ),
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
