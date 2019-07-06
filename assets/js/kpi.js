$('.select').change(function(){
		let select =  $(this).val();
		switch(select){
			case "revenue":
			$('.type').html('<option value>---Chọn loại---</option><option value="plan">Kế hoạch</option><option value="result">Kết quả</option>');
			$('.location_list').hide();
			break;
			case "total":
			$('.type').html('<option value>---Chọn loại---</option><option value="plan">Kế hoạch</option><option value="result">Kết quả</option>');
			$('.location_list').show();
			break;
			case "profit":
			$('.type').html('<option value>---Chọn loại---</option><option value="result">Kết quả</option>');
			$('#room').html('');
			$('.location_list').hide();
			break;
			default:
			$('.type').html('<option value>---Chọn loại---</option>');
			$('#room').html('');
			$('.location_list').hide();
		}
		 
	});


	$(document).on('change','.type',function(){
		let select = $('.select').val();
		let type = $(this).val();
		if(select=="revenue" && type=="plan")
		{
			$('#room').html('<thead class ="thead"><tr class="info"><th rowspan="2">Khu vực</th><th colspan="4">Doanh thu kế hoạch</th></tr><tr><th>Bảo lãnh, đại lý phát hành trái phiếu</th><th>Bảo lãnh, đại lý phát hành cổ phiếu</th><th>M&A</th><th>Tư vấn khác</th></tr></thead><tbody class="tbody"></tbody>');
		}
		if (select=="revenue" && type=="result")
		{
			$('#room').html('<thead class ="thead"><tr class="info"><th rowspan="2">Khu vực</th><th colspan="4">Doanh thu thực tế</th></tr><tr><th>Bảo lãnh, đại lý phát hành trái phiếu</th><th>Bảo lãnh, đại lý phát hành cổ phiếu</th><th>M&A</th><th>Tư vấn khác</th></tr></thead><tbody class="tbody"></tbody>');
		}

		if (select=="total" && type=="plan")
		{
		$('#room').html('<thead class="thead"> <tr class="info"><th>STT</th>  <th colspan="2">Chỉ tiêu</th> <th>Trọng số(%)</th> </tr>'
			+'</thead> <tbody class="tbody">'
			+'<tr>  <td class="inf" rowspan="2">1</td>  <td class="inr" rowspan="2">Tài chính</td> <td class="inr">Doanh thu</td>'
			+' <td class="total_plan" tpe="1" name="revenue" data-toggle="modal" data-target="#total_plan_edit"></td> </tr> <tr>  <td class="inr">Lợi nhuận</td>'
			+' <td class="total_plan" tpe="1" name="profit" data-toggle="modal" data-target="#total_plan_edit"></td> </tr> <tr> <td class="inf"  rowspan="2">2</td> <td class="inr" rowspan="2">Khách hàng</td> <td class="inr">Đánh giá của KH bên ngoài về chất lượng dịch vụ</td> '
			+' <td class="total_plan" tpe="1" name="customer_out" data-toggle="modal" data-target="#total_plan_edit"></td> </tr>  <tr>  <td class="inr">Đánh giá của KH nội bộ về chất lượng dịch vụ</td>'
			+'<td class="total_plan" tpe="1" name="customer_in" data-toggle="modal" data-target="#total_plan_edit"></td> </tr> <tr>   <td class="inf" >3</td> <td class="inr" colspan="2">Quy trình, nội quy, quy định nội bộ</td>'
			+'<td class="total_plan" tpe="1" name="process" data-toggle="modal" data-target="#total_plan_edit"></td>   </tr>  <tr>   <td class="inf" >4</td> <td class="inr" colspan="2">Đào tạo và phát triển</td>'
			+'<td class="total_plan" tpe="1" name="training" data-toggle="modal" data-target="#total_plan_edit"></td>   </tr>  <tr>  <td class="vcb inf">Tổng</td> '
			+'<td colspan="3" class="total" ></td></tr> </tbody>');
		}
		if (select=="total" && type=="result")
		{
	$('#room').html('<thead class="thead"> <tr class="info"><th>STT</th>  <th colspan="2">Chỉ tiêu</th> <th>Trọng số(%)</th><th>Điểm</th> </tr>'
			+'</thead> <tbody class="tbody">'
			+'<tr>  <td rowspan="2" class="inf" >1</td>  <td class="inr" rowspan="2">Tài chính</td> <td class="inr">Doanh thu</td>'
			+' <td class="t_revenue"></td> <td class="revenue_point"></td></tr> <tr>  <td class="inr">Lợi nhuận</td>'
			+' <td class="t_profit"></td> <td class="profit_point"></td></tr> <tr> <td rowspan="2"class="inf" >2</td> <td class="inr" rowspan="2">Khách hàng</td> <td class="inr">Đánh giá của KH bên ngoài về chất lượng dịch vụ</td> '
			+' <td class="t_customer_out"></td><td class="total_plan" tpe="2" name="customer_out" data-toggle="modal" data-target="#total_plan_edit"></td> </tr>  <tr>  <td class="inr">Đánh giá của KH nội bộ về chất lượng dịch vụ</td>'
			+'<td class="t_customer_in"></td><td class="total_plan" tpe="2" name="customer_in" data-toggle="modal" data-target="#total_plan_edit"></td> </tr> <tr>   <td class="inf" >3</td> <td class="inr" colspan="2">Quy trình, nội quy, quy định nội bộ</td>'
			+'<td class="t_process"></td><td class="total_plan" tpe="2" name="process" data-toggle="modal" data-target="#total_plan_edit"></td>   </tr>  <tr>   <td class="inf" >4</td> <td class="inr" colspan="2">Đào tạo và phát triển</td>'
			+'<td class="t_training"></td> <td class="total_plan" tpe="2" name="training" data-toggle="modal" data-target="#total_plan_edit"></td>  </tr>  <tr>  <td class="vcb inf">Tổng</td> '
			+'<td colspan="2" ></td><td class="total"></td><td class="t_point"></td></tr> </tbody>');
		}
		if (select=="profit")
		{
		$('#room').html('<thead class="thead"></thead> <tbody class="tbody"> </tbody>');
		}
		
	});

	$(document).on('change','.sl',function(){
		load_list_kpi_room();
	
	});

