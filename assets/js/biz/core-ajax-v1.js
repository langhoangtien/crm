/*
* Core Ajax for application
*/
var coreAjax = {
		
	xhrAutocomplete : [],
		
	xhrFilter : [],

	xhrLoadMore : [],
        
        mobileDebug: false,
	
	abortAllLoadMoreAjax: function()
	{
		coreAjax.xhrLoadMore.forEach(function(jqXHR, i){
			jqXHR.abort();
		});
		
		coreAjax.xhrLoadMore.forEach(function(jqXHR, i){
			var index = $.inArray(jqXHR, coreAjax.xhrLoadMore);
			if (index > -1) {
				coreAjax.xhrLoadMore.splice(index, 1);
			}
		});
	},

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
	
	abortAllAutocompleteAjax: function()
	{
		coreAjax.xhrAutocomplete.forEach(function(jqXHR, i){
			jqXHR.abort();
		});
		
		coreAjax.xhrAutocomplete.forEach(function(jqXHR, i){
			var index = $.inArray(jqXHR, coreAjax.xhrAutocomplete);
			if (index > -1) {
				coreAjax.xhrAutocomplete.splice(index, 1);
			}
		});
	},
	
	callIgnoreLoadMoreBefore : function (url, data, callBackOK, callBackKO, type, isAsync, ignoreMask) {
		
		if( coreAjax.xhrFilter.length )
		{
			return false;
		}
		
		if( coreAjax.xhrLoadMore.length )
		{
			return false;
		}
		
		type = typeof type !== 'undefined' ? type: 'POST';
		ignoreMask = typeof ignoreMask !== 'undefined' ? ignoreMask: false;
                if(coreAjax.mobileDebug){
                    alert('call ignore load more:'+ url);
                }
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "json",
				data: data,
				beforeSend: function( jqXHR )
				{
					coreAjax.xhrLoadMore.push(jqXHR);
					
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
						coreDialog.errorDialog(messageError);
					}
					
					if( callBackKO ) {
						callBackKO();
					}
				},
				complete: function(jqXHR) {
					var index = $.inArray(jqXHR, coreAjax.xhrLoadMore);
					if (index > -1) {
						coreAjax.xhrLoadMore.splice(index, 1);
					}
				}
			});
	},

	callIgnoreFilterBefore : function (url, data, callBackOK, callBackKO, type, isAsync, ignoreMask) {
		
		coreAjax.abortAllFilterAjax();
		
		type = typeof type !== 'undefined' ? type: 'POST';
		ignoreMask = typeof ignoreMask !== 'undefined' ? ignoreMask: false;
                if(coreAjax.mobileDebug){
                    alert('call ignore filter:'+ url);
                }
		$.ajax(
			{
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
						coreDialog.errorDialog(messageError);
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
	callIgnoreAutocompleteBefore : function (url, data, callBackOK, callBackKO, type, isAsync, ignoreMask) {
		
		coreAjax.abortAllAutocompleteAjax();
		
		type = typeof type !== 'undefined' ? type: 'POST';
		ignoreMask = typeof ignoreMask !== 'undefined' ? ignoreMask: false;
                if(coreAjax.mobileDebug){	
                    alert('call ignore auto complete:'+ url);
                }
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "json",
				data: data,
				beforeSend: function( jqXHR )
				{
					coreAjax.xhrAutocomplete.push(jqXHR);
					
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
						coreDialog.errorDialog(messageError);
					}
					
					if( callBackKO ) {
						callBackKO();
					}
				},
				complete: function(jqXHR) {
					var index = $.inArray(jqXHR, coreAjax.xhrAutocomplete);
					if (index > -1) {
						coreAjax.xhrAutocomplete.splice(index, 1);
					}
				}
			});
	},
	call : function (url, data, callBackOK, callBackKO, type, isAsync, ignoreMask) {
		type = typeof type !== 'undefined' ? type: 'POST';
		ignoreMask = typeof ignoreMask !== 'undefined' ? ignoreMask: false;
                if(coreAjax.mobileDebug){
                    alert('URL:'+url);
                }
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "json",
				data: data,
				beforeSend: function()
				{
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
						coreDialog.errorDialog(messageError);
					}
					
					if( callBackKO ) {
						callBackKO();
					}
				}
			});
	},

	getHtml : function (url, data, callBackOK, callBackKO, type, isAsync, ignoreMask) {
		type = typeof type !== 'undefined' ? type: 'POST';
		ignoreMask = typeof ignoreMask !== 'undefined' ? ignoreMask: false;
                if(coreAjax.mobileDebug){	
                    alert('get html:'+ url);
                }
		$.ajax(
			{
				async : isAsync ? false : true,
				type: type,
				url: url,
				dataType: "html",
				data: data,
				beforeSend: function()
				{
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
						coreDialog.errorDialog(messageError);
					}
					
					if( callBackKO ) {
						callBackKO();
					}
				}
			});
	}
}

var ajaxEventHandle = {
		maskDiv		: null,
		
		init : function () {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			/*
			$( document ).ajaxStart( ajaxEventHandle.ajaxStart );
			$( document ).ajaxStop( ajaxEventHandle.ajaxStop );
			*/
			
			$('<div class="mask"><div class="mask-body"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div><div class="spinner-loading-text">'+Lang.get('common::app.loading_text')+'</div></div></div>').appendTo(document.body).hide();
			
			ajaxEventHandle.maskDiv = $('div.mask');
		},
		
		ajaxStart : function() {
			ajaxEventHandle.showMask();
		},
		
		ajaxStop : function() {
			ajaxEventHandle.hideMask();
		},
		
		showMask : function() {
                        //$('body').addClass('modal-open');
			ajaxEventHandle.maskDiv.show();
		},
		
		hideMask : function() {
                        //$('body').removeClass('modal-open');
			ajaxEventHandle.maskDiv.hide();
		}
}

ajaxEventHandle.init();
