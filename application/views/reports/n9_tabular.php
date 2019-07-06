        
<?php $this->load->view("partial/header");?>
<?php
$link_submit = base_url() . 'reports/report_filter?action='.$action;
if(!isset($time))
    $time = 0;
if($time == 1)
    $link_submit = $link_submit .'&time=1';
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <?php echo $title; ?>
            </div>
            <!-- <?php echo $link_submit;?> -->
            <div class="panel-body">
                <form class="form-horizontal form-horizontal-mobiles" method="POST" id="report_form" action="<?php echo $link_submit;?>">
                    <div id="report_date_range_complex">
                <?php
                foreach($inputs as $input) { ?>
                    <?php $this->load->view("reports/form/$input"); ?>
                <?php } ?>

                <?php if(isset($no_excel) && !$no_excel): ?>
                        <div class="form-group">
                            <label class="col-sm-3 col-md-3 col-lg-2 control-label">Xuất tệp excel :</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="radio" name="export_excel" id="export_excel_yes" value="1"> Đồng ý
                                <label for="export_excel_yes"><span></span></label>
                                <input type="radio" name="export_excel" id="export_excel_no" value="0" checked="checked"> Từ chối
                                <label for="export_excel_no"><span></span></label>
                            </div>
                        </div>
                <?php endif; ?>
                        <div class="form-actions pull-right">
                            <button name="generate_report" type="button" id="generate_report" class="btn btn-primary submit_button">Thực hiện</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        var time = <?php echo $time; ?>;
        var action = '<?php echo $action; ?>';
        $( "#start_date" ).focus(function() {
            $("#complex_radio").prop("checked", true);
        });

        $( "#end_date" ).focus(function() {
            $("#complex_radio").prop("checked", true);
        });

        $( "#report_date_range_simple" ).change(function() {
            $("#simple_radio").prop("checked", true);
        });

        $( "#reports_locations_list li span" ).click(function() {
            var li_parent = $(this).closest('li');
            var checkbox = li_parent.find('.reports_selected_location_ids_checkboxes');
            if(checkbox.is(':checked'))
                checkbox.prop('checked', false);
            else
                checkbox.prop('checked', true);
        });

        $( "#generate_report" ).click(function() {
            $("#report_form").submit();

        });
<?php
 if($time == 1) {
?>
        date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT);
        date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT);
<?php
 }else {
 ?>
        date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
        date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
 <?php
 }
?>

    });
</script>
<?php $this->load->view("partial/footer"); ?>