<img src="https://u-marketing.org/public/images/logotwo.png" height="50">

## Stack

- Laravel 5.5
- PHP 7.3
- Linux Ubuntu 16.04
- Nginx v1.10.3



## Возможные баги


### admin.u-marketing.org
* Штраф опоздания на 5 и 10 минут дублируются, возможно нужно пересмотреть крон (ВАЖНО)
* Штраф не показывется в желтых метках (суббота и воскресенье)
* У случайных людей, в timetracking-е дублируются записи, иногда по несколько


### cp.u-marketing.org

* Автозвонки не приходят, которые повторяются (НЕ ТОЧНО)



## Уязвимости

* В профиле некоторых сотрудников отсутствуют поля, которые должны быть заполнены (Например: work_time, position)
* settingController::setUserNotificationIsRead может принять NULL, который кинет Exception

## Задачи, которые нужно согласовать

* Отмечать красным те дни, где был штраф, с таблице начисления
* Настроить левое меню, чтобы Настройки не забивались на одной странице
* Для ввода модульного тестирования, нужен PHP 7.2 минимум
