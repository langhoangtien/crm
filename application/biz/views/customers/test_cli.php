<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<input id = "test">
<input type = "submit" id = "submit">
<div id= "return_value"></div>
<script>
$('#submit').click(function(){
	$.ajax({
		url: 'test_cli',
		type:'POST',
		data: {cmd:$('#test').val()},
		success: function(data)
		{
			$('#return_value').html(data);
		},
		
	})
	
});
</script>