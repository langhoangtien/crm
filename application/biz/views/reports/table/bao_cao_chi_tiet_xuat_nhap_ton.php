<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
    <div class="row">
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_start">..</span>
                <p>Tồn đầu kỳ</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span id="debt_end">..</span>
                <p>Tồn cuối kỳ</p>
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
                   <strong>Báo cáo xuất nhập tồn</strong>
                    <?php if(!empty($time_arr)): ?>
                        <small class="reports-range"><?php echo implode(' - ', $time_arr);?></small>
                    <?php endif; ?>
                    <span class="pull-right">
                         <div class="input-group">
                            <input type="text" class="form-control" id="tim-kiem" placeholder="Tìm kiếm ...">
                        </div>
                        <div id='ket_qua' class="text-primary"></div>

                    </span>

                    <i class="fa fa-spinner fa-spin loading" id="store_accounts_loading" style="display: none;"></i>
                     <input type="hidden" class="data-n9-s" name ='s_start_date' value="<?php if(isset($filter['start_date'])) echo $filter['start_date']; ?>" data-table="store_accounts"/>
                    <input type="hidden" class="data-n9-s" name ='s_end_date' value="<?php if(isset($filter['end_date'])) echo $filter['end_date']; ?>" data-table="store_accounts"/>
  
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table data-table="store_accounts" data-currentpage="1" data-url="<?php echo base_url() . 'reports/'.$action.'_store/'; ?>" class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only" id="sortable_table" data-callback="true">
                            <thead>
                            <tr>
                                <th style="font-weight: bold;" colspan="2" rowspan="2">Mặt hàng</th>
                                <th style="font-weight: bold;" colspan="2" rowspan="2">Tồn đầu kỳ</th>
                                <th style="font-weight: bold;" colspan="4">Phát sinh trong kỳ</th>
                                <th style="font-weight: bold;" colspan="2" rowspan="2">Tồn cuối kỳ</th>
                            </tr>
                            <tr>
                                <th style="font-weight: bold;" colspan="2">Nhập</th>
                                <th style="font-weight: bold;" colspan="2">Xuất</th>
                            </tr>
                            <tr></tr>
                            <tr>
                                <th style="font-weight: bold;">Mã HH</th>
                                <th style="font-weight: bold;">Tên HH</th>
                                <th style="font-weight: bold;">Số lượng</th>
                                <th style="font-weight: bold;">Thành tiền</th>
                                <th style="font-weight: bold;">Số lượng</th>
                                <th style="font-weight: bold;">Thành tiền</th>
                                <th style="font-weight: bold;">Số lượng</th>
                                <th style="font-weight: bold;">Thành tiền</th>
                                <th style="font-weight: bold;">Số lượng</th>
                                <th style="font-weight: bold;">Thành tiền</th>
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


        $("#tim-kiem").on("keyup", function () {
        if (this.value.length > 0) {   
          $("tbody tr").hide().filter(function () {
            return $(this).text().toLowerCase().indexOf($("#tim-kiem").val().toLowerCase()) != -1;
          }).show();
            result = $("tbody tr[style*='display: table-row']").length;
            $('#ket_qua').text('Có '+result+' kết quả')
         
        }  
        else { 
            
            $("tbody tr").show();
        }
    })


<?php

 ?>

    </script>
<?php $this->load->view("partial/footer"); ?>