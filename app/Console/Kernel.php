<?php

namespace App\Console;

use App\Console\Commands\Api\CheckPaymentsStatusCommand;
use App\Console\Commands\Api\RunAutoPaymentCommand;
use App\Console\Commands\Bitrix\RecruiterStats;
use App\Console\Commands\CheckForReferrerDaily;
use App\Console\Commands\Employee\BonusUpdate;
use App\Console\Commands\Employee\CheckLate;
use App\Console\Commands\ForTestingCommand;
use App\Console\Commands\ListenQueue;
use App\Console\Commands\Pusher\NotificationTemplatePusher;
use App\Console\Commands\Pusher\Pusher;
use App\Console\Commands\RestartQueue;
use App\Console\Commands\SetExitTimetracking;
use App\Console\Commands\StartDayForItDepartmentCommand;
use App\Console\Commands\Tools\TenantMigrateFreshCommand;
use App\Models\Tenant;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Make\RepositoryMakeCommand::class,
        Commands\SaveUserKpi::class,
        RunAutoPaymentCommand::class,
        BonusUpdate::class,
        CheckPaymentsStatusCommand::class,
        StartDayForItDepartmentCommand::class,
        RecruiterStats::class,
        RestartQueue::class,
        ListenQueue::class,
        CheckLate::class,
        Pusher::class,
        NotificationTemplatePusher::class,
        SetExitTimetracking::class,
        TenantMigrateFreshCommand::class,
        ForTestingCommand::class,
        CheckForReferrerDaily::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*
        |--------------------------------------------------------------------------
        | Команды кабинета bp.jobtron.org
        |--------------------------------------------------------------------------
        |
        | Только запускаются у компании Bpartners в bp.jobtron.org
        |
        */
        $schedule->command('tenants:run run:pusher --tenants=bp')
            ->withoutOverlapping()
            ->dailyAt('08:00');
        $schedule->command('tenants:run run:pusher:template --tenants=bp')
            ->withoutOverlapping()
            ->dailyAt('08:00');
        $schedule->command('tenants:run check:ref-duplicates --tenants=bp')->everyTenMinutes(); //
        $schedule->command('tenants:run fetch:anviz --tenants=bp')->everyMinute(); // Anviz W1 Pro Учет времени: выгрузка истории отпечатков с базы и запись в Timetracking
        $schedule->command('tenants:run recruiter:attendance --tenants=bp')->hourly(); // Рекрутеры 1 и 2 день стажировки присутствовавших
        $schedule->command('tenants:run whatsapp:estimate_first_day --tenants=bp')->hourly()->between('11:00', '13:00'); // Ссылка на ватсап для стажеров на первый день обучения
        $schedule->command('tenants:run recruiting:trainee_report --tenants=bp')->hourlyAt(56); // Отчет, сколько пристутствовал на обучении в течении семи дней
        $schedule->command('tenants:run callibro:fetch --tenants=bp')->hourly(); // Отработанное время сотрудников Евраз 1 Хоум
        $schedule->command('tenants:run currency:refresh --tenants=bp')->dailyAt('00:00'); // Обновление курса валют currencylayer.com
        $schedule->command('tenants:run usernotification:estimate_trainer --tenants=bp')->dailyAt('06:00'); // Уведолмение об оценке руководителя и старшего спеца. За 2 дня до конца месяца
        $schedule->command('tenants:run intellect:send --tenants=bp')->dailyAt('02:00'); // Отправить сообщение со ссылкой по ватсапу на учет времени и битрикс, приглашенным стажерам на 4ый день стажировки
        $schedule->command('tenants:run callibro:grades --tenants=bp')->dailyAt('17:12'); // Сохранить недельные Оценки диалогов с Callibro
        $schedule->command('tenants:run usernotification:report --tenants=bp')->weekly()->fridays()->at('11:00'); // Уведомление о заполнении отчета в 17:00 в пятницу
        $schedule->command('tenants:run usernotification:foreigner --tenants=bp')->weekly()->mondays()->at('02:00'); // Уведомление руководителей групп об оплате иностранным стажерам. Запускается каждый понедельник
        $schedule->command('tenants:run start_day:it_department --tenants=bp')->daily(); // Запускается каждый день, начинает день для IT отдела
        $schedule->command('tenants:run logs:access --tenants=bp')->daily()->at('01:00'); // Запускается каждый день, начинает день для IT отдела
        $schedule->command('tenants:run recruiter:stats --tenants=bp --argument="count_last_hour=1"')->hourlyAt(1); // Данные почасовой таблицы рекрутинга из битрикса
        $schedule->command('tenants:run recruiter:stats --tenants=bp')->hourlyAt(20); // Данные почасовой таблицы рекрутинга из битрикса
        $schedule->command('tenants:run recruiter:stats --tenants=bp')->hourlyAt(40); // Данные почасовой таблицы рекрутинга из битрикса
        $schedule->command('tenants:run restart-queue --tenants=bp')->dailyAt('00:10');
        /*
        |--------------------------------------------------------------------------
        | Команды кабинетов jobtron.org
        |--------------------------------------------------------------------------
        |
        | Запускаются во всех субдоменах.
        |
        */
        $schedule->command('tenants:run timetracking:check')->everyMinute(); // автоматически завершить рабочий день если забыли нажать на кнопку
        $schedule->command('tenants:run set:absent')->everyMinute(); // Автоматически отмечать отсутстовваших в стажировке после истечения 30 минутной ссылки
        $schedule->command('tenants:run salary:group')->everyTenMinutes(); // Сохранить заработанное группой без вычета шт и ав
        $schedule->command('tenants:run salary:update')->hourly(); // обновление зарплаты: за текущий день
        $schedule->command('tenants:run count:hours')->hourly(); // обновление минут
        $schedule->command('tenants:run check:late')->hourly(); // Опоздание
        $schedule->command('tenants:run bonus:update')->hourly(); // Бонусы сотрудников
        $schedule->command('tenants:run user:save_kpi')->hourlyAt(50); // Сохранить kpi для быстрой загрузки аналитики
        $schedule->command('tenants:run quality:totals')->hourly(); // Расчет недельных и месячных средних значений по контролю качества в Каспи
        $schedule->command('tenants:run userxxxxxxxxxxxxx:delete:dontusethisitsnotforsuign')->dailyAt('00:00'); // Удаление сотрудников с отработкой в 6 утра
        $schedule->command('tenants:run usernotification:adaptation')->dailyAt('02:40'); // Уведомление о заполнении адаптации
        $schedule->command('tenants:run salary:indexation')->dailyAt('17:02'); // Индексация зарплаты
        $schedule->command('tenants:run timetracking:salary_trainees')->dailyAt('17:30'); // Расчет оплаты стажерам
        $schedule->command('tenants:run check:timetrackers')->dailyAt('20:00'); // Автоматически завершать день в 2 часа ночи, тем кто забыл завершить
        $schedule->command('tenants:run fine:check')->weeklyOn(1, '00:00'); // Каждый понедельник в 6 утра проверка на отсутствие в воскресенье
        $schedule->command('tenants:run fine:check')->weeklyOn(2, '00:00'); // Каждый вторник в 6 утра проверка на отсутствие в понедельник
        $schedule->command('tenants:run analytics:pivots')->withoutOverlapping()->monthly(); // создать сводные таблицы отделов в аналитике
        $schedule->command('tenants:run analytics:parts')->withoutOverlapping()->monthly(); // создать декомпозицию и спидометры в аналитике
        $schedule->command('auto-payment:run')->daily(); // Команда для авто-оплаты запускается каждый день.
        $schedule->command('check-payments-status:run')->everyFiveMinutes();
        $schedule->command('bitrix:trainees:move')->dailyAt('06:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function commands()
    {
        if (table_exists('tenants')) {
            if (config('tenancy.default_tenant')) {
                $tenant = Tenant::query()->where('id', config('tenancy.default_tenant'))->first();
                if ($tenant) {
                    $this->load(__DIR__ . '/Commands');
                    tenancy()->initialize($tenant);
                };
            }
        }
        require base_path('routes/console.php');
    }
}
