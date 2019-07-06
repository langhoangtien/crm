<div class="panel-heading">
    <h3 class="panel-title">
        <span class="title active" data-tab="contract_sales">Yêu cầu sản phẩm</span>
        <span class="title" data-tab="contract_delivery">Yêu cầu giao hàng</span>
        <span class="title" data-tab="contract_payment">Yêu cầu thanh toán</span>
        <span class="title" data-tab="contract_other">Yêu cầu khác</span>
    </h3>
</div>
<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_sales">
    <div class="clearfix top-control">
        <div class="col-xs-12 col-md-6 pull-left">
            <button class="btn btn-primary btn-lg submitf">Tải lại đơn hàng</button>
        </div>
    </div>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_sales" data-url="<?php echo base_url() . 'customers/quotes_constract_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
        <thead>
        <tr>
            <th>Tên</th>
            <th style="width: 10%;">Giá</th>
            <th style="width: 15%;">Số lượng</th>
            <th style="width: 10%;">Đơn vị tính</th>
            <th style="width: 10%;">Chiết khấu</th>
            <th style="width: 10%;">Thuế</th>
            <th style="width: 10%;">Thành tiền</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_delivery" style="display: none;">
    <div class="clearfix top-control">
        <div class="col-xs-12 col-md-6 pull-left">
            <button class="btn btn-primary btn-lg submitf">Thêm mới</button>
        </div>
        <div class="col-xs-6 pull-right">
            <button class="btn btn-red btn-lg btn-right">Xóa</button>
        </div>
    </div>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_payment" data-url="<?php echo base_url() . 'customers/quotes_constract_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
        <thead>
        <tr>
            <th class="leftmost" style="width: 20px;">
                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
            </th>
            <th style="width: 7%;">ID</th>
            <th>Giai đoạn</th>
            <th style="width: 15%;">Thời gian giao hàng</th>
            <th style="width: 15%;">Đơn vị</th>
            <th style="width: 20%;">Địa điểm</th>
            <th style="width: 200px;">&nbsp</th>
        </tr>
        </thead>
        <tbody>
        <tr style="cursor: pointer;">
            <td class="cb"><input type="checkbox" value="49" class="file_checkbox"><label><span></span></label></td>
            <td class="cb center">1</td>
            <td><a href="#">Giai đoạn 1</td>
            <td class="cb center">19/03/1990 00:00</td>
            <td class="cb">Công ty LifeTek</td>
            <td class="cb">Công ty LifeTek</td>
            <td class="cb"><a href="javascript:;" onclick="note(211);">Sản phẩm</a> | <a href="javascript:;" onclick="note(211);">Sửa</a></td>
        </tr>
        </tbody>
    </table>
</div>

<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_payment" style="display: none;">
    <div class="clearfix top-control">
        <div class="col-xs-12 col-md-6 pull-left">
            <button class="btn btn-primary btn-lg submitf">Thêm mới</button>
        </div>
        <div class="col-xs-6 pull-right">
            <button class="btn btn-red btn-lg btn-right">Xóa</button>
        </div>
    </div>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_payment" data-url="<?php echo base_url() . 'customers/quotes_constract_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
        <thead>
        <tr>
            <th class="leftmost" style="width: 20px;">
                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
            </th>
            <th style="width: 7%;">ID</th>
            <th>Giai đoạn</th>
            <th style="width: 15%;">Thời gian giao hàng</th>
            <th style="width: 15%;">Số tiền</th>
            <th style="width: 10%;">VAT</th>
        </tr>
        </thead>
        <tbody>
        <tr style="cursor: pointer;">
            <td class="cb"><input type="checkbox" value="49" class="file_checkbox"><label><span></span></label></td>
            <td class="cb center">1</td>
            <td><a href="#">Giai đoạn 1</td>
            <td class="cb center">19/03/1990 00:00</td>
            <td class="cb">2.000.000 VND</td>
            <td class="cb"><a href="javascript:;">Chưa xuất</a></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_other" style="display: none;">
    <div class="clearfix hang">
        <label class="col-md-3 col-lg-2 control-label">File Upload</label>
        <div class="col-md-9 col-lg-10">
            <input type="file" name="file_upload" class="file_upload" id="file_upload" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
            <div class="bootstrap-filestyle input-group"><input type="text" name="file_display" id="file_display" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image_id" class="btn btn-file-upload "><span class="glyphicon glyphicon-folder-open"></span> <span class="buttonText" id="choose_file">Choose file</span></label></span></div>
            <span for="file_upload" class="text-danger errors"></span>
        </div>
    </div>
    <div class="clearfix hang">
        <label class="col-md-3 col-lg-2 control-label">Tên file</label>
        <div class="col-md-9 col-lg-10">
            <input type="text" name="file_name" id="file_name" value="" class="form-control">
            <span for="file_name" class="text-danger errors"></span>
        </div>
    </div>
    <div class="clearfix hang">
        <label for="family_info" class="col-sm-3 col-md-3 col-lg-2 control-label ">Yêu cầu khác :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <textarea name="family_info" cols="17" rows="3" id="family_info" class="form-control text-area"></textarea>
        </div>
    </div>
</div>
