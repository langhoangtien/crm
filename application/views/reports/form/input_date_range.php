<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$current_day      = date('Y-m-d', time());
$yesterday        = date('Y-m-d',strtotime("-1 days"));
$six_days_left    = date('Y-m-d',strtotime("-6 days"));

$day = date('w');
$week_start      = date('Y-m-d', strtotime('-'.($day - 1).' days'));
$week_end        = date('Y-m-d', strtotime('+'.(7-$day).' days'));

$last_week_start = date('Y-m-d', strtotime('-'.($day - 1 + 7).' days'));
$last_week_end   = date('Y-m-d', strtotime('+'.(7-$day - 7).' days'));

$month_start     = date('Y-m-01');
$month_end       = date('Y-m-'.date("t"));

$month_start_ini  = new DateTime("first day of last month");
$month_end_ini    = new DateTime("last day of last month");

$last_month_start =  $month_start_ini->format('Y-m-d');
$last_month_end   =  $month_end_ini->format('Y-m-d');


$year_start_ini   = new DateTime("first day of january");
$year_ini_end     = new DateTime("last day of december");

$year_start       =  $year_start_ini->format('Y-m-d');
$year_end         =  $year_ini_end->format('Y-m-d');

$last_year        = date('Y') - 1;
$last_year_start  = $last_year.'-01-01';
$last_year_end    = $last_year.'-12-31';
?>
<div class="form-group">
    <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Phạm vi ngày :</label>
    <div class="col-sm-9 col-md-2 col-lg-2">
        <input type="radio" name="report_type" id="simple_radio" value="simple" checked="checked">
        <label for="simple_radio"><span></span></label>
        <select name="report_date_range_simple" id="report_date_range_simple" class="form-control">
            <option value="<?php echo $current_day;?>/<?php echo $current_day;?>">Hôm nay</option>
            <option value="<?php echo $yesterday; ?>/<?php echo $yesterday; ?>">Ngày hôm qua</option>
            <option value="<?php echo $six_days_left;?>/<?php echo $current_day;?>">7 ngày qua</option>
            <option value="<?php echo $week_start;?>/<?php echo $week_end;?>">Tuần này</option>
            <option value="<?php echo $last_week_start; ?>/<?php echo $last_week_end;?>">Tuần trước</option>
            <option value="<?php echo $month_start;?>/<?php echo $month_end;?>">Tháng này</option>
            <option value="<?php echo $last_month_start;?>/<?php echo $last_month_end;?>">Tháng trước</option>
            <option value="<?php echo $year_start; ?>/<?php echo $year_end; ?>">Năm nay</option>
            <option value="<?php echo $last_year_start;?>/<?php echo $last_year_end;?>">Năm trước</option>
            <option value="all">Toàn bộ thời gian</option>
        </select>
    </div>
</div>
<div class="form-group">
    <label for="complex_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Tùy chỉnh :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <input type="radio" name="report_type" id="complex_radio" value="complex">
        <label for="complex_radio"><span></span></label>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           Từ
                                            </span>
                    <input type="text" class="form-control start_date" name="start_date" id="start_date">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    Đến
                                            </span>
                    <input type="text" class="form-control end_date" name="end_date" id="end_date">
                </div>
            </div>
        </div>
    </div>
</div>