<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<div class="row">
    <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
        <div class="info-seven primarybg-info">
            <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
            <span id="shift_total">..</span>
            <p>Tổng chi phí</p>
        </div>
    </div>
</div>
<?php
    if(!empty($filter['start_date']))
        $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['start_date']));

    if(!empty($filter['end_date']))
        $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['end_date']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-piluku reports-printable">
            <div class="panel-heading reports">
                Báo cáo - Báo cáo <?php echo mb_strtolower($category['name'],'UTF-8'); ?>
<?php if(!empty($time_arr)): ?>
                <small class="reports-range"><?php echo implode(' - ', $time_arr);?></small>
<?php endif; ?>
                <span class="pull-right"></span>
                <i class="fa fa-spinner fa-spin loading" id="shift_expenses_loading" style="display: none;"></i>
                <input type="hidden" class="data-n9-s" name ='s_start_date' value="<?php echo $filter['start_date']; ?>" data-table="shift_expenses"/>
                <input type="hidden" class="data-n9-s" name ='s_end_date' value="<?php echo $filter['end_date']; ?>" data-table="shift_expenses"/>
                <input type="hidden" class="data-n9-s" name="s_location_ids" value="<?php echo implode(',', $filter['selected_location_ids']); ?>" data-table="shift_expenses"/>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table data-table="shift_expenses" data-currentpage="1" data-url="<?php echo base_url() . 'reports/'.$action.'_store/'; ?>" class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only" id="sortable_table">
                        <thead>
                            <tr>
                                <th data-field="expense_date" style="width: 20%;">Thời gian</th>
                                <th style="width: 30%;">Diễn giải</th>
                                <th data-field="expense_amount" style="width: 20%;">Số tiền</th>
                                <th data-field="employee_id" style="width: 20%;">Người nhận</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <table data-responsive-table="shift_expenses" data-currentpage="1" data-url="<?php echo base_url() . 'reports/'.$action.'_store/'; ?>" class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary text-white hidden-print" id="print_button"> In </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top" data-table="shift_expenses">
</div>
    <script type="text/javascript">
        $( document ).ready(function() {
            load_list('shift_expenses');
        });

        function print_report()
        {
            window.print();
        }

        $('#print_button').click(function(e){
            e.preventDefault();
            print_report();
        });

    </script>
<?php $this->load->view("partial/footer"); ?>