function load_list_kpi_room(){

	let data={};
	data['select'] = $('.select').val();
	data['tp'] =  $('.type').val();
	data['year'] = $('.year').val();
	data['quater'] =  $('.quater').val();
	data['location_id'] = $('.location_id_list').val();
	let action = data.select && data.tp && data.year;
	if(action){
		$.ajax({
			url:BASE_URL+'kpi/kpi_room',
			dataType:'json',
			type:'POST',
			data:data,
			success:function(response){
				load_room_kpi(response);
			}
		});
	}
		
}
function load_room_kpi(response){
	let html ="";
	let tp = response.tp;
	let dt = response.dt;
	switch(tp){
		case "plan_quater":
		{
			$.each(dt,function(index,item){

				let v1 = item.location_data[1]['value'];
				let v2 = item.location_data[2]['value'];
				let v3 = item.location_data[3]['value'];
				let v4 = item.location_data[4]['value'];

				if(item.location_id)
				{
				html +='<tr><td class="inf">'+item.location_name
				+'</td><td data-toggle="modal" group="1" location='+item.location_id+' data-target="#kpi_edit" va="'+v1+'" class="rp">'+v1
				+'</td><td data-toggle="modal" group="2" location='+item.location_id+' data-target="#kpi_edit" va="'+v2+'" class="rp">'+v2
				+'</td><td data-toggle="modal" group="3" location='+item.location_id+' data-target="#kpi_edit" va="'+v3+'" class="rp">'+v3
				+'</td><td data-toggle="modal" group="4" location='+item.location_id+' data-target="#kpi_edit" va="'+v4+'" class="rp">'+v4+'</td></tr>';
				}
				else{
				html +='<tr><td class="vcb inf">'+item.location_name
				+'</td><td class="vcb">'+v1
				+'</td><td class="vcb">'+v2
				+'</td><td class="vcb">'+v3
				+'</td><td class="vcb">'+v4+'</td></tr>';
				}
			
			});
			$('.tbody').html(html);
			$('.tbody tr td').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
			break;
		}

		case "plan_year":
		{
			$.each(dt,function(index,item){

				let v1 = item.location_data[1]['value'];
				let v2 = item.location_data[2]['value'];
				let v3 = item.location_data[3]['value'];
				let v4 = item.location_data[4]['value'];

				if(item.location_id)
				{
				html +='<tr><td class="inf">'+item.location_name
				+'</td><td>'+v1
				+'</td><td>'+v2
				+'</td><td>'+v3
				+'</td><td>'+v4+'</td></tr>';
				}
				else{
				html +='<tr><td class="vcb inf">'+item.location_name
				+'</td><td class="vcb">'+v1
				+'</td><td class="vcb">'+v2
				+'</td><td class="vcb">'+v3
				+'</td><td class="vcb">'+v4+'</td></tr>';
				}
			
			});
			$('.tbody').html(html);
			$('.tbody tr td').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
			break;
		}

		case "result_quater":
		case "result_year":
		{
			let ppr= response.ppr;
			let point = response.rate;
			let density = response.density;
			let tt = response.tt;
			$.each(dt,function(index,item){
				let v1 = item.location_data[1];
				let v2 = item.location_data[2];
				let v3 = item.location_data[3];
				let v4 = item.location_data[4];

				html +='<tr><td class="inf">'+item.location_name
				+'</td><td>'+v1
				+'</td><td>'+v2
				+'</td><td>'+v3
				+'</td><td>'+v4+'</td></tr>';

			});

			html += '<tr><td class="inf">TH/KH(%)</td><td>'+ppr[1]+'</td><td>'+ppr[2]+'</td><td>'+ppr[3]+'</td><td>'+ppr[4]+'</td></tr>';
			html += '<tr><td class="inf">Điểm</td><td>'+point[1]+'</td><td>'+point[2]+'</td><td>'+point[3]+'</td><td>'+point[4]+'</td></tr>';
			

			html += '<tr><td class="inf">Tỷ trọng(%)</td><td class="density" data-toggle="modal" van="'+density[1]+'"  group="1" data-target="#density_edit">'+density[1]+'</td><td class="density" data-toggle="modal" van="'+density[2]+'"  group="2" data-target="#density_edit">'+density[2]+'</td><td class="density" data-toggle="modal" van="'+density[3]+'"  group="3" data-target="#density_edit">'+density[3]+'</td><td class="density" data-toggle="modal" van="'+density[4]+'"  group="4" data-target="#density_edit">'+density[4]+'</td></tr>';
			html += '<tr><td class="inf vcb">Điểm doanh thu thực hiện</td><td class="vcb" colspan="4">'+tt+'</td></tr>';
			
			
			$('.tbody').html(html);
			$('.tbody tr td').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
			$('.density').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
			break;
		}

		case "profit":
		{
			let location =dt.location;
			let revenue = dt.revenue;
			let expense = dt.expense;
			let revenue_receving = dt.revenue_receving;
			let supplier_expense = dt.supplier_expense;
			let general_expense = dt.general_expense;
			let profit = dt.profit;
			let plan_profit =dt.plan_profit;
			let ppr = dt.ppr;
			let point = dt.point;
			let salary = dt.salary;
			let net_profit = dt.net_profit;
		
			let html1x="";
			let html2x="";
			let html3x="";
			let html4x="";
			let html5x="";
			let html6x="";
			let html7x="";
			let html8x="";
			let html9x="";
			let html10x="";
			let html11x="";
			let html12x="";
			$.each(revenue,function(index,item){
				html1x += '<th class="vcb">'+location[index]+'</th>';
				html2x += '<td>'+revenue[index]+'</td>';
				html3x += '<td>'+expense[index]+'</td>';
				html4x += '<td>'+revenue_receving[index]+'</td>';
				html5x += '<td>'+supplier_expense[index]+'</td>';
				html6x += '<td>'+general_expense[index]+'</td>';
				html7x += '<td>'+profit[index]+'</td>';
				html8x += '<td>'+plan_profit[index]+'</td>';
				html9x += '<td class="ppr">'+ppr[index]+'</td>';
				html10x += '<td>'+point[index]+'</td>';
				html11x += '<td>'+salary[index]+'</td>';
				html12x += '<td>'+net_profit[index]+'</td>';

			});

			let html1 = '<tr><td class="inf"></td>'+html1x+'</tr>';
			let html2 = '<tr><td class="inf">Tổng doanh thu</td>'+html2x+'</tr>';
			let html3 = '<tr><td class="inf">Doanh thu chia phòng ban khác</td>'+html3x+'</tr>';
			let html4 = '<tr><td class="inf">Doanh thu thực hiện</td>'+html4x+'</tr>';
			let html5 = '<tr><td class="inf">Chi phí bên thứ ba</td>'+html5x+'</tr>';
			let html6 = '<tr><td class="inf">Chi phí chung</td>'+html6x+'</tr>';
			let html7 = '<tr><td class="inf">Lợi nhuận thực hiện</td>'+html7x+'</tr>';
			let html8 = '<tr><td class="inf">Lợi nhuận kế hoạch</td>'+html8x+'</tr>';
			let html9 = '<tr><td class="inf ppr">TH/KH(%)</td>'+html9x+'</tr>';
			let html10 = '<tr><td class="inf">Điểm lợi nhuận thực hiên</td>'+html10x+'</tr>';
			let html11 = '<tr><td class="inf">Chi phí lương cơ bản</td>'+html11x+'</tr>';
			let html12 = '<tr><td class="inf">Lợi nhuận ròng</td>'+html12x+'</tr>';

			let html = html2 +html3 + html4 +html5 +html6 + html7 +html8 +html9 + html10 +html11 +html12;
			$('.thead').html(html1);
			$('.tbody').html(html);
			$('.tbody tr td').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
			$('.ppr').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
			break;

		}
		case "total_plan":
		{
			$('td[name=revenue]').html(dt.revenue);
			$('td[name=profit]').html(dt.profit);
			$('td[name=customer_in]').html(dt.customer_in);
			$('td[name=customer_out]').html(dt.customer_out);
			$('td[name=process]').html(dt.process);
			$('td[name=training]').html(dt.training);

			$('.total').html(dt.total);
			$('.total_plan').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
		}
		case "total_result":
		{
			point = response.point;
			$('.t_revenue').html(dt.revenue);
			$('.t_profit').html(dt.profit);
			$('.t_customer_in').html(dt.customer_in);
			$('.t_customer_out').html(dt.customer_out);
			$('.t_process').html(dt.process);
			$('.t_training').html(dt.training);
			$('.revenue_point').html(response.revenue_point);
			$('.profit_point').html(response.profit_point);
			$('td[name=customer_in]').html(point.customer_in);
			$('td[name=customer_out]').html(point.customer_out);
			$('td[name=process]').html(point.process);
			$('td[name=training]').html(point.training);
			$('.t_point').html(response.t_point);
			$('.total').html(dt.total);
			$('.total_plan').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});
		}

	}

}

