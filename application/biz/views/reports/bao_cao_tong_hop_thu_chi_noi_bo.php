<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
    <div class="row">
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_start"></span>
                <p>Tổng thu</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_end"></span>
                <p>Tổng chi</p>
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
                    Báo cáo thu chi chi tiết nội bộ
                    <?php if(!empty($time_arr)): ?>
                        <small class="reports-range"><?php echo implode(' - ', $time_arr);?></small>
                    <?php endif; ?>
                    <span class="pull-right"></span>
                    <i class="fa fa-spinner fa-spin loading" id="store_accounts_loading" style="display: none;"></i>
                    <input type="hidden" class="data-n9-s" name ='s_start_date' value="<?php if(isset($filter['start_date'])) echo $filter['start_date']; ?>" data-table="store_accounts"/>
                    <input type="hidden" class="data-n9-s" name ='s_end_date' value="<?php if(isset($filter['end_date'])) echo $filter['end_date']; ?>" data-table="store_accounts"/>

                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table data-table="store_accounts" data-currentpage="1" data-url="<?php echo base_url() . 'reports/'.$action.'_store/'; ?>" class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only" id="sortable_table" data-callback="true" class="table table-bordered table-striped sortable">
                            <thead>
                                <tr>
                                    <th style="width: 20%;" colspan="2" data-mainsort="3" style="text-align: center;" class="nosort" data-sortcolumn="1" data-sortkey="1-0">Chứng từ</th>
                                    <th style="width: 20%;" class="text-center" colspan="1">Diễn dải</th>
                                    <th style="width: 20%;" class="text-center">Lý do</th>
                                    <th style="width: 20%;" class="text-center">Người nhận</th>
                                    <th class="text-left">Người phê duyệt</th>
                                     <th colspan="2" data-mainsort="3" style="text-align: center;" class="nosort" data-sortcolumn="1" data-sortkey="1-0">Số tiền</th>
                                </tr>
                                <tr>
                                    <th style="width: 7%" colspan="1" data-mainsort="1" data-firstsort="desc" class="nosort" data-sortcolumn="1" data-sortkey="1-1">Số</th>
                                    <th style="width: 20%">Ngày</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="width: 20%" colspan="1" data-mainsort="1" data-firstsort="desc" class="nosort" data-sortcolumn="1" data-sortkey="1-1">Tiền thu</th>
                                    <th style="width: 20%" colspan="1" data-mainsort="1" data-firstsort="desc" class="nosort" data-sortcolumn="1" data-sortkey="1-1">Tiền chi</th>
                                </tr>
                            </thead>
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