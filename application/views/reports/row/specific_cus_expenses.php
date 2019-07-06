<?php
    if(!empty($items)) {
        foreach($items as $val) {
            $id = $val['id'];
            $expense_amount = to_currency($val['expense_amount']);
            $expense_tax    = to_currency_without_unit($val['expense_tax']);
            if($val['expense_type'] == 1)
                $expense_type = 'Chi';
            else
                $expense_type = 'Thu';

            if(!empty($val['expense_description']))
                $expense_description = nl2br($val['expense_description']);
            else
                $expense_description = $val['category_name'];

            if (isset($val['expenses_date_format'])) {
                $expense_date = $val['expenses_date_format'];
            }
            else {
                $expense_date = '';
            }               
?>
            <tr>
                <td class="center"><?php echo $expense_date; ?></td>
                <td class="center"><?php echo $expense_type; ?></td>
                <td class="center"><?php echo $expense_amount; ?></td>
                <td class="center"><?php echo $expense_tax; ?></td>
                <td><?php echo $expense_description; ?></td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="5"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>