$(document).on("click",".rp",function(){
	$('.location').val( $(this).attr('location'));
	$('.value').val($(this).attr('va'));
	$('.group').val($(this).attr('group')) ;
	$('.value').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ',', vMax: '999999999999999'});
		});


$(document).on("click",".density",function(){
	$('.van').val($(this).attr('van'));
	$('.group_den').val($(this).attr('group'));
	$('.van').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ',', vMax: '999999999999999'});
		});

$(document).on("click",".total_plan",function(){
	$('.total_plan_value').val($(this).html());
	$('.total_plan_name').val($(this).attr('name'));
	$('.total_plan_tpe').val($(this).attr('tpe'));
	$('.total_plan_value').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ',', vMax: '999999999999999'});
		});

$(document).on('click','.save',function(){
				$.ajax({
			url:BASE_URL+"kpi/update_room_data",
			dataType:"json",
			type:"POST",
			data:{
				location:$('.location').val(),
				quater:$('.quater').val(),
				value:$('.value').val(),
				year:$('.year').val(),
				group: $('.group').val(),
			},

			success:function(respon){
				console.log(respon);
				if(respon.flag=='warning'){

					toastr.warning(respon.notice,"Cảnh báo");
				}
				if(respon.flag=='error'){

					toastr.error(respon.notice,"Lỗi");
				}
				if(respon.flag=='success'){

					toastr.success(respon.notice,"Thông báo");
					load_list_kpi_room();
				}
				
			}
		});

	});	



