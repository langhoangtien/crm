<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
    <div class="row">
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info" id="specific_cus_balance_start">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_start" class="total"><?php echo $debt_start ?></span>
                <p>Tồn đầu kỳ</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info" id="specific_cus_balance_end">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_end" class="total"><?php echo $debt_end ?></span>
                <p>Tồn cuối kỳ</p>
            </div>
        </div>
    </div>
<?php
if(!empty($start_date))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($start_date));

if(!empty($end_date))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($end_date));

?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-piluku reports-printable">
                <div class="panel-heading reports" id="specific_cus_title">
                     <strong class="title total"><?php echo $specific_cus_title . ' - ' . $item_name . ' - ' ; ?></strong>
                    
                    <?php if(!empty($time_arr)): ?>
                        <small class="reports-range"><?php echo implode(' - ', $time_arr);?></small>
                    <?php else : ?>
                         <small class="reports-range"><?php echo lang('common_all_time');?></small>
                    <?php endif; ?>
                    
                    <span class="pull-right"></span>
                    <i class="fa fa-spinner fa-spin loading" id="store_accounts_loading" style="display: none;"></i>
                     <input type="hidden" class="data-n9-s" name ='s_start_date' value="<?php if(isset($filter['start_date'])) echo $filter['start_date']; ?>" data-table="store_accounts"/>
                    <input type="hidden" class="data-n9-s" name ='s_end_date' value="<?php if(isset($filter['end_date'])) echo $filter['end_date']; ?>" data-table="store_accounts"/>
                    <input type="hidden" class="data-n9-s" name="s_customer_id" value="<?php echo $filter['customer_id']; ?>" data-table="store_accounts"/>
                    <input type="hidden" class="data-n9-s" name="s_options" value="<?php echo $filter['customer_balance_options']; ?>" data-table="store_accounts"/>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table data-table="store_accounts" data-currentpage="1" data-url="<?php echo base_url() . 'reports/'.$action.'_store/'; ?>" class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only" id="sortable_table" data-callback="true">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Mã sản phẩm</th>
                                    <th>Tồn đầu</th>
                                    <th>Xuất hàng</th>
                                    <th>Nhập hàng</th>
                                    <th>Tồn cuối</th>
                                    <th>Ghi chú</th>
                                    <th>Địa điểm</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php echo $body ?>
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