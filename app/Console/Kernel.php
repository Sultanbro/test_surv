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
use App\Console\Commands\ChecklistUpdater;
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
        $schedule->command("timetracking:check")->everyMinute(); // автоматически завершить рабочий день если забыли нажать на кнопку
        $schedule->command("fetch:anviz")->everyMinute(); // Anviz W1 Pro Учет времени: выгрузка истории отпечатков с базы и запись в Timetracking 
        $schedule->command("set:absent")->everyMinute(); // Автоматически отмечать отсутстовваших в стажировке после истечения 30 минутной ссылки
        
        /**
         * КАЖДЫЕ 5 МИНУТ
         */
        $schedule->command("headhunter:fetch 1")->everyFiveMinutes(); // hh отклики запрос откликов
        $schedule->command("headhunter:fetch 3")->everyFiveMinutes(); // hh отклики в битрикс 


        $schedule->command("salary:group")->everyTenMinutes(55); // Сохранить заработанное группой без вычета шт и ав
 
        /**
         * КАЖДЫЕ 15 МИНУТ
         */
        $schedule->command("headhunter:fetch 2")->everyFifteenMinutes(); // hh отклики запрос резюме 500 в день


        /**
         * РАЗ В ЧАС
         */
        $schedule->command("salary:update")->hourly(); // обновление зарплаты: за текущий день
        $schedule->command("count:hours")->hourly(); // обновление минут
        $schedule->command("check:late")->hourly(); // Опоздание
        $schedule->command("bonus:update")->hourly(); // Бонусы сотрудников
        $schedule->command("callibro:minutes_aggrees")->everyFiveMinutes(); // Отработанное время сотрудников Евраз 1 Хоум
       // $schedule->command("callibro:minutes_aggrees")->hourlyAt(40); // Отработанное время сотрудников Евраз 1 Хоум
        $schedule->command('whatsapp:estimate_first_day')->hourly()->between('11:00', '13:00'); // Ссылка на ватсап для стажеров на первый день обучения
        $schedule->command('recruiting:trainee_report')->hourlyAt(56); // Отчет, сколько пристутствовал на обучении в течении семи дней
        $schedule->command('user:save_kpi')->hourlyAt(50); // Сохранить kpi для быстрой загрузки аналитики
        
        
        $schedule->command("bitrix:stats")->hourlyAt(57); // Данные статистики из битрикса для рекрутинга
        $schedule->command("quality:totals")->hourly(); // Расчет недельных и месячных средних значений по контролю качества в Каспи
        
	    $schedule->command('recruiter:stats 1')->hourlyAt(1); // Данные почасовой таблицы рекрутинга из битрикса 
	    $schedule->command('recruiter:stats')->hourlyAt(14); // Данные почасовой таблицы рекрутинга из битрикса  
	    $schedule->command('recruiter:stats')->hourlyAt(29); // Данные почасовой таблицы рекрутинга из битрикса 
	    $schedule->command('recruiter:stats')->hourlyAt(44); // Данные почасовой таблицы рекрутинга из битрикса 
        
        $schedule->command("recruiting:totals")->hourlyAt(59); //  рекрутинг cводная
        $schedule->command("bitrix:funnel:stats")->hourlyAt(16); // Воронка в Аналитике
        
        /**
         * РАЗ В ДЕНЬ
         */
        $schedule->command("timetracking:mark_trainees")->dailyAt('00:00'); // Отметка стажеров в табели в 6 утра
        $schedule->command('currency:refresh')->dailyAt('00:00'); // Обновление курса валют currencylayer.com
        $schedule->command("userxxxxxxxxxxxxx:delete:dontusethisitsnotforsuign")->dailyAt('00:00'); // Удаление сотрудников с отработкой в 6 утра

        $schedule->command("usernotification:estimate_trainer")->dailyAt('06:00'); // Уведолмение об оценке руководителя и старшего спеца. За 2 дня до конца месяца
        $schedule->command("headhunter:fetch 0")->dailyAt('02:00'); // hh вакансиии обновить
        $schedule->command("intellect:send")->dailyAt('02:00'); // Отправить сообщение со ссылкой по ватсапу на учет времени и битрикс, приглашенным стажерам на 4ый день стажировки
        $schedule->command("usernotification:adaptation")->dailyAt('02:40'); // Уведомление о заполнении адаптации
        $schedule->command("salary:indexation")->dailyAt('17:02'); // Индексация зарплаты
        $schedule->command("callibro:grades")->dailyAt('17:12'); // Сохранить недельные Оценки диалогов с Callibro
        $schedule->command("timetracking:salary_trainees")->dailyAt('17:30'); // Расчет оплаты стажерам
        $schedule->command('callibro:conversion')->dailyAt('17:35'); // Конверсия согласий сотрудников Евраз 1 Хоум
        
        $schedule->command("check:timetrackers")->dailyAt('20:00'); // Автоматически завершать день в 2 часа ночи, тем кто забыл завершить
        $schedule->command("checklist:update")->dailyAt('00:00'); //Ставить чек листы каждый день для сотрудников
        $schedule->command("trainee:count_days")->dailyAt('00:00'); //Запись дней в аналитику по стажерам 1й день 2й+ день
    
        /**
         * РАЗ В НЕДЕЛЮ
         */
        
        $schedule->command("usernotification:report")->weekly()->fridays()->at('11:00'); // Уведомление о заполнении отчета в 17:00 в пятницу
        $schedule->command("usernotification:foreigner")->weekly()->mondays()->at('02:00'); // Уведомление руководителей групп об оплате иностранным стажерам. Запускается каждый понедельник
        $schedule->command("fine:check")->weeklyOn(1, '00:00'); // Каждый понедельник в 6 утра проверка на отсутствие в воскресенье 
        $schedule->command("fine:check")->weeklyOn(2, '00:00'); // Каждый вторник в 6 утра проверка на отсутствие в понедельник


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
