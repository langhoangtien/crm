<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<div class="manage_buttons">
    <div class="manage-row-options hidden" data-table="expenses">
        <div class="email_buttons text-center">
            <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
            <a href="javascript:;" data-table="expenses" data-url="<?php echo base_url() . 'expenses/delete_item'; ?>" class="btn btn-red btn-lg"><?php echo lang('common_clear_selection'); ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="search no-left-border">
                <input type="text" class="form-control data-n9-s" name ='s_keywords' id='search' data-table="expenses" value="" placeholder="Tìm kiếm <?php echo $category['name']; ?>"/>
                <input type="hidden" name="s_category_id" class="data-n9-s" data-table="expenses" value="<?php echo $this->config->item('shift_category_id'); ?>"/>
            </div>
            <div class="clear-block <?php echo (!isset($search)||$search=='') ? 'hidden' : ''  ?>">
                <a class="clear" href="<?php echo site_url($controller_name.'/clear_state_mail'); ?>">
                    <i class="ion ion-close-circled"></i>
                </a>
            </div>
        </div>

        <div class="col-md-7">
            <div class="buttons-list">
                <div class="pull-right-btn">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row manage-table">
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="item-tabs">
                        <a href="<?php echo base_url() . 'expenses';?>"><?php echo lang('common_list_of').' '.lang('module_expenses'); ?></a>
                    </span>
                    <span title="total customers" class="badge bg-primary tip-left"><?php echo $total_rows; ?></span>

                    <span class="selected item-tabs">
                        <?php echo $category['name']; ?>
                    </span>
                    <span title="total customers" id="count_expenses" class="badge bg-primary tip-left">0</span>
                    <i class="fa fa-spinner fa-spin loading" id="expenses_loading" style="display: none;"></i>
                </h3>
            </div>
            <div class="panel-body nopadding table_holder table-responsive" >
                <table class="tablesorter table  table-hover data-n9-table" data-table="expenses" data-url="<?php echo base_url() . 'expenses/shift_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                    <thead>
                    <tr>
                        <th class="leftmost" style="width: 20px;">
                            <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                        </th>
                        <th data-field="expense_date" style="width: 20%;">Thời gian</th>
                        <th data-field="expense_amount" style="width: 20%;">Số tiền</th>
                        <th style="width: 30%;">Diễn giải</th>
                        <th data-field="employee_id" style="width: 20%;">Người nhận</th>

                        <th style="width: 150px;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
        <div class="text-center data-n9-pagination" data-table="expenses"></div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        load_list('expenses');

        // search
        var typingTimer;
        $('body').on('keyup','#search',function(){
            clearTimeout(typingTimer);
            typingTimer = setTimeout(startSearch, 500);
        });

        $('body').on('keydown','#search',function(){
            clearTimeout(typingTimer);
        });

        function startSearch () {
            load_list('expenses', 1);
        }
    });
</script>
<style>
    .data-n9-table th {
        text-align: center;
    }

    .data-n9-table th[data-field] {
        cursor: pointer;
    }

    .data-n9-table td.center {
        text-align: center;
    }

    .panel-piluku > .panel-heading h3 {
        position: relative;
    }

    .loading {
        position: absolute;
        bottom: -91px;
        left: -9px;
    }
</style>
</div>
<?php
if(!empty($_SESSION['notice'])) {
    $notice = $_SESSION['notice'];
    unset($_SESSION['notice']);
    ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            toastr.success('<?php echo $notice; ?>', 'Thông báo');
        });
    </script>
<?php
}
?>
