<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
    <div class="row">
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_start">..</span>
                <p>Nợ đầu kỳ</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_end">..</span>
                <p>Nợ cuối kỳ</p>
            </div>
        </div>
    </div>
<?php

if(!empty($filter['start_date']))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['start_date']));

if(!empty($filter['end_date']))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['end_date']));

if($filter['supplier_balance_options'] == 1)
    $balance_name = $this->config->item('supplier_balance');
elseif($filter['supplier_balance_options'] == 2)
    $balance_name = $this->config->item('supplier_balance_2');
?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-piluku reports-printable">
                <div class="panel-heading reports">
                   Báo cáo chi tiết công nợ nhà cung cấp - <?php echo $balance_name; ?>
                    <?php if(!empty($time_arr)): ?>
                        <small class="reports-range"><?php echo implode(' - ', $time_arr);?></small>
                    <?php endif; ?>
                    <span class="pull-right"></span>
                    <i class="fa fa-spinner fa-spin loading" id="supplier_store_accounts_loading" style="display: none;"></i>
                    <input type="hidden" class="data-n9-s" name ='s_start_date' value="<?php if(isset($filter['start_date'])) echo $filter['start_date']; ?>" data-table="supplier_store_accounts"/>
                    <input type="hidden" class="data-n9-s" name ='s_end_date' value="<?php if(isset($filter['end_date'])) echo $filter['end_date']; ?>" data-table="supplier_store_accounts"/>
                    <input type="hidden" class="data-n9-s" name="s_supplier_id" value="<?php echo $filter['supplier_id']; ?>" data-table="supplier_store_accounts"/>
                    <input type="hidden" class="data-n9-s" name="s_options" value="<?php echo $filter['supplier_balance_options']; ?>" data-table="supplier_store_accounts"/>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table data-table="supplier_store_accounts" data-currentpage="1" data-url="<?php echo base_url() . 'reports/'.$action.'_store/'; ?>" class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only" id="sortable_table" data-callback="true">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Mã đơn hàng</th>
                                    <th>Nợ đầu</th>
                                    <th>Ghi nợ</th>
                                    <th>Ghi có</th>
                                    <th>Nợ cuối</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
<!--                        <table data-responsive-table="shift_expenses" data-currentpage="1" data-url="--><?php //echo base_url() . 'reports/'.$action.'_store/'; ?><!--" class="table table-bordered table-striped table-reports tablesorter stacktable small-only">-->
<!--                            <tbody>-->
<!--                            </tbody>-->
<!--                        </table>-->
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary text-white hidden-print" id="print_excel"> In </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top" data-table="supplier_store_accounts">
    </div>
    <script type="text/javascript">
        function n9_grid_callback(data_table,result) {
            if(data_table == 'supplier_store_accounts') {
                $('#debt_start').text(result.debt_start);
                $('#debt_end').text(result.debt_end);
            }
        }
        $( document ).ready(function() {
            load_list('supplier_store_accounts');
<?php if($filter['supplier_id'] == -1) :?>
            toastr.error('Phải chọn một nhà cung cấp', 'Lỗi');
<?php endif; ?>
        });

        function print_report()
        {
            window.print();
        }

        $('#print_button').click(function(e){
            e.preventDefault();
            print_report();
        });

        $('#print_excel').click(function(e){
            window.location.href = "<?php echo $url_print; ?>";
        });

<?php

 ?>

    </script>
<?php $this->load->view("partial/footer"); ?>