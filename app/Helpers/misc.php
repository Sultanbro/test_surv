<?php
/**
 *  helper miscellaneous functions
 */

/**
 * Посчитатть рабочие дни, за исключением $ignore (дни недели)
 */
function workdays($year, $month, array $ignore = [6,0]) {
    $count = 0;
    $counter = mktime(0, 0, 0, $month, 1, $year);
    while (date("n", $counter) == $month) {
        if (in_array(date("w", $counter), $ignore) == false) {
            $count++;
        }
        $counter = strtotime("+1 day", $counter);
    }
    return $count;
}

/**
 * Колво рабочих дней между датами
 */
function workdays_diff($start, $end, $exclude = 2) {
    $start = new \DateTime($start);
    $end = new \DateTime($end);
    // otherwise the  end date is excluded (bug?)
    //$end->modify('+1 day');

    $interval = $end->diff($start);

    // total days
    $days = $interval->days;

    // create an iterateable period of date (P1D equates to 1 day)
    $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

    // best stored as array, so you can add more than one
    $holidays = array('2012-09-07');

    foreach($period as $dt) {
        $curr = $dt->format('D');

        // substract if Saturday or Sunday
        if($exclude == 2) {
            if ($curr == 'Sat' || $curr == 'Sun') {
                $days--;
            }
        } else {
            if ($curr == 'Sun') {
                $days--;
            }
        }
        

    }


    return $days; // 4
}

