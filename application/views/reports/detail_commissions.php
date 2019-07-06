<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>

    <div class="row">
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="detail_commissions_order_total">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                 <p><?php echo lang('reports_order_value')?></p>
            </div>
        </div>

        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="detail_commissions_profit">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p><?php echo lang('reports_profit')?></p>
            </div>
        </div>
				<div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="detail_commissions_profit_before">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p><?php echo lang('reports_profit_before_charging_commission')?></p>
            </div>
        </div>

        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="detail_commissions_value">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                 <p><?php echo lang('reports_summary_commission')?></p>
            </div>
        </div>
    </div>
<?php
if(!empty($filter['start_date']))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['start_date']));

if(!empty($filter['end_date']))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['end_date']));

$filter['location_ids'] = implode(',', $filter['selected_location_ids']);
$filter['group_ids']    = implode(',', $filter['group_ids']);
$filter['employees']    = implode(',', $filter['employees']);

?>
    <div class="row manage-table type-2">
        <div class="col-md-12">
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <span class="title" data-tab="detail_commission"><?php echo lang('reports_summary_commission_by_employee')?></span>
			
                        <?php if(!empty($time_arr)): ?>
                            <small class="reports-range"><?php echo implode(' - ', $time_arr);?></small>
                        <?php endif; ?>

                        <i class="fa fa-spinner fa-spin loading" id="detail_commission_loading" style="display: none;"></i>
                    </h3>
                </div>
                <div id="detail_commission" class="panel-body nopadding table_holder table-responsive tabs" style="display: block;">
                    <input type="hidden" class="data-n9-s" name="s_start_date" value="<?php echo $filter['start_date']; ?>" data-table="detail_commission">
                    <input type="hidden" class="data-n9-s" name="s_end_date" value="<?php echo $filter['end_date']; ?>" data-table="detail_commission">
                    <input type="hidden" class="data-n9-s" name="s_employees" value="<?php echo $filter['employees']; ?>" data-table="detail_commission">
                    <input type="hidden" class="data-n9-s" name="s_group_ids" value="<?php echo $filter['group_ids']; ?>" data-table="detail_commission">
                    <input type="hidden" class="data-n9-s" name="s_location_ids" value="<?php echo $filter['location_ids']; ?>" data-table="detail_commission">

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table data-table="detail_commission" data-currentpage="1" data-callback="true" data-url="<?php echo base_url(); ?>reports/detail_commissions_store/" class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only table-tree">
                                <thead>
                                <tr>
                                    <th class="hidden-print" style="width: 25px;"><a href="#" class="expand_all">&nbsp;</a></th>
                                    <th style="width: 15%;" data-field="sale_time" class="header headerSortDown">Thời gian</th>
                                    <th style="width: 10%;">Mã đơn hàng</th>
                                    <th style="width: 15%;">Tổng giá trị</th>
                                    <th style="width: 15%;">Lợi nhuận</th>
                                    <th style="width: 20%;">Hoa hồng</th>
                                    <th>Ghi chú</th>
                                    <td align="center" style="width: 50px;">#</td>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top" data-table="detail_commission">
    </div>
    <script type="text/javascript">
        $( document ).ready(function() {
            load_list('detail_commission');

            <?php if(!empty($filter['customer_id']) && $filter['customer_id'] == -1) :?>
            toastr.error('Phải chọn một khách hàng', 'Lỗi');
            <?php endif; ?>

            $('body').on('click','[data-tab]',function(){
                $( "[data-tab]" ).removeClass('active');
                var data_id = $(this).attr('data-tab');

                $('.manage-table.type-2 .tabs').hide();
                $(this).addClass('active');
                $('[data-table="'+data_id+'"] tbody').html('');
                $('#'+data_id).show();

                load_list(data_id);

                $('.data-n9-pagination').hide();

                $('.data-n9-pagination[data-table="'+data_id+'"]').show();
            });

            // order detail
            $('body').on('click','.table-tree .expand_all',function(){
                var symbol = $(this).text();
                var tr_element = $(this).closest('tr');
                var table_element = $(this).closest('table');
                var id = tr_element.attr('data-tree');

                var tr_child = table_element.find('tr[data-parent="'+id+'"]');

                if(symbol == '-'){
                    tr_child.find('.innertable').css('display', 'none');
                    $(this).text('+');

                }else{
                    tr_child.find('.innertable').css('display', 'table-cell');
                    $(this).text('-');

                    $.ajax({
                        type: "POST",
                        url: BASE_URL + 'reports/specific_cus_order_detail',
                        data: {
                            sale_id : id
                        },
                        success: function(string){
                            tr_child.find('.innertable').html(string);
                        }
                    });
                }
            });
        });

        function n9_grid_callback(data_table,result) {
            $.each(result.total_list, function( index, value ) {
                $('#'+index+' .total').text(value);
            });
        }

    </script>
    <style type="text/css">
        .manage-table.type-2 .top-control {
            padding-top: 0;
            padding-bottom: 0px;
        }

        .manage-table.type-2 .panel-heading {
            height: 20px;
            line-height: 20px;
            position: relative;
        }

        .manage-table.type-2 .loading {
            bottom: -38px;
            left: 20px;
            position: absolute;
        }

        .manage-table.type-2 .panel-heading .panel-title {
            height: 20px;
            line-height: 20px;
        }
        .manage-table .panel-body {
            padding: 15px;
        }

        .manage-table.type-2 .tabs {
            padding-top: 0;
            padding-bottom: 0;
            border-top: 0;
        }

        .manage-table tr th {
            line-height: 27px !important;
        }

        .manage-table tr td {
            height: initial !important;
        }

        .manage-table tr td:last-child, .manage-table tr th:last-child {
            padding-left: 4px;
        }

        #s_options {
            float: right;
            width: 200px;
        }


    </style>
    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <div id="my_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    </div>
    <div id="my_table" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    </div>
    <div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
<?php $this->load->view("partial/footer"); ?>