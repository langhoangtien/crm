<!DOCTYPE html>
<head>
    <meta charset="UTF-8" />
    <title><?php
    $congviec = count($this->Employee->get_task_alert($this->Employee->get_logged_in_employee_info()->id));
    $this->load->helper('demo');
    echo !is_on_demo_host() ?  $this->config->item('company').' -- '.lang('common_powered_by').' 4Biz by LifeTek' : 'Demo - 4Biz by LifeTek | Easy to use Online POS Software' ?></title>
    <link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>/assets/css/n9-modal.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>/assets/css/custom.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>/assets/css/font-awesome.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->
    <base href="<?php echo base_url();?>" />

    <link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon"/>
    <script type="text/javascript">

        var datatableOption = {
                            // "bFilter": false,
                            "bInfo": false,
                            "iDisplayStart ": 10,
                            "iDisplayLength": 10,
                            "bLengthChange": false,
                            "lengthChange": false,
                            "pageLength": 20,
                            "language": {
                                "paginate": {
                                    "first":      "First",
                                    "last":       "Last",
                                    "next":       "&gt;",
                                    "previous":   "&lt",
                                    "class":"vi"
                                },
                                "search":         "Tìm kiếm:",
                            },
                        };


                        var SITE_URL= "<?php echo site_url(); ?>";
                        var BASE_URL= "<?php echo base_url(); ?>";
                        var ENABLE_SOUNDS = <?php echo $this->config->item('enable_sounds') ? 'true' : 'false'; ?>;
                        var JS_DATE_FORMAT = <?php echo json_encode(get_js_date_format()); ?>;
                        var JS_TIME_FORMAT = <?php echo json_encode(get_js_time_format()); ?>;
                        var LOCALE =  <?php echo json_encode(get_js_locale()); ?>;
                        var IS_MOBILE = <?php echo $this->agent->is_mobile() ? 'true' : 'false'; ?>;
                        var DISABLE_QUICK_EDIT = <?php echo $this->config->item('disable_quick_edit') ? 'true' : 'false'; ?>;
                    </script>
                    <?php
                    $this->load->helper('assets');
                    foreach(get_css_files() as $css_file) { ?>
                        <link rel="stylesheet" type="text/css" href="<?php echo base_url().$css_file['path'].'?'.ASSET_TIMESTAMP;?>" />
                    <?php } ?>
                    <?php foreach(get_js_files() as $js_file) { ?>
                        <script src="<?php echo base_url().$js_file['path'].'?'.ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>
                    <?php } ?>
                    <script type="text/javascript">
                        COMMON_SUCCESS = <?php echo json_encode(lang('common_success')); ?>;
                        COMMON_ERROR = <?php echo json_encode(lang('common_error')); ?>;

                        bootbox.addLocale('ar', {
                            OK : 'حسنا',
                            CANCEL : 'إلغاء',
                            CONFIRM : 'تأكيد'
                        });

                        bootbox.addLocale('km', {
                            OK :'យល់ព្រម',
                            CANCEL : 'បោះបង់',
                            CONFIRM : 'បញ្ជាក់ការ'
                        });
                        bootbox.setLocale(LOCALE);
                        $.ajaxSetup ({
                            cache: false,
                            headers: { "cache-control": "no-cache" }
                        });
                        toastr.options = {
                          "closeButton": true,
                          "debug": false,
                          "positionClass": "toast-top-right",
                          "onclick": null,
                          "showDuration": "1000",
                          "hideDuration": "1000",
                          "timeOut": "5000",
                          "extendedTimeOut": "1000",
                          "showEasing": "swing",
                          "hideEasing": "linear",
                          "showMethod": "fadeIn",
                          "hideMethod": "fadeOut"
                      }

                      $.fn.editableform.buttons =
                      '<button tabindex="-1" type="submit" class="btn btn-primary btn-sm editable-submit">'+
                      '<i class="icon ti-check"></i>'+
                      '</button>'+
                      '<button tabindex="-1" type="button" class="btn btn-default btn-sm editable-cancel">'+
                      '<i class="icon ti-close"></i>'+
                      '</button>';

                      $.fn.editable.defaults.emptytext = <?php echo json_encode(lang('common_empty')); ?>;

                      $(document).ready(function()
                      {
                        $(".wrapper.mini-bar .left-bar").hover(
                            function() {
                                $(this).parent().removeClass('mini-bar');
                            }, function() {
                                $(this).parent().addClass('mini-bar');
                            }
                            );

                        $('.menu-bar').click(function(e){
                            e.preventDefault();
                            $(".wrapper").toggleClass('mini-bar');
                        });

            //Ajax submit current location
            $(".set_employee_current_location_id").on('click',function(e)
            {
                e.preventDefault();

                var location_id = $(this).data('location-id');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('home/set_employee_current_location_id'); ?>',
                    data: {
                        'employee_current_location_id': location_id,
                    },
                    success: function(){
                        window.location.reload(true);
                    }
                });

            });

            $(".set_employee_language").on('click',function(e)
            {
                e.preventDefault();

                var language_id = $(this).data('language-id');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url('employees/set_language'); ?>',
                    data: {
                        'employee_language_id': language_id,
                    },
                    success: function(){
                        window.location.reload(true);
                    }
                });

            });

            /* Validate Form Fields: Begin */
            var $form_validate = $('.form-validate');
            if ($form_validate.size() > 0) {
                $form_validate.validate();
            }
            /* Validate Form Fields: End */

            <?php
            //If we are using on browser close (NULL or ""; both false) then we want to keep session alive
            if (!$this->Appconfig->get_raw_phppos_session_expiration())
            {
                ?>
            //Keep session alive by sending a request every 5 minutes
            setInterval(function(){$.get('<?php echo site_url('home/keep_alive'); ?>');}, 300000);
        <?php } ?>


        $('#dTableC').DataTable(datatableOption);
        $('#dTableZ').DataTable(datatableOption);

        $('#new_customer_count').on('click',function(){
         $('#warning_add_new_customer_modal').modal('show');
     });
        $('#dTableD').DataTable({
            "searching":        false,
            "lengthChange": false,
                            // "sPaginationType": "bootstrap",
                            "pageLength": 20,
                            "language": {
                                "paginate": {
                                    "first":      "First",
                                    "last":       "Last",
                                    "next":       "&gt;",
                                    "previous":   "&lt",
                                    "class":"vi"
                                },
                                "search":         "Tìm kiếm:",
                            },
                        });

        $('#new_contracts_count').on('click',function(){
         $('#warning_add_new_contracts_modal').modal('show');
     });

    });
</script>

</head>
<body>

    <?php //var_dump($all_allowed_modules); die(); ?>

    <div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
    <div class="modal fade hidden-print" id="myModalDisableClose" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static"></div>

    <div class="wrapper">

        <!-- Menu config -->
        <?php 

        if($this->uri->segment(1) != 'config'){
         $this->load->view("partial/menu_config");
     }

     ?>																								
     <div class="left-bar hidden-print" >
        <div class="admin-logo" style="<?php echo isset($location_color) && $location_color ? 'background-color: '.$location_color : ''; ?>">
            <div class="logo-holder pull-left">
                <a href="<?php echo site_url('home'); ?>">
                    <?php echo img(
                        array(
                            'src' => $this->Appconfig->get_logo_image(),
                            'class'=>'hidden-print logo',
                            'id'=>'header-logo',
                            'style' => "max-height: 60px;"
                        )); ?>
                    </a>
                </div>
            </div>
            <!-- admin-logo -->

            <ul class="list-unstyled menu-parent" id="mainMenu">


                <li  <?php echo $this->uri->segment(1)=='home'  ? 'class="active"' : ''; ?>>
                    <a tabindex = "-1" href="<?php echo site_url('home'); ?>" class="waves-effect waves-light">
                        <i class="icon ti-dashboard"></i>
                        <span class="text"><?php echo lang('common_dashboard'); ?></span>
                    </a></li>
                    <?php

                    foreach($all_allowed_modules as $module) { ?>
                        <?php if (empty($module->main_menu) || in_array($module->module_id, $hiddenMenus)) continue; ?>
                        <li <?php echo $module->module_id==$this->uri->segment(1)  ? 'class="active"' : ''; ?>>
                            <?php if ($module->module_id == 'sales') {?>
                                <a tabindex = "-1" href="<?php echo site_url("sales/suspended");?>"  class="waves-effect waves-light">
                                    <i class="icon ti-<?php echo $module->icon; ?>"></i>
                                    <span class="text"><?php echo lang("module_".$module->module_id) ?></span>
                                </a>

                            <?php } else if ($module->module_id == 'receivings') {?>

                               <a tabindex = "-1" href="<?php echo site_url("receivings/list_receiving");?>"  class="waves-effect waves-light">
                                <i class="icon ti-<?php echo $module->icon; ?>"></i>
                                <span class="text"><?php echo lang("module_".$module->module_id) ?></span>
                            </a>

                            <?php  ?>

                        <?php  }else{ ?>
                            <a tabindex = "-1" href="<?php echo site_url("$module->module_id");?>"  class="waves-effect waves-light">
                                <i class="icon ti-<?php echo $module->icon; ?>"></i>
                                <span class="text"><?php echo lang("module_".$module->module_id) ?></span>
                            </a>
                        <?php }?>

                    </li>
                <?php } ?>

                <?php
                if ($this->config->item('timeclock'))
                {
                    ?>
                    <li <?php echo 'timeclocks'==$this->uri->segment(1)  ? 'class="active"' : ''; ?>>
                        <a tabindex = "-1" href="<?php echo site_url("timeclocks");?>">
                            <i class="icon ti-alarm-clock"></i>
                            <span class="text"><?php echo lang("employees_timeclock") ?></span>
                        </a>
                    </li>
                    <?php
                }
                ?>


                <li>
                    <?php
                    if ($this->config->item('track_cash') && $this->Register->is_register_log_open()) {
                        $continue = $this->config->item('timeclock') ? 'timeclocks' : 'logout';
                        echo anchor("sales/closeregister?continue=$continue",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
                    } else {

                        if ($this->config->item('timeclock') && $this->Employee->is_clocked_in())
                        {
                            echo anchor("timeclocks",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
                        }
                        else
                        {
                            echo anchor("home/logout",'<i class="icon ti-power-off"></i><span class="text">'.lang("common_logout").'</span>', array('tabindex' => '-1'));
                        }
                    }
                    ?>

                </li>
            </ul>
        </div>
        <!-- left-bar -->

        <div class="content" id="content">
            <div class="overlay hidden-print"></div>
            <div class="top-bar hidden-print">
                <nav class="navbar navbar-default top-bar">
                    <div class="menu-bar-mobile" id="open-left"><i class="ti-menu"></i></div>
                    <div class="nav navbar-nav top-elements navbar-breadcrumb hidden-xs">
                        <?php
                        $this->load->helper('breadcrumb');
                        echo create_breadcrumb(); ?>
                    </div>

                    <ul class="nav navbar-nav navbar-right top-elements">
                        <?php if ($this->config->item('show_clock_on_header')) { ?>
                            <li>

                                <?php
                                $url = 'javascript:void(0);';

                                if ($this->config->item('timeclock'))
                                {
                                    $url = site_url('timeclocks');
                                }

                                ?>
                                <a href="<?php echo $url;?>" class="visible-lg">
                                    <?php echo date(get_time_format()); ?>
                                    <?php echo date(get_date_format()) ?>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(($this->uri->segment(1)=='sales' && $this->uri->segment(2) != 'receipt' && $this->uri->segment(2) != 'complete') || ($this->uri->segment(1)=='receivings' && $this->uri->segment(2) != 'receipt' && $this->uri->segment(2) != 'complete')) { ?>
                            <li class="dropdown">
                                <a tabindex = "-1" href="#" class="fullscreen" data-toggle="" role="button" aria-expanded="false"><i class="ion-arrow-expand  icon-notification"></i></a>
                            </li>
                            <li class="dropdown">
                                <a tabindex = "-1" data-target="#" class="" data-toggle="" role="button" aria-expanded="false"><i class="ion-bag  icon-notification"></i><span class="badge info-number cart cart-number count">0</span></a>
                            </li>

                        <?php } ?>
                        <?php if (1) {?>
                           <li class="dropdown">
                             <?php if($show_warning_add_new_customer){ $aa = count($new_customers) ?>

                               <a id="new_customer_count" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="ion-person-add  icon-notification"></i><span class="badge info-number count bell" ><?php echo $aa;?></span></a>
                               <!-- Modal -->
                               <div class="modal fade" id="warning_add_new_customer_modal" tabindex="-1" role="dialog" aria-labelledby="chooseLocation" data-keyboard="false">
                                   <div class="modal-dialog modal-lg" role="document">
                                      <div class="modal-content">
                                         <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                               <span class="ti-close" aria-hidden="true"></span>
                                           </button>
                                           <h4 class="modal-title" id="chooseLocation"><?php echo 'Khách hàng mới hôm nay: '.'('.$aa.')'; ?></h4>
                                       </div>
                                       <div class="modal-body">
                                        <div>
                                           <table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableC">
                                              <thead>
                                                 <tr>
                                                    <th>STT</th>
                                                    <th class="hidden-xs"><?php echo lang('customers_customer_name'); ?></th>
                                                    <th class="hidden-xs"><?php echo lang('customers_customer_phoneNumber'); ?></th>
                                                    <th><?php echo lang('customers_customer_email'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                             <?php $i = 1; foreach($new_customers as $new_customer):?>
                                             <tr>
                                               <td><?php echo $i;?></td>
                                               <td class="hidden-xs"><?php echo $new_customer['last_name'].' '.$new_customer['first_name'];?></td>
                                               <td class="hidden-xs">
                                                  <?php echo $new_customer['phone_number'];?>
                                              </td>
                                              <td><?php echo $new_customer['email'];?></td>
                                          </tr>
                                          <?php $i++; endforeach;?>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          <?php } ?>
      </li>

      <?php if($check_view){ ?>
             <li>
                        <a tabindex = "" href="<?php echo base_url('groups/update_view/'.$check_view['id']) ?>">
                            <i class="icon ti-world"></i>
                            <span class="text"><?php echo $check_view['name']; ?></span>
                        </a>
            </li>
      <?php } ?>
      <!-- Số lượng nhân viên online -->


      <li class="dropdown">

       <a id="employee_count" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="ti-layout-list-thumb  icon-notification"></i><span class="badge info-number bell"><?php echo count($number_employee)  ?></span></a>
       <!-- Modal -->
       <div class="modal fade" id="list_employee" tabindex="-1" role="dialog" aria-labelledby="chooseLocation" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="ti-close" aria-hidden="true"></span>
                    </button>
                    <h4 class="modal-title" id="chooseLocation">Số nhân viên đang online</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableH">
                            <thead>
                                <tr>
                                    <th>
                                        Tài khoản
                                    </th>
                                    <th>
                                       Tên nhân viên
                                   </th>
                                   <th>
                                   Hoạt động lần cuối</th>
                               </tr>
                           </thead>
                           <tbody>
                            <?php  foreach ($number_employee as $key => $value) {

                               ?>
                               <tr>
                                <td style="color: green">
                                    <?php echo $value['username'] ?>
                                </td>
                                <td>
                                    <?php echo $value['first_name'] ?>
                                </td>
                                <td><?php echo $value['last_active'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

</li>

<script>
    $('#employee_count').on('click',function(){
        $('#list_employee').modal('show');
    });

    $('#dTableH').DataTable({
        "searching":        false,
        "lengthChange": false,
            // "sPaginationType": "bootstrap",
            "pageLength": 20,
            "language": {
                "paginate": {
                    "first":      "First",
                    "last":       "Last",
                    "next":       "&gt;",
                    "previous":   "&lt",
                    "class":"vi"
                },
                "search":         "Tìm kiếm:",
            },
        });
    </script>


    <li class="dropdown">
     <?php if($show_warning_add_new_contracts){ ?>
       <a id="new_contracts_count" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="ion-clipboard  icon-notification"></i><span class="new_contracts_count badge info-number count bell" ><?php echo $new_contracts_count;?></span></a>
       <!-- Modal -->
       <div class="modal fade" id="warning_add_new_contracts_modal" tabindex="-1" role="dialog" aria-labelledby="chooseLocation" data-keyboard="false">
           <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                 <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span class="ti-close" aria-hidden="true"></span>
                   </button>
                   <h4 class="modal-title" id="chooseLocation">Hợp đồng mới trong quý</h4>
               </div>
               <div class="modal-body">
                <div>
                   <table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableD">
                     <thead>
                      <tr>

                        <th data-field="code">Số hợp đồng</th>
                        <th data-field="name">Tên hợp đồng</th>
                        <?php if($option == 'customer'): ?>
                            <th>Tên khách hàng</th>
                        <?php endif; ?>
                        <?php if($option == 'supplier'): ?>
                            <th style="width: 10%;">Nhà cung cấp</th>
                            <th data-field="receiving_id">Số đơn hàng</th>
                        <?php endif; ?>

                        <th data-field="date_signing">Ngày ký</th>
                        <th data-field="status">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                  <?php foreach($new_contracts as $new_contract):?>
                    <tr style="cursor: pointer;">
                       <td><?php echo $new_contract['code']; ?></td>
                       <td><?php echo $new_contract['name']; ?></td>
                       <td><?php echo $new_contract['customer_name']; ?></td>
                       <td><?php echo $new_contract['date_signing']; ?></td>
                       <td><?php echo $new_contract['status']; ?></td>
                   </tr>
               <?php endforeach;?>
           </tbody>
       </table>
   </div>
</div>
</div>
</div>
</div>
<?php } ?>
</li>							 
<li class="dropdown">
    <?php $tong =$task_notice + $number_task + $so_hop_dong +$congviec + $thongbaothutien+$number_message_to +$norevenue +$norevenue_notice; ?>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="ion-chatbox-working  icon-notification"></i>
        <?php 
        if($tong>0){ ?>
            <span class="badge info-number count <?php echo $tong > 0 ? 'bell': '';?>" id="unread_message_count">
                <?php echo $tong ?>

            </span>
        <?php } ?>
    </a>
    <ul class="dropdown-menu animated fadeInUp wow message_drop neat_drop" data-wow-duration="1500ms" role="menu">
        <?php foreach ($this->Employee->get_messages(4) as $key => $value) { ?>
            <li>
                <a href="<?php echo site_url('messages/view/'.$value['message_id']); ?>">
                    <span class="avatar_left"><img src="<?php echo base_url(); ?>assets/assets/images/avatar-default.jpg" alt=""></span>
                    <span class="text_info"><?php echo $value['message']; ?></span>
                    <span class="time_info"><?php echo date(get_date_format().' '.get_time_format(), strtotime($value['created_at'])) ?> <i class="ion-record <?php echo !$value['message_read'] ? 'online' : ''?>"></i></span>
                </a>
            </li>
        <?php	} ?>
        <li class="bottom-links">
            <a href="<?php echo site_url('messages') ?>" class="last_info"><?php echo lang('common_see_all_notifications');?></a>
        </li>
        <?php if ($this->Employee->has_module_action_permission('messages','send_message',$this->Employee->get_logged_in_employee_info()->person_id)) {  ?>

            <li class="bottom-links">
                <a href="<?php echo site_url('messages/sent_messages'); ?>" class="last_info"><?php echo lang('common_view_sent_message') ?></a>
            </li>

            <li class="bottom-links">
                <a href="<?php echo site_url('messages'); ?>" class="last_info">Hộp thư đến
                    <?php if (($number_message_to>0)) { ?> 
                       <span class="badge info-number <?php echo $number_message_to > 0 ? 'bell': '';?>" style="margin: -1px;"><?php echo $number_message_to; ?></span>
                   <?php } ?>
               </a>
           </li>

           <li class="bottom-links">
            <a href="<?php echo site_url('messages/send_message') ?>" class="last_info"><?php echo lang('employees_send_message');?></a>
        </li>
    <?php } if($number_task>0){ ?>
    <li class="bottom-links">
        <a href="<?php echo site_url('tasks/approve_notice_d13') ?>" class="last_info">
           <?php echo lang('common_see_all_approve_notifications');?>
           <?php if (!empty($number_task)) { ?> 
               <span class="badge info-number bell" style="margin: -1px;"><?php echo $number_task; ?></span>
           <?php } ?>
       </a>
   </li>
   <?php } if($so_hop_dong>0){ ?>
   <li class="bottom-links">
    <a href="<?php echo site_url('contracts/contract_alert') ?>" class="last_info">
        Thay đổi trạng thái hợp đồng
        <span class="badge info-number <?php echo $so_hop_dong>0 ? 'bell':'' ?>" style="margin: -1px;"><?php echo $so_hop_dong ?></span>
    </a>
</li>
   <?php } ?>
   <li class="bottom-links">
    <a href="<?php echo site_url('tasks/task_notice') ?>" class="last_info">
        Dự án, công việc
        <?php if($task_notice>0){ ?>
        <span class="badge info-number <?php echo $task_notice>0 ? 'bell':'' ?>" style="margin: -1px;"><?php echo $task_notice ?></span>
    <?php } ?>
    </a>
</li>
<?php  if($thongbaothutien>0){ ?>
<li class="bottom-links">
    <a href="<?php echo site_url('tasks/task_finish_alert') ?>" class="last_info">
        Cần nghiệm thu thanh lý
        <span class="badge info-number <?php echo $thongbaothutien>0 ? 'bell':'' ?>" style="margin: -1px;"><?php echo $thongbaothutien ?></span>
    </a>
</li>
<?php } if($congviec>0){ ?>
<li class="bottom-links">
    <a href="<?php echo site_url('tasks/task_alert') ?>" class="last_info">
        Các công việc sắp đến hạn và quá hạn
        <span class="badge info-number <?php echo $congviec>0 ? 'bell':'' ?>" style="margin: -1px;"><?php echo $congviec ?></span>
    </a>
</li>
<?php } if($norevenue>0){ ?>
<li class="bottom-links">
    <a href="<?php echo site_url('tasks/norevenue_approve') ?>" class="last_info">
        Phê duyệt công việc không tạo doanh thu
        <span class="badge info-number <?php echo $norevenue>0 ? 'bell':'' ?>" style="margin: -1px;"><?php echo $norevenue ?></span>
    </a>
</li>
<?php } ?>
<?php  if($norevenue_notice>0){ ?>
<li class="bottom-links">
    <a href="<?php echo site_url('tasks/norevenue_notice') ?>" class="last_info">
        Công việc không tạo doanh thu
        <span class="badge info-number <?php echo $norevenue_notice>0 ? 'bell':'' ?>" style="margin: -1px;"><?php echo $norevenue_notice ?></span>
    </a>
</li>
<?php } ?>
</ul>
</li>
<?php } ?>
<?php if (count($authenticated_locations) > 1) { ?>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> <?php echo $authenticated_locations[$current_logged_in_location_id]; ?> <span class="drop-icon"><i class="ion ion-chevron-down"></i></span></a>
        <ul class="dropdown-menu animated fadeInUp wow locations-drop locations-drop neat_drop" data-wow-duration="1500ms" role="menu">
            <?php foreach ($authenticated_locations as $key => $value) { ?>
                <li><a class="set_employee_current_location_id" data-location-id="<?php echo $key; ?>" href="<?php echo site_url('home/set_employee_current_location_id/'.$key) ?>"><span class="badge" style="background-color:<?php echo $this->Location->get_info($key)->color; ?>">&nbsp;</span> <?php echo $value; ?> </a></li>
            <?php } ?>
        </ul>
    </li>

<?php } ?>
<?php if ($this->config->item('show_language_switcher')) { ?>
    <?php
    $languages = array('english'  => 'English',
        'indonesia'    => 'Indonesia',
        'spanish'   => 'Español',
        'french'    => 'Fançais',
        'italian'    => 'Italiano',
        'german'    => 'Deutsch',
        'dutch'    => 'Nederlands',
        'portugues'    => 'Portugues',
        'arabic' => 'العَرَبِيةُ‎‎',
        'khmer' => 'Khmer',
    );

    ?>
    <!-- redirect($_SERVER['HTTP_REFERER']);	 -->
    <li class="dropdown">
        <a tabindex = "-1" href="#" class="dropdown-toggle language-dropdown" data-toggle="dropdown" role="button" aria-expanded="false"><img class="flag_img" src="<?php echo base_url(); ?>assets/assets/images/flags/<?php echo $user_info->language ? $user_info->language : "english";  ?>.png" alt=""> <span class="hidden-sm"> <?php echo $user_info->language ? $languages[$user_info->language] : $languages["english"];  ?></span><span class="drop-icon"><i class="ion ion-chevron-down"></i></span></a>
        <ul class="dropdown-menu animated fadeInUp wow language-drop neat_drop" data-wow-duration="1500ms" role="menu">
            <?php foreach ($languages as $key => $value) {
                if($user_info->language!=$key){
                    ?>
                    <li><a tabindex = "-1" href="<?php echo site_url('employees/set_language/') ?>" data-language-id="<?php echo $key; ?>" class="set_employee_language"><img class="flag_img" src="<?php echo base_url(); ?>assets/assets/images/flags/<?php echo $key; ?>.png" alt="flags"><?php echo $value; ?></a></li>
                <?php } } ?>
            </ul>
        </li>
    <?php } ?>




    <li class="dropdown">
        <a tabindex = "-1" href="#" class="dropdown-toggle avatar_width" data-toggle="dropdown" role="button" aria-expanded="false"><span class="avatar-holder">

         <?php echo $user_info->image_id ? img(array('src' => site_url('app_files/view/'.$user_info->image_id))) : img(array('src' => base_url('assets/assets/images/avatar-default.jpg'))); ?></span>
         <span class="avatar_info hidden-sm"><?php echo $user_info->first_name." ".$user_info->last_name; ?></span></a>
         <ul class="dropdown-menu user-dropdown animated fadeInUp wow avatar_drop neat_drop" data-wow-duration="1500ms"  role="menu">
            <?php if ($this->Employee->has_module_permission('config', $user_info->person_id)) {?>

                <li><?php echo anchor("config",'<i class="ion-android-settings"></i><span class="text">'.lang("common_settings").'</span>', array('tabindex' => '-1')); ?></li>
            <?php } ?>
            <li>
                <a tabindex = "-1" id="switch_user" href="<?php echo site_url('login/switch_user/'.($this->uri->segment(1) == 'sales' ? '0' : '1'));  ?>" data-toggle="modal" data-target="#myModalDisableClose"><i class="ion-ios-toggle-outline"></i><span class="text"><?php echo lang('common_switch_user'); ?></span></a>
            </li>
            <li>
                <a tabindex = "-1" title="" href="<?php echo site_url('login/edit_profile')?>" data-toggle="modal" data-target="#myModal"><i class="ion-edit"></i><span class="text"><?php echo lang('common_edit_profile'); ?></span></a>
            </li>
            <?php
            if ($this->config->item('timeclock'))
            {
                ?>
                <li>
                    <?php
                    echo anchor("timeclocks",'<i class="ion-clock"></i>'.lang("employees_timeclock"), array('tabindex' => '-1'));
                    ?>
                </li>
                <?php
            }
            ?>
            <li>
                <?php
                if ($this->config->item('track_cash') && $this->Register->is_register_log_open()) {
                    $continue = $this->config->item('timeclock') ? 'timeclocks' : 'logout';
                    echo anchor("sales/closeregister?continue=$continue",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
                } else {

                    if ($this->config->item('timeclock') && $this->Employee->is_clocked_in())
                    {
                        echo anchor("timeclocks",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
                    }
                    else
                    {
                        echo anchor("home/logout",'<i class="ion-power"></i><span class="text">'.lang("common_logout").'</span>',array('class'=>'logout_button','tabindex' => '-1'));
                    }
                }
                ?>
            </li>
        </ul>
    </li>
				<!--  <li class="dropdown">
                    <a href="javascript:void(0)" class="page-quick-sidebar-toggler-item" id="config">
                        <i class="page-quick-sidebar-toggler-item ion ion-gear-b"></i>
                    </a>
                </li> -->										 
            </ul>
        </nav>
    </div>
    <!-- top-bar -->
    <div class="main-content">
