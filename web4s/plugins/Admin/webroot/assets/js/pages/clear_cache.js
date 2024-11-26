$(document).on('click', '.nh-clear-cache', function() {
    swal.fire({
        title: nhMain.getLabel('xoa_cache'),
        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_cache'),
        type: 'warning',
        
        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
        confirmButtonClass: 'btn btn-sm btn-danger',

        showCancelButton: true,
        cancelButtonText: nhMain.getLabel('huy_bo'),
        cancelButtonClass: 'btn btn-sm btn-default'
    }).then(function(result) {
    	if(typeof(result.value) != _UNDEFINED && result.value){
            KTApp.blockPage(blockOptions);
            
    		nhMain.callAjax({
				url: adminPath + '/setting/clear-cache'
			}).done(function(response) {
                KTApp.unblockPage();

				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

			    if (code == _SUCCESS) {
	            	toastr.info(message);
	            } else {
	            	toastr.error(message);
	            }
			});
    	}    	
    });
	return false;
});