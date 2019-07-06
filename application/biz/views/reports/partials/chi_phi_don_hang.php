        <?php if(count($chi_phi_sp_list) >= 1) { ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="width: 20%;">Diễn giải</th>
                <th style="width: 7%;">Loại</th>
                <th style="width: 15%;">Số tiền</th>
                <th style="width: 15%;">Thuế</th>
            </tr>
            </thead>
            <tbody>
        <?php
        foreach($chi_phi_sp_list as $val) {
            $description = $val['expense_description'];
            if(empty($description))
                $description = $val['category_name'];

            if($val['expense_type'] == 1)
                $expense_type = 'Chi';
            elseif($val['expense_type'] == -1)
                $expense_type = 'Thu';

            $expense_amount = to_currency($val['expense_amount']);
            $expense_tax    = to_currency($val['expense_tax']);

        ?>
            <tr>
                <td class="center"><?php echo $description; ?></td>
                <td class="center"><?php echo $expense_type;?></td>
                <td class="right"><?php echo $expense_amount; ?></td>
                <td class="right"><?php echo $expense_tax; ?></td>
            </tr>
        <?php
        }
        ?>
                <tr>
                    <td class="right" colspan="3">Tổng chi phí</td>
                    <td class="right"><?php echo to_currency($chi_phi_sp);?></td>
                </tr>
            </tbody>
        </table>
        <?php } ?>
