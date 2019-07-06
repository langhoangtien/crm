<?php
    $action = $this->uri->segment(2, 'index');
?>

<nav class="quick-nav">
            <a class="quick-nav-trigger" href="#0">
                <span aria-hidden="true"></span>
            </a>
            <ul>
        <?php
        if($action != 'index') {
        ?>
  
            <li>
                <a href="<?php echo base_url(); ?>customers" class="import ">
                <span>Quản lý khách hàng</span>
                <i class="fa fa-history" aria-hidden="true"></i>
                </a>
            </li>
        <?php
            }
        ?>

        <?php
        if($action != 'type_list') {
        ?>       
             <li>
                <a href="<?php echo base_url(); ?>customers/categories" class="import ">
                <span>Quản lý danh mục</span>
                <i class="fa fa-sitemap" aria-hidden="true"></i>
                </a>
            </li>
        <?php
        }
        ?>
       
 
             <li>
                <a href="<?php echo base_url(); ?>customers/manage_sms" class="import ">
                <span>Quản lý SMS Brandname</span>
               <i class="fa fa-commenting-o" aria-hidden="true"></i>
                </a>
            </li>
    
             <li>
                <a href="<?php echo base_url(); ?>customers/manage_group_send_SMS_email" class="import ">
                <span>Quản lý nhóm SMS/Mail</span>
                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                </a>
            </li>
             <li>
                <a href="<?php echo base_url(); ?>contracts/index/customer" class="import ">
                <span>Quản lý hợp đồng</span>
                <i class="fa fa-linode" aria-hidden="true"></i>
                </a>
            </li>
             <li>
                <a href="<?php echo base_url(); ?>customers/manage_mail" class="import ">
                <span>Quản lý E-Mail</span>
                <i class="fa fa-ravelry" aria-hidden="true"></i>
                </a>
            </li>
             <li>
                <a href="<?php echo base_url(); ?>customers/quotes_contract" class="import ">
                <span>Quản lý mẫu văn bản</span>
                <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                </a>
            </li>



            <li>
                <a href="<?php echo base_url(); ?>customers/excel_import" class="import ">
                <span>Khởi tạo từ tệp Excel</span>
                <i class="fa fa-table" aria-hidden="true"></i>
                </a>
            </li>
        
            <li>
                <a href="<?php echo base_url(); ?>customers/excel_export" class="import ">
                <span>Xuất tệp excel</span>
                <i class="fa fa-download" aria-hidden="true"></i>
                </a>
            </li>
        
            <li>
                <a href="<?php echo base_url(); ?>customers/cleanup" class="">
                <span>Xóa khách hàng cũ</span>
               <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>
            </li>
            
        </ul>
    <span aria-hidden="true" class="quick-nav-bg"></span>
</nav>
<div class="quick-nav-overlay"></div>
