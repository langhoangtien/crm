<?php
function dateDiff($time1, $time2) {
    $date3 = date_create($time2);
    $date4 = date_create($time1);

    $diff34 = date_diff($date4, $date3);

    $days = $diff34->d;

    $months = $diff34->m;

    $years = $diff34->y;

    // $hours=$diff34->h;

    // $minutes=$diff34->i;

    // $seconds=$diff34->s;

    if($years > 0)
        $result[] = $years . ' năm';

    if($months > 0)
        $result[] = $months . ' tháng';

    if($days > 0)
        $result[] = $days . ' ngày';
    else 
        $result[] = '1 ngày';
        
    // $result[] = $hours . ' giờ';
    // $result[] = $minutes . ' phút';

    $result = implode(', ', $result);

    return $result;
}

function distance_between_two_days($date_start, $date_end) {
    $date_start = date('Y-m-d', strtotime($date_start));

    $date_end   = date('Y-m-d', strtotime($date_end));

    $datediff = strtotime($date_end) - strtotime($date_start);
    $duration = floor($datediff/(60*60*24));
    return $duration;
}