$(document).on('click','.total_plan_save',function(){
				$.ajax({
			url:BASE_URL+"kpi/update_total",
			dataType:"json",
			type:"POST",
			data:{
				quater:$('.quater').val(),
				type:$('.total_plan_tpe').val(),
				year:$('.year').val(),
				value:$('.total_plan_value').val(),
				name:$('.total_plan_name').val(),
				location_id:$('.location_id_list').val(),
			},

			success:function(respon){
				console.log(respon);
				if(respon.flag=='warning'){

					toastr.warning(respon.notice,"Cảnh báo");
				}
				if(respon.flag=='error'){

					toastr.error(respon.notice,"Lỗi");
				}
				if(respon.flag=='success'){

					toastr.success(respon.notice,"Thông báo");
					load_list_kpi_room();
				}
				
			}
		});

	});	

$(document).on('click','.density-save',function(){
				$.ajax({
			url:BASE_URL+"kpi/update_density_data",
			dataType:"json",
			type:"POST",
			data:{
				quater:$('.quater').val(),
				value:$('.van').val(),
				year:$('.year').val(),
				group: $('.group_den').val(),
			},

			success:function(respon){
				console.log(respon);
				if(respon.flag=='warning'){

					toastr.warning(respon.notice,"Cảnh báo");
				}
				if(respon.flag=='error'){

					toastr.error(respon.notice,"Lỗi");
				}
				if(respon.flag=='success'){

					toastr.success(respon.notice,"Thông báo");
					load_list_kpi_room();
				}
				
			}
		});

	});	