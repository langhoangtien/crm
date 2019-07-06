<style type="text/css">
.customer-recent-sales .modal-body table tr th {
	border: 1px solid #d7dce5;
}
</style>
<!-- Modal -->
<div class="modal fade" id="testFormulaModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog customer-recent-sales" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><?php echo lang('Chạy thử công thức'); ?></h4>
			</div>
			<div class="modal-body">
            <form id="testFormulaForm">
                <ul class ="list-group">
                    <?php foreach($listVariable as $variable):?>
                    <li class="list-group-item">
                        <?php echo $variable;?>
                        <input class ="form-control" type = "text" name = "input_test[<?php echo $variable?>]" >
                    </li>
                    <?php endforeach;?>
                    <li class="list-group-item">
                        <textarea class ="form-control" name ="strCodeTestFormula"><?php echo $formula;?></textarea>
                    </li>
                    <li class="list-group-item">
                        <button class ="btn" id = "run"> Run </button>
                    </li>
                    <li class="list-group-item">
                        <span  id = "test_result"></span>
                    </li>
                </ul>
            </form>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
    $("#testFormulaForm").submit(function(e) {
        $.ajax({
               type: "POST",
               url: "attribute_sets/testFormula",
               data: $(this).serialize(), // serializes the form's elements.
               success: function(data)
               {
                    $("#test_result").html(data);
               }
             });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });
});

</script>