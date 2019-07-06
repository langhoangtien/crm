	function formatDate(date) {
		arr_date = date.split('-');
		day = arr_date[0];
		month = arr_date[1];
		year = arr_date[2];
		return [year, month, day].join('-');
	}

