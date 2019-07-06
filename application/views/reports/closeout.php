<?php $this->load->view("partial/header"); ?>
<div class="row"></div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-piluku reports-printable">
            <div class="panel-heading">
                Báo cáo - Kết thúc Ngày làm việc
                <small class="reports-range"><?php echo $date; ?></small>
				<span class="pull-right">
				</span>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-reports tablesorter" id="sortable_table">
                        <thead>
                        <tr>
                            <th align="left">Mô tả</th>
                            <th align="left">Dữ liệu</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="left"><a href="<?php echo base_url(); ?>reports/closeout/<?php echo $prev_date; ?>">« Ngày hôm trước</a></td>
                                <td align="left"><a href="<?php echo base_url(); ?>reports/closeout/<?php echo $next_date; ?>">Ngày tiếp theo »</a></td>
                            </tr>
            <?php
                foreach($report_list as $val) {
            ?>
                    <tr>
                        <td align="left"><?php echo $val['left']; ?></td>
                        <td align="left"><?php echo $val['right']; ?></td>
                    </tr>
            <?php
                }
            ?>

                        </tbody>
                   </table>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary text-white hidden-print" id="print_excel"> In </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $( "#print_excel" ).click(function() {
            window.location.href = BASE_URL + "reports/closeout_excel/<?php echo $date; ?>";
        });
    });
</script>
<?php $this->load->view("partial/footer"); ?>