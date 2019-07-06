/*
* Core Ajax for application
*/
var coreAjax = {
	xhrFilter : [],
	
	abortAllFilterAjax: function()
	{
		coreAjax.xhrFilter.forEach(function(jqXHR, i){
			jqXHR.abort();
		});
		
		coreAjax.xhrFilter.forEach(function(jqXHR, i){
			var index = $.inArray(jqXHR, coreAjax.xhrFilter);
			if (index > -1) {
				coreAjax.xhrFilter.splice(index, 1);
			}
		});
	},
	
	callIgnoreFilterBefore : function (url, data, callBackOK, callBackKO, type, isAsync, ignoreMask) {
		coreAjax.abortAllFilterAjax();
		type = typeof type !== 'undefined' ? type: 'POST';
		ignoreMask = typeof ignoreMask !== 'undefined' ? ignoreMask: false;
		$.ajax({
			async : isAsync ? false : true,
			type: type,
			url: url,
			dataType: "json",
			data: data,
			beforeSend: function( jqXHR )
			{
				coreAjax.xhrFilter.push(jqXHR);
				
				if( !ignoreMask )
				{
					ajaxEventHandle.showMask();
				} else {
					ajaxEventHandle.hideMask();
				}
					
			},
			success: function(response){
				
				/* Hide mask layer if not ignore mask */
				if( !ignoreMask )
				{
					ajaxEventHandle.hideMask();
				}
				
				if( callBackOK ){
					callBackOK(response);
				}
			},
			error: function(xhr, status, error) {
				
				/* Hide mask layer if not ignore mask */
				if( !ignoreMask )
				{
					ajaxEventHandle.hideMask();
				}
				
				if( xhr.status )
				{
					var messageError = xhr.statusText;
					if( typeof xhr.responseJSON != 'undefined' && typeof xhr.responseJSON.message != 'undefined' )
					{
						messageError = xhr.responseJSON.message;
					}
					console.log('--- ERROR: ' + messageError);
				}
				
				if( callBackKO ) {
					callBackKO();
				}
			},
			complete: function(jqXHR) {
				var index = $.inArray(jqXHR, coreAjax.xhrFilter);
				if (index > -1) {
					coreAjax.xhrFilter.splice(index, 1);
				}
			}
		});
	},
	
	call : function (url, data, callBackOK, callBackKO, type, isAsync) {
		type = typeof type !== 'undefined' ? type: 'POST';
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "json",
				data: data,
				beforeSend: function() {
					ajaxEventHandle.showMask();
				},
				success: function(response) {
					ajaxEventHandle.hideMask();

					if( callBackOK ){
						callBackOK(response);
					}
				},
				error: function(xhr, status, error) {
					ajaxEventHandle.hideMask();

					if( callBackKO ) {
						callBackKO();
					}
				}
			});
	},

	callWithoutMask : function (url, data, callBackOK, callBackKO, type, isAsync) {
		type = typeof type !== 'undefined' ? type: 'POST';
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "json",
				data: data,
				success: function(response) {
					if( callBackOK ){
						callBackOK(response);
					}
				},
				error: function(xhr, status, error) {
					if( callBackKO ) {
						callBackKO();
					}
				}
			});
	},

}

var ajaxEventHandle = {
		maskDiv		: null,
		
		init : function () {
			// $.ajaxSetup({
			// 	headers: {
			// 		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			// 	}
			// });

			
			// $( document ).ajaxStart( ajaxEventHandle.ajaxStart );
			// $( document ).ajaxStop( ajaxEventHandle.ajaxStop );
			

			$('<div class="mask"><div class="ajax_spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>').appendTo(document.body).hide();
			ajaxEventHandle.maskDiv = $('div.mask');
		},
		
		ajaxStart : function() {
			// ajaxEventHandle.showMask();
		},
		
		ajaxStop : function() {
			// ajaxEventHandle.hideMask();
		},
		
		showMask : function() {
			ajaxEventHandle.maskDiv.show();
		},
		
		hideMask : function() {
			ajaxEventHandle.maskDiv.hide();
		}
}

ajaxEventHandle.init();