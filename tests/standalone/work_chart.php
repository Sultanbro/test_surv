<?php
// Exercise pure report helpers without booting Laravel, touching databases or invoking tenant jobs.
require __DIR__ . '/../../app/Service/Timetrack/SalaryWorkChart/SalaryWorkChartService.php';
set_error_handler(function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
$service = (new ReflectionClass(App\Service\Timetrack\SalaryWorkChart\SalaryWorkChartService::class))->newInstanceWithoutConstructor();
$checks = 0;
function equalValue($expected, $actual, $label) {
    global $checks;
    if ($expected != $actual) throw new RuntimeException($label . ': unexpected result');
    $checks++;
}
equalValue(0, $service->countDayWork([]), 'Empty schedule has no working days');
equalValue(0, $service->countDayOff([]), 'Empty schedule has no days off');
equalValue(0, $service->totalSalaryToDay([]), 'Empty schedule has no salary');
$holidays = [['day' => 1, 'day_off' => true], ['day' => 2, 'day_off' => true]];
equalValue(0, $service->countDayWork($holidays), 'Holiday-only schedule');
equalValue(2, $service->countDayOff($holidays), 'Two holidays');
equalValue(0, $service->totalSalaryToDay($holidays), 'Holidays without accruals');
$working = [['day' => 1, 'day_off' => false, 'salary' => '1200.50'], ['day' => 2, 'day_off' => false, 'salary' => '800.25']];
equalValue(2, $service->countDayWork($working), 'Two working days');
equalValue(0, $service->countDayOff($working), 'Working-only schedule');
equalValue(2000.75, $service->totalSalaryToDay($working), 'Accrual summary with decimals');
$mixed = array_merge($working, $holidays);
equalValue(2, $service->countDayWork($mixed), 'Mixed schedule working days');
equalValue(2, $service->countDayOff($mixed), 'Mixed schedule days off');
equalValue(2000.75, $service->totalSalaryToDay($mixed), 'Days without accruals do not affect total');
echo "OK ($checks work-chart checks)\n";
