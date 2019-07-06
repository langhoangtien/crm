<script>
	Highcharts.chart('employees-graph-2', {
		credits: {
      enabled: false
  		},
		chart: {
			type: 'line'
		},
		title: {
			text: 'Thống kê Số lượng dự án'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			categories:[<?php foreach ($result['time'] as  $value) {
				echo "'".$value."',";
			}  ?>]
		},
		yAxis: {
			title: {
				text: 'Số lượng dự án'
			}
		},
		plotOptions: {
			line: {
				dataLabels: {
					enabled: false
				},
				enableMouseTracking: false
			}
		},
		series: 
		[
		{
			name: '<?php echo $info['first_name']; ?>',
			data: 
			[<?php foreach ($result['task'] as $value) {
				echo ($value). ',';
			} ?>
			]
		},
		<?php foreach($colleague as $value) { ?>
		{
			name: '<?php echo $value['name'] ?>',
			data: 
			[
			<?php foreach ($value['task'] as $value2) {
				echo ($value2). ',';
			} ?>
			],
			<?php if($visible){echo "visible: false";} ?>
		},
<?php } ?>
		]
	});
</script>
