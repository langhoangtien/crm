<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url(); ?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet"
          type="text/css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/n9-modal.css" type="text/css" media="screen"/>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/customer.js'; ?>"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <style>
        table button {
            background: none !important;
            border: none;
            padding: 0 !important;

            color: #069;
            text-decoration: underline;
            cursor: pointer;
        }
    </style>

<?php
//echo '<pre>';
//print_r($data);
//print_r(json_encode(array_values($chartType2)));
//echo '</pre>';
//die();

$link_list = base_url() . 'customers/listkh';
?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 text-right">
                <div class="pull-right-btn" style="display: inline-block">
                    <a href="<?php echo base_url() . 'customers/categories'; ?>" id="mange_category" class="btn btn-primary"><span class=""><?php echo lang('customers_mange_category'); ?></span></a>
                </div>
                <div class="pull-right" style="margin: 0 5px;">
                    <form action="customers/download_excel" method="post" accept-charset="utf-8">
                        <button type="submit" class="btn btn-primary">Xuất file</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12">
                <?php $this->load->view('customers/short_list'); ?>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4 col-lg-offset-4 col-md-offset-4 col-sm-offset-4"
                     style="margin-bottom: 30px">
                    <a href="<?php echo $link_list; ?>" class="btn btn-primary"
                       style="width: 100%">Xem thêm</a>
                </div>
            </div>

            <div class="col-md-12" style="padding-bottom: 20px;">
                <div class="col-md-6" style="padding-bottom: 20px;">
                    <select id="mySelect" onclick="statistical()" style="height: 40px;">
                        <option value="groupCustomer">Nhóm khách hàng</option>
                        <option value="typeCustomer">Loại khách hàng</option>
                        <option value="jobBusiness">Ngành nghề kinh doanh</option>
                        <option value="geographicalArea">Khu vực</option>
                        <option value="companyForm">Hình thức công ty</option>
                    </select>
                </div>

                <div class="col-md-12" id="containerPie"></div>

                <div class="col-md-12" id="chart_typeCustomer" style="display: none;"></div>

                <div class="col-md-12" id="chart_jobBusiness" style="display: none;"></div>

                <div class="col-md-12" id="chart_geographicalArea" style="display: none;"></div>

                <div class="col-md-12" id="chart_company_form" style="display: none;"></div>

                <!--                <a href="--><?php //echo $link_list; ?><!--" class="btn btn-primary"-->
                <!--                   style="width: 100%;background-color: #555;color: #fff; border-radius: 0;">Xem thêm</a>-->
            </div>
            <!---->
            <!--            <div class="col-md-6" style="padding-bottom: 20px;">-->
            <!--                <div id="containerBar"></div>-->
            <!--                <a href="--><?php //echo $link_list; ?><!--" class="btn btn-primary"-->
            <!--                   style="width: 100%;background-color: #555;color: #fff; border-radius: 0;">Xem thêm</a>-->
            <!--            </div>-->

            <div class="col-md-6" style="padding-bottom: 20px;">
                <div>
                    <h4 style="padding-bottom: 15px;">Top 10 KH có giá trị hợp đồng lớn nhất</h4>
                </div>
                <div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th><?php echo lang('customers_no'); ?></th>
                            <th><?php echo lang('customers_name'); ?></th>
                            <th><?php echo lang('customers_contract'); ?></th>
                            <th><?php echo lang('customers_contract_value'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1; foreach ($top10ContractValue as $key => $customer) { ?>
                         
                                <tr>
                                    <td>
                                     <?php echo $i; $i++; ?>
                                    </td>
                                    <td>
                                  <a href="<?php echo base_url('customers/view/'.$customer['person_id'].'/3')  ?>"><?php echo $customer['customer_name'] ?></a>
                                    </td>
                                    <td>
                                       <a href="<?php echo base_url('contracts/view/customer/'.$customer['id'])  ?>"><?php echo $customer['code'] ?></a>
                                    </td>

                                    <td style="text-align: right; padding-right: 10px;"><?php echo number_format($customer['price'], 0, '', ','); ?></td>
                                </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
              
            </div>

            <div class="col-md-6" style="padding-bottom: 20px;">
                <div>
                    <h4 style="padding-bottom: 15px;"><?php echo lang('customers_top_10_income'); ?></h4>
                </div>
                <div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th><?php echo lang('customers_no'); ?></th>
                            <th><?php echo lang('customers_name'); ?></th>
                            <th style="width: 20%"><?php echo lang('customers_num_of_sign_contract'); ?></th>
                            <th><?php echo lang('customers_all_contract_income'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1; foreach ($top10Income as $key => $customer) { ?>

                                <tr>
                                    <td>
                                     <?php echo $i; $i++ ?>
                                    </td>
                                    <td>
                                   <a href="<?php echo base_url('customers/view/'.$customer['person_id'].'/3')  ?>"><?php echo $customer['customer_name'] ?></a>
                                    </td>
                                    <td>
                                   <a href="<?php echo base_url('reports/customer/'.$customer['person_id'])  ?>"><?php echo $customer['num_of_contract'] ?></a>
                                    </td>

                                    <td style="text-align: right; padding-right: 10px;"><?php echo number_format($customer['price_total'], 0, '', ','); ?></td>
                                </tr>
                            
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
             
            </div>
        </div>
    </div>

<?php //$this->load->view("customers/partial/customer_menu"); ?>
    <script>
        function statistical() {
            var x = document.getElementById("mySelect").value;
            if (x == 'groupCustomer') {
                document.getElementById("containerPie").style.display = "block";
                document.getElementById("chart_typeCustomer").style.display = "none";
                document.getElementById("chart_jobBusiness").style.display = "none";
                document.getElementById("chart_geographicalArea").style.display = "none";
                document.getElementById("chart_company_form").style.display = "none";
            } else if (x == 'typeCustomer') {
                document.getElementById("containerPie").style.display = "none";
                document.getElementById("chart_typeCustomer").style.display = "block";
                document.getElementById("chart_jobBusiness").style.display = "none";
                document.getElementById("chart_geographicalArea").style.display = "none";
                document.getElementById("chart_company_form").style.display = "none";
            } else if (x == 'jobBusiness') {
                document.getElementById("containerPie").style.display = "none";
                document.getElementById("chart_typeCustomer").style.display = "none";
                document.getElementById("chart_jobBusiness").style.display = "block";
                document.getElementById("chart_geographicalArea").style.display = "none";
                document.getElementById("chart_company_form").style.display = "none";
            } else if (x == 'geographicalArea') {
                document.getElementById("containerPie").style.display = "none";
                document.getElementById("chart_typeCustomer").style.display = "none";
                document.getElementById("chart_jobBusiness").style.display = "none";
                document.getElementById("chart_geographicalArea").style.display = "block";
                document.getElementById("chart_company_form").style.display = "none";
            } else if (x == 'companyForm') {
                document.getElementById("containerPie").style.display = "none";
                document.getElementById("chart_typeCustomer").style.display = "none";
                document.getElementById("chart_jobBusiness").style.display = "none";
                document.getElementById("chart_geographicalArea").style.display = "none";
                document.getElementById("chart_company_form").style.display = "block";
            }

        }
    </script>

    <script type="text/javascript">

        var CUSTOMER_DASHBOARD = {
            drawTrend: function (xAxis, seriesData) {
                Highcharts.chart('containerTrend', {
                    credits: false,
                    chart: {
                        style: {
                            fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif'
                        },
                    },
                    title: {
                        align: 'left',
                        text: '<h4 style="padding-bottom: 15px;">SỐ LƯỢNG KHÁCH HÀNG</h4>',
                        useHTML: true
                    },
                    xAxis: {
                        categories: xAxis
                    },
                    yAxis: {
                        title: {
                            text: null,
                        }
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },
                    legend: {
                        enable: false
                    },
                    series: [{
                        name: 'KHÁCH HÀNG',
                        data: seriesData
                    }]
                });
            }
        }

        $(document).ready(function () {
            // CUSTOMER_DASHBOARD.drawTrend(TrendxAxis, TrendseriesData);
            $('#filter_week, #filter_month').unbind('click').bind('click', function () {
                $('#filter').text('Lọc theo: ' + $(this).text());
                var _data = {};
                _data['range_type'] = $(this).data('range_type');
                coreAjax.call(
                    '<?php echo site_url("customers/dashboard_filter");?>',
                    _data,
                    function (response) {
                        CUSTOMER_DASHBOARD.drawTrend(response.TrendxAxis, response.TrendseriesData);
                    }
                );
            });

        });
        var TrendxAxis = <?php echo json_encode(array_keys($chartSL)); ?>;
        var TrendseriesData = <?php echo json_encode(array_values($chartSL)); ?>;

        var PieData = <?php echo json_encode(array_values($chartType)); ?>;

        var TierData = <?php echo json_encode(array_values($chart_tier)); ?>;

        var BusinessTypeData = <?php echo json_encode(array_values($chart_business_type)); ?>;

        var GeographycalAreaData = <?php echo json_encode(array_values($chart_geographycal_area)); ?>;

        var CompanyFormData = <?php echo json_encode(array_values($chart_company_form)); ?>;


        var BarxAxis = <?php echo json_encode(array_keys($chartReference)); ?>;
        var BarseriesData = <?php echo json_encode(array_values($chartReference)); ?>;

        Highcharts.chart('containerPie', {
            credits: false,
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                style: {
                    fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif'
                },
            },
            title: {
                align: 'left',
                text: '<h4 style="padding-bottom: 15px;display: none;">NHÓM KHÁCH HÀNG</h4>',
                useHTML: true
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<a href="' + BASE_URL + '/customers/listkh">{point.name}: {point.percentage:.1f} %</a>',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true,
                    allowPointSelect: false,
                    point: {
                        events: {
// 	            legendItemClick: function(e){
// 	                e.preventDefault();
// 	            }
                        }
                    }
                }
            },
            legend: {
//         useHTML: true,
//         labelFormatter: function () {
//             return '<a href="'+ BASE_URL +'/customers/listkh">'+ this.name +'</a>';
//         }
            },
            series: [{
                name: 'Chiếm',
                colorByPoint: true,
                data: PieData
            }]
        });

        Highcharts.chart('chart_typeCustomer', {
            credits: false,
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                style: {
                    fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif'
                },
            },
            title: {
                align: 'left',
                text: '<h4 style="padding-bottom: 15px;display: none;">LOẠI KHÁCH HÀNG</h4>',
                useHTML: true
            },
            plotOptions: {
                pie: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<a href="' + BASE_URL + '/customers/listkh">{point.name}: {point.percentage:.1f} %</a>',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true,
                    allowPointSelect: false,
                    point: {
                        events: {
// 	            legendItemClick: function(e){
// 	                e.preventDefault();
// 	            }
                        }
                    }
                }
            },
            legend: {
//         useHTML: true,
//         labelFormatter: function () {
//             return '<a href="'+ BASE_URL +'/customers/listkh">'+ this.name +'</a>';
//         }
            },
            series: [{
                name: 'Chiếm',
                colorByPoint: true,
                data: TierData
            }]
        });

        Highcharts.chart('chart_jobBusiness', {
            credits: false,
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                style: {
                    fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif'
                },
            },
            title: {
                align: 'left',
                text: '<h4 style="padding-bottom: 15px;display: none;">NGÀNH NGHỀ KINH DOANH</h4>',
                useHTML: true
            },
            plotOptions: {
                pie: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<a href="' + BASE_URL + '/customers/listkh">{point.name}: {point.percentage:.1f} %</a>',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true,
                    allowPointSelect: false,
                    point: {
                        events: {
// 	            legendItemClick: function(e){
// 	                e.preventDefault();
// 	            }
                        }
                    }
                }
            },
            legend: {
//         useHTML: true,
//         labelFormatter: function () {
//             return '<a href="'+ BASE_URL +'/customers/listkh">'+ this.name +'</a>';
//         }
            },
            series: [{
                name: 'Chiếm',
                colorByPoint: true,
                data: BusinessTypeData
            }]
        });

        Highcharts.chart('chart_geographicalArea', {
            credits: false,
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                style: {
                    fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif'
                },
            },
            title: {
                align: 'left',
                text: '<h4 style="padding-bottom: 15px; display: none;">KHU VỰC</h4>',
                useHTML: false
            },
            plotOptions: {
                pie: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<a href="' + BASE_URL + '/customers/listkh">{point.name}: {point.percentage:.1f} %</a>',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true,
                    allowPointSelect: false,
                    point: {
                        events: {
// 	            legendItemClick: function(e){
// 	                e.preventDefault();
// 	            }
                        }
                    }
                }
            },
            legend: {
//         useHTML: true,
//         labelFormatter: function () {
//             return '<a href="'+ BASE_URL +'/customers/listkh">'+ this.name +'</a>';
//         }
            },
            series: [{
                name: 'Chiếm',
                colorByPoint: true,
                data: GeographycalAreaData
            }]
        });
        Highcharts.chart('chart_company_form', {
            credits: false,
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                style: {
                    fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif'
                },
            },
            title: {
                align: 'left',
                text: '<h4 style="padding-bottom: 15px;display: none;">Hình thức côngg ty</h4>',
                useHTML: true
            },
            plotOptions: {
                pie: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<a href="' + BASE_URL + '/customers/listkh">{point.name}: {point.percentage:.1f} %</a>',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true,
                    allowPointSelect: false,
                    point: {
                        events: {
// 	            legendItemClick: function(e){
// 	                e.preventDefault();
// 	            }
                        }
                    }
                }
            },
            legend: {
//         useHTML: true,
//         labelFormatter: function () {
//             return '<a href="'+ BASE_URL +'/customers/listkh">'+ this.name +'</a>';
//         }
            },
            series: [{
                name: 'Chiếm',
                colorByPoint: true,
                data: CompanyFormData
            }]
        });

        // Highcharts.chart('containerBar', {
        //     credits: false,
        //     chart: {
        //         type: 'bar',
        //         style: {
        //             fontFamily: '"Helvetica Neue",Helvetica,Arial,sans-serif'
        //         },
        //     },
        //     title: {
        //         align: 'left',
        //         text: '<h4 style="padding-bottom: 15px;">NGUỒN KHÁCH HÀNG</h4>',
        //         useHTML: true
        //     },
        //     xAxis: {
        //         categories: BarxAxis
        //     },
        //     yAxis: {
        //         title: {
        //             text: null,
        //         }
        //     },
        //     plotOptions: {
        //         series: {
        //             dataLabels: {
        //                 enabled: true
        //             }
        //         }
        //     },
        //     series: [{
        //         name: 'KHÁCH HÀNG',
        //         data: BarseriesData
        //     }],
        //     legend: {
        //         enable: false
        //     }
        // });
    </script>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/quick-nav.js'; ?>"></script>
<?php $this->load->view("partial/footer"); ?>