<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
    <div class="row">
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_start"><?php if(isset($filter['tong_khach_no'])) echo number_format($filter['tong_khach_no'],1) ?></span>
                <p>Tổng tiền khách nợ</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_end"><?php if(isset($filter['tong_no_khach'])) echo number_format($filter['tong_no_khach'],1) ?></span>
                <p>Tổng tiền nợ khách</p>
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
                   Báo cáo tổng hợp công nợ theo nhóm khách hàng
                    <?php if(!empty($time_arr)): ?>
                        <small class="reports-range"><?php echo implode(' - ', $time_arr);?></small>
                    <?php endif; ?>
                    <span class="pull-right"></span>
                    <i class="fa fa-spinner fa-spin loading" id="store_accounts_loading" style="display: none;"></i>
                    <input type="hidden" class="data-n9-s" name ='s_start_date' value="<?php echo isset($filter['start_date'])?$filter['start_date']:''; ?>" data-table="store_accounts"/>
                    <input type="hidden" class="data-n9-s" name ='s_end_date' value="<?php echo isset($filter['end_date'])?$filter['end_date']:''; ?>" data-table="store_accounts"/>
                   

                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table data-table="store_accounts" data-currentpage="1" data-url="<?php echo base_url() . 'reports/'.$action.'_store/'; ?>" class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only" id="sortable_table" data-callback="true">
                            <thead>
                                <tr>
                                    <th class="text-left">STT</th>
                                    <th class="text-left">Tên nhóm khách hàng</th>
                                    <th class="text-left">Tài khoản khách nợ</th>
                                    <th class="text-left">Tài khoản nợ khách</th>
                                </tr>
                            </thead>
                      <!--       <?php var_dump($_SESSION['bao_cao_chi_tiet_cong_no_nhom_khach_hang']) ?> -->
                            <tbody>

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
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top" data-table="store_accounts">
    </div>
    <script type="text/javascript">
        function n9_grid_callback(data_table,result) {
            if(data_table == 'store_accounts') {
                $('#debt_start').text(result.debt_start);
                $('#debt_end').text(result.debt_end);
            }
        }
        $( document ).ready(function() {
            load_list('store_accounts');
            
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