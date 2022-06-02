<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\ExamZarpMonthly;
use App\Console\Commands\FetchAnvizRecords;
use App\Console\Commands\QualityRecordsTotals;
use App\Console\Commands\Timetracking;
use App\Console\Commands\MarkTrainees;
use App\Console\Commands\CheckTimetrackers;
use App\Console\Commands\DeleteUser;
use App\Console\Commands\InviteToBitrixAndAdmin;
use App\Console\Commands\NotifyManagersAboutForeigners;
use App\Console\Commands\SalaryTrainees;
use App\Console\Commands\RecruitingRecordsTotals;
use App\Console\Commands\SetAbsent;
use App\Console\Commands\Bitrix\BitrixStats;
use App\Console\Commands\Bitrix\FunnelStats;
use App\Console\Commands\Notifications\Adaptation;
use App\Console\Commands\Notifications\FillReport;
use App\Console\Commands\Callibro\GetWorkedHours;
use App\Console\Commands\Whatsapp\Assessment;
use App\Console\Commands\Employee\SalaryIndexation;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        

        
        /**
         * =====================================================================
         * УЧЕТ ВРЕМЕНИ 
         * ====================================================================
         */

        /**
         * КАЖДАЯ МИНУТА
         */
        $schedule->command("tenants:run " . "timetracking:check" . " --tenants=bp")->everyMinute(); // автоматически завершить рабочий день если забыли нажать на кнопку
        $schedule->command("tenants:run " . "fetch:anviz" . " --tenants=bp")->everyMinute(); // Anviz W1 Pro Учет времени: выгрузка истории отпечатков с базы и запись в Timetracking 
        $schedule->command("tenants:run " . "set:absent" . " --tenants=bp")->everyMinute(); // Автоматически отмечать отсутстовваших в стажировке после истечения 30 минутной ссылки
        


        /**
         * КАЖДЫЕ 5 МИНУТ
         */
        $schedule->command("tenants:run " . "headhunter:fetch 1" . " --tenants=bp")->everyFiveMinutes(); // hh отклики запрос откликов
        $schedule->command("tenants:run " . "headhunter:fetch 3" . " --tenants=bp")->everyFiveMinutes(); // hh отклики в битрикс 


        $schedule->command("tenants:run " . "salary:group" . " --tenants=bp")->everyTenMinutes(55); // Сохранить заработанное группой без вычета шт и ав
 
        /**
         * КАЖДЫЕ 15 МИНУТ
         */
        $schedule->command("tenants:run " . "headhunter:fetch 2". " --tenants=bp")->everyFifteenMinutes(); // hh отклики запрос резюме 500 в день



        /**
         * РАЗ В ЧАС
         */
        $schedule->command("tenants:run " . "salary:update" . " --tenants=bp")->hourly(); // обновление зарплаты: за текущий день
        $schedule->command("tenants:run " . "count:hours" . " --tenants=bp")->hourly(); // обновление минут
        $schedule->command("tenants:run " . "check:late" . " --tenants=bp")->hourly(); // Опоздание
        $schedule->command("tenants:run " . "bonus:update" . " --tenants=bp")->hourly(); // Бонусы сотрудников
        $schedule->command("tenants:run " . "callibro:minutes_aggrees ". " --tenants=bp")->hourlyAt(40); // Отработанное время сотрудников Евраз 1 Хоум
        $schedule->command("tenants:run " . 'whatsapp:estimate_first_day'. " --tenants=bp")->hourly()->between('11:00', '13:00'); // Ссылка на ватсап для стажеров на первый день обучения
        $schedule->command("tenants:run " . 'recruiting:trainee_report'. " --tenants=bp")->hourlyAt(56); // Отчет, сколько пристутствовал на обучении в течении семи дней
        $schedule->command("tenants:run " . 'user:save_kpi'. " --tenants=bp")->hourlyAt(50); // Сохранить kpi для быстрой загрузки аналитики
        
        
        $schedule->command("tenants:run " . "bitrix:stats" . " --tenants=bp")->hourlyAt(57); // Данные статистики из битрикса для рекрутинга
        $schedule->command("tenants:run " . "quality:totals" . " --tenants=bp")->hourly(); // Расчет недельных и месячных средних значений по контролю качества в Каспи
        
	    $schedule->command("tenants:run " . 'recruiter:stats 1'. " --tenants=bp")->hourlyAt(1); // Данные почасовой таблицы рекрутинга из битрикса 
	    $schedule->command("tenants:run " . 'recruiter:stats'. " --tenants=bp")->hourlyAt(14); // Данные почасовой таблицы рекрутинга из битрикса  
	    $schedule->command("tenants:run " . 'recruiter:stats'. " --tenants=bp")->hourlyAt(29); // Данные почасовой таблицы рекрутинга из битрикса 
	    $schedule->command("tenants:run " . 'recruiter:stats'. " --tenants=bp")->hourlyAt(44); // Данные почасовой таблицы рекрутинга из битрикса 
        
        $schedule->command("tenants:run " . "recruiting:totals" . " --tenants=bp")->hourlyAt(59); //  рекрутинг cводная
        $schedule->command("tenants:run " . "bitrix:funnel:stats" . " --tenants=bp")->hourlyAt(16); // Воронка в Аналитике
        
        /**
         * РАЗ В ДЕНЬ
         */
        $schedule->command("tenants:run " . "timetracking:mark_trainees" . " --tenants=bp")->dailyAt('00:00'); // Отметка стажеров в табели в 6 утра
        $schedule->command("tenants:run " . 'currency:refresh' . " --tenants=bp")->dailyAt('00:00'); // Обновление курса валют currencylayer.com
        $schedule->command("tenants:run " . "userxxxxxxxxxxxxx:delete:dontusethisitsnotforsuign" . " --tenants=bp")->dailyAt('00:00'); // Удаление сотрудников с отработкой в 6 утра
        //$schedule->command("salary:update week")->dailyAt('00:10'); // обновление зарплаты: последняя неделя (в 6:10 утра)
        $schedule->command("tenants:run " . "usernotification:estimate_trainer" . " --tenants=bp")->dailyAt('06:00'); // Уведолмение об оценке руководителя и старшего спеца. За 2 дня до конца месяца
        $schedule->command("tenants:run " . "headhunter:fetch 0" . " --tenants=bp")->dailyAt('02:00'); // hh вакансиии обновить
        $schedule->command("tenants:run " . "intellect:send" . " --tenants=bp")->dailyAt('02:00'); // Отправить сообщение со ссылкой по ватсапу на учет времени и битрикс, приглашенным стажерам на 4ый день стажировки
        $schedule->command("tenants:run " . "usernotification:adaptation". " --tenants=bp")->dailyAt('02:40'); // Уведомление о заполнении адаптации
        $schedule->command("tenants:run " . "salary:indexation" . " --tenants=bp")->dailyAt('17:02'); // Индексация зарплаты
        $schedule->command("tenants:run " . "callibro:grades" . " --tenants=bp")->dailyAt('17:12'); // Сохранить недельные Оценки диалогов с Callibro
        $schedule->command("tenants:run " . "timetracking:salary_trainees" . " --tenants=bp")->dailyAt('17:30'); // Расчет оплаты стажерам
        $schedule->command("tenants:run " . 'callibro:conversion' . " --tenants=bp")->dailyAt('17:35'); // Конверсия согласий сотрудников Евраз 1 Хоум
        
        $schedule->command("tenants:run " . "check:timetrackers" . " --tenants=bp")->dailyAt('20:00'); // Автоматически завершать день в 2 часа ночи, тем кто забыл завершить
    
        /**
         * РАЗ В НЕДЕЛЮ
         */
        
        $schedule->command("tenants:run " . "usernotification:report" . " --tenants=bp")->weekly()->fridays()->at('11:00'); // Уведомление о заполнении отчета в 17:00 в пятницу
        $schedule->command("tenants:run " . "usernotification:foreigner" . " --tenants=bp")->weekly()->mondays()->at('02:00'); // Уведомление руководителей групп об оплате иностранным стажерам. Запускается каждый понедельник
        $schedule->command("tenants:run " . "fine:check" . " --tenants=bp")->weeklyOn(1, '00:00'); // Каждый понедельник в 6 утра проверка на отсутствие в воскресенье 
        $schedule->command("tenants:run " . "fine:check" . " --tenants=bp")->weeklyOn(2, '00:00'); // Каждый вторник в 6 утра проверка на отсутствие в понедельник


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        
        $tenant = \App\Models\Tenant::find('bp');
        tenancy()->initialize($tenant);
        
        require base_path('routes/console.php');
    }
}
