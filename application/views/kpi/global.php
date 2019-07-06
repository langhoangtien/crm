<?php $this->load->view("partial/header"); ?>
<?php $y = date("Y") ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<select class="form-control type select" name="type" id="">
					<option>Chọn loại</option>
					<option value="plan">Kế hoạch</option>
					<option value="result">Kết quả</option>
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<select class="form-control year select" name="year" id="">
					<option value>Chọn năm</option>
					<?php for ($i=-8;$i<3;$i++) {?>
					<option value="<?php echo $y+$i  ?>"><?php echo $y+$i  ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<select class="form-control kpi select" name="" id="">
					<option>Chọn loại</option>
				</select>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12 col-sm-12">
			<table class="table table-bordered" id="kpi">
				<thead>
					<tr class="info">
						<th class="" rowspan="2">Khu vực</th>
						<th class="title_1" colspan="5">KPI doanh thu HĐTV VCBS giao</th>
						<th class="title_2" colspan="5">KPI doanh thu VCB giao</th>
					</tr>
					<tr class="info">
						<th>Quý I</th>
						<th>Quý II</th>
						<th>Quý III</th>
						<th>Quý IV</th>
						<th class="total">Tổng</th>
						<th>Quý I</th>
						<th>Quý II</th>
						<th>Quý III</th>
						<th>Quý IV</th>
						<th class="total">Tổng</th>
					</tr>
				</thead>
				<tbody class="global_kpi">

				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="kpi_edit" class="modal fade search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
            <h4 class="modal-title"> Cập nhật</h4>
        </div>
      
      
        <div class="modal-body">
           <div class="form-group">
           	<input class="value form-control" type="text">
           	<input class="quater" type="hidden" value="">
           	<input type="hidden" class="location">
           	<input type="hidden" class="ty">
           </div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary save" data-dismiss="modal">Lưu</button></div>
    </div>
</div>

</div>
<button style="float: right" onclick="window.print()" class="btn btn-primary">In</button>
<script>

	$('.type').change(function(){
		let type = $(this).val();
		let html="";
		if(type=="plan")
		{
			 html='<option value="">Chọn loại</option><option value="revenue">KPI doanh thu</option><option value="profit">KPI lợi nhuận</option>';
			$('.kpi').html(html);
			$('.title_1').html("TH/KH Doanh thu HĐTV VCBS giao");
			$('.title_2').html("TH/KH Lợi nhuận HĐTV VCBS giao");
			$('.total').html("Tổng");

		}
		else if(type=="result")
		{
			html='<option>Chọn loại</option><option value="1">Doanh thu/lợi nhuận thực hiện</option><option value="2">TH/KH HĐTV VCBS giao</option><option value="3">TH/KH VCB giao</option>';
			$('.kpi').html(html);
			$('.title_1').html("KPI Doanh thu HĐTV VCBS giao");
			$('.title_2').html("KPI Doanh thu VCB giao");
			$('.total').html("Lũy kế");

		}
	});

	$('.kpi').change(function(){
		let kpi = $(this).val();
		switch(kpi){
			case "1":
			$('.title_1').html("Doanh thu thực hiện");
			$('.title_2').html("Lợi nhuận thực hiện");
			break;
			case "2":
			$('.title_1').html("TH/KH Doanh thu HĐTV VCBS giao");
			$('.title_2').html("TH/KH Lợi nhuận HĐTV VCBS giao");
			break;
			case "3":
			$('.title_1').html("TH/KH Doanh thu HĐTV VCB giao");
			$('.title_2').html("TH/KH Lợi nhuận HĐTV VCB giao");
			break;
			case "revenue":
			$('.title_1').html("KPI Doanh thu HĐTV VCBS giao");
			$('.title_2').html("KPI Doanh thu VCB giao");
			break;
			case "profit":
			$('.title_1').html("KPI Lợi nhuận HĐTV VCBS giao");
			$('.title_2').html("KPI Lợi nhuận VCB giao");
			break;
		}
	});
	$(document).on('change','.select',function(){
		load_list_kpi();
		
	})


	function load_list_kpi(){
		// alert('load');
		let type = $('.type').val();
		let kpi = $('.kpi').val();
		let year = $('.year').val();
		$.ajax({
			url:BASE_URL+"kpi/kpi_global",
			dataType:"json",
			type:"POST",
			data:{
				type:type,
				kpi:kpi,
				year:year
			},
			success:function(respon){
				load_kpi(respon);
				$('.rp').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin: '-99999999999999'});
				$('.vcb').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-99999999999999'});
				$('.td').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-99999999999999'});
			}
		});
	}

	function load_kpi(result){
		// console.log(result);
		let html="";

		if(result.tp == 'plan')
		{
		let dt=result.dt;
		$.each(dt,function(index,item){
			// console.log(item.location_data[0].vcb);
			// alert(item);
		item.vcbs1 = (typeof item.location_data[1] =="undefined")? 0 : item.location_data[1].vcbs;
		item.vcbs2 = (typeof item.location_data[2]=="undefined")? 0 : item.location_data[2].vcbs;
		item.vcbs3 = (typeof item.location_data[3]=="undefined")? 0 : item.location_data[3].vcbs;
		item.vcbs4 = (typeof item.location_data[4]=="undefined")? 0 : item.location_data[4].vcbs;
		item.vcb1 = (typeof item.location_data[1]=="undefined")? 0 : item.location_data[1].vcb;
		item.vcb2 = (typeof item.location_data[2]=="undefined")? 0 : item.location_data[2].vcb;
		item.vcb3 = (typeof item.location_data[3]=="undefined")? 0 : item.location_data[3].vcb;
		item.vcb4 = (typeof item.location_data[4]=="undefined")? 0 : item.location_data[4].vcb;


		html+='<tr><td class="inf">'+item.location_name
		+'</td> <td data-toggle="modal" data-target="#kpi_edit" ty="vcbs" class="rp" location="'+item.location_id+'" va="'+item.vcbs1+'" quater="1">'+item.vcbs1
		+'</td>	<td data-toggle="modal" data-target="#kpi_edit" ty="vcbs" class="rp" location="'+item.location_id+'" va="'+item.vcbs2+'" quater="2">'+item.vcbs2
		+'</td> <td data-toggle="modal" data-target="#kpi_edit" ty="vcbs" class="rp" location="'+item.location_id+'" va="'+item.vcbs3+'" quater="3">'+item.vcbs3
		+'</td>	<td data-toggle="modal" data-target="#kpi_edit" ty="vcbs" class="rp" location="'+item.location_id+'" va="'+item.vcbs4+'" quater="4">'+item.vcbs4
		+'</td>	<td class="vcb">'+item.location_data.total.vcbs
		+'</td>	<td data-toggle="modal" data-target="#kpi_edit" ty="vcb" class="rp" location="'+item.location_id+'" va="'+item.vcb1+'" quater="1">'+item.vcb1
		+'</td>	<td data-toggle="modal" data-target="#kpi_edit" ty="vcb" class="rp" location="'+item.location_id+'" va="'+item.vcb2+'" quater="2">'+item.vcb2
		+'</td>	<td data-toggle="modal" data-target="#kpi_edit" ty="vcb" class="rp" location="'+item.location_id+'" va="'+item.vcb3+'" quater="3">'+item.vcb3
		+'</td>	<td data-toggle="modal" data-target="#kpi_edit" ty="vcb" class="rp" location="'+item.location_id+'" va="'+item.vcb4+'" quater="4">'+item.vcb4
		+'</td>	<td class="vcb">'+item.location_data.total.vcb
		+'</td> </tr>';			
				});	

		}

		if(result.tp=='result')
		{

		let dt = result.dt;
		$.each(dt,function(index,item){
		html+='<tr><td class="inf">'+item.location_name
		+'</td> <td class="td">'+item.location_data[1]+'</td>'
		+'</td> <td class="td">'+item.location_data[2]+'</td>'
		+'</td> <td class="td">'+item.location_data[3]+'</td>'
		+'</td> <td class="td">'+item.location_data[4]+'</td>'
		+'</td> <td class="vcb">'+item.location_data[5]+'</td>'
		+'</td> <td class="td">'+item.location_data[6]+'</td>'
		+'</td> <td class="td">'+item.location_data[7]+'</td>'
		+'</td> <td class="td">'+item.location_data[8]+'</td>'
		+'</td> <td class="td">'+item.location_data[9]+'</td>'
		+'</td> <td class="vcb">'+item.location_data[10]+'</td>'
		+'</tr>';

			});
		}

		$('.global_kpi').html(html);
		}

	$('.rp').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999',vMin:'-9999999999999'});


	$(document).on("click",".rp",function(){

		 $('.location').val( $(this).attr('location'));
		 $('.quater').val($(this).attr('quater'));
		 $('.value').val($(this).attr('va'));
		 $('.ty').val($(this).attr('ty')) ;
		 // $('.value').val(vcbs);
		 $('.value').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999'});
				
	});

	$(document).on('click','.save',function(){
				$.ajax({
			url:BASE_URL+"kpi/update_data",
			dataType:"json",
			type:"POST",
			data:{
				location:$('.location').val(),
				quater:$('.quater').val(),
				value:$('.value').val(),
				ty:$('.ty').val(),
				year:$('.year').val(),
				type: $('.type').val(),
				kpi:$('.kpi').val(),
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
				}
				load_list_kpi();
			}
		});

	});	


</script>
<style>
	th{
		text-align: center;
	}
	.info th {
		font-weight: bold;
		text-align: center
	}
	.vcb{
		font-weight: bold !important;
	}
	.rp{
		color: #25699a !important;
		cursor: pointer;
	}

	.inf {

		text-align: center
	}
	.inr{
		text-align: left;
	}
	td{
		text-align: right;
	}
</style>
<?php $this->load->view("partial/footer"); ?>