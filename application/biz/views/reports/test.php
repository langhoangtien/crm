<table class="table table-condensed">
        <thead>
        <tr>
            <th>Ma san pham</th>
            <th>ma nhan vien</th>
            <th>thoi gian</th>
            <th>ghi chu</th>
            <th>so luong</th>
            <th>Ton dau</th>
        </tr>
        </thead>
        <tbody>
        
            <?php foreach ($data as $key => $value) { ?>
            <tr>
                <td><?php echo $value['trans_id'] ?></td>
                <td><?php echo $value['trans_user']; ?></td>
                <td><?php echo $value['trans_date']; ?></td>
                <td><?php echo $value['trans_comment']; ?></td>
                <td><?php echo $value['trans_inventory']; ?></td>
                <td><?php echo $value['bat_dau']; ?></td>
            </tr>
                          
            <?php } ?>
           

</tbody>
</table>


