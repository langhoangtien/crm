
// Thêm các phương thức



// Validate maxDate
// Ex: maxDate:'20-01-1992'

$.validator.addMethod("maxDate", function(value, element,params) {

	if(!value) 
	return true;
	
	if(params=="")
	{
		var now = new Date();
		var nd = now.getDate();
		var nm = now.getMonth() + 1;
		var ny = now.getFullYear();
		now = nm+'/'+nd+'/'+ny;
		var today = now;

	}
	else
	{
	var today = params.split("-");
    var dd = today[0];
    var mm = parseInt(today[1]);
    var yyyy = today[2];
    today = mm + '/' + dd + '/' + yyyy; 
	}
   
   
    var iDate = value.split("-");
    var id = iDate[0];
    var im = parseInt(iDate[1]);
    var iy = iDate[2];
    iDate = im + '/' + id + '/' + iy;  
    var curDate = new Date(today);
    var inputDate = new Date(iDate);
    console.log('Ngay hien tai:'+curDate); 
    console.log('Ngay nhap vao:'+inputDate); 
    if (inputDate < curDate)
		return true;
	return false;
}, "Ngày nhập không thể lớn hơn ngày hiện tại");



// Validate minDate
// Ex: minDate:'20-01-1992'

$.validator.addMethod("minDate", function(value, element,params) {

	if(!value) 
	return true;
	var today = params.split("-");
    var dd = today[0];
    var mm = parseInt(today[1]);
    var yyyy = today[2];
   
    today = mm + '/' + dd + '/' + yyyy;   
   
    var iDate = value.split("-");
    var id = iDate[0];
    var im = parseInt(iDate[1]);
    var iy = iDate[2];
    iDate = im + '/' + id + '/' + iy;  
    var curDate = new Date(today);
    var inputDate = new Date(iDate);
    console.log('Ngay hien tai:'+curDate); 
    console.log('Ngay nhap vao:'+inputDate); 
    if (inputDate > curDate)
		return true;
	return false;
}, "Ngày nhập không thể nhỏ hơn ngày hiện tại");

// Validate website
// Ex: website:true
jQuery.validator.addMethod("website", function(value, element) {
 
  return this.optional( element ) || /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test( value );
}, 'Nhập không đúng định dạng website');



// Validate name
// Ex: name:true
jQuery.validator.addMethod("ten", function(value, element) {
 
  return this.optional( element ) || /^([a-zA-Z]+(([a-zA-Z ])?[a-zA-Z]*)*){4,255}$/.test( value );
}, 'Nhập không đúng định dạng tên');
