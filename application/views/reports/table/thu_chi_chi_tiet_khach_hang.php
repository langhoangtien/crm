<?php
    if(!empty($items)) {

        $stt = 0;
        foreach($items as $item) { 
            $stt++;
            $sale_prefix = $this->config->item('sale_prefix');
            $link_sale       = base_url() . 'sales/receipt/' . $item['sale_id'];
            $code            = $sale_prefix . ' ' . $item['id_don_hang'];
?>
            <tr>
                <td class="text-center"><?php echo $stt?></a></td>
                <td class="text-center"><?php echo $item['expense_date']; ?></td>
                <td class="text-center"><a href="<?php echo $link_sale; ?>" target="_blank"><?php echo $code; ?></a></td>
                <td class="text-center"><?php echo $item['expense_description']; ?></td>
                <td class="text-center"><?php echo $item['nhan_vien']; ?></td>
                <td class="text-center"><?php echo $item['nhan_vien_phe_duyet']; ?></td>
                <td class="text-center"><?php echo number_format($item['tien_thu'],2); ?></td>
                <td class="text-center"><?php echo number_format($item['tien_chi'],2); ?></td>
            </tr>

<?php } } else { ?>
        <tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>