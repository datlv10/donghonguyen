"use strict";

var nhShipmentDetail = function () {

	var init = function() {
		$(document).on('click', '.btn-shipping-status', function(e) {
			var status = $(this).data('status');
			var _modal = $('#shipping-status-modal');

			var title = '';
			var message = '';
			var btnLabel = '';

			switch(status){				
				case _DELIVERY:
			    	title = nhMain.getLabel('cap_nhat_trang_thai_dang_giao_hang');
			    	message = nhMain.getLabel('ban_co_chac_chan_chuyen_trang_thai_thanh_dang_giao_hang_khong');
			    	btnLabel = nhMain.getLabel('cap_nhat');
			    break;

			    case _DELIVERED:
			    	title = nhMain.getLabel('cap_nhat_trang_thai_da_giao_hang');
			    	message = nhMain.getLabel('ban_co_chac_chan_chuyen_trang_thai_thanh_da_giao_hang_khong');
			    	btnLabel = nhMain.getLabel('cap_nhat');
			    break;

			    case _CANCEL_PACKAGE:
			    	title = nhMain.getLabel('huy_dong_goi');
			    	message = nhMain.getLabel('ban_co_chac_chan_huy_dong_goi_don_giao_hang_nay_khong');
			    	btnLabel = nhMain.getLabel('huy_dong_goi');
			    break;

			    case _CANCEL_WAIT_DELIVER:
			    	title = nhMain.getLabel('huy_don_giao_hang');
			    	message = nhMain.getLabel('ban_co_chac_chan_huy_don_giao_hang_nay_khong');
			    	btnLabel = nhMain.getLabel('huy_giao_hang');
			    break;

			    case _CANCEL_DELIVERED:
			    	title = nhMain.getLabel('huy_don_giao_hang');
			    	message = nhMain.getLabel('ban_co_chac_chan_huy_don_giao_hang_nay_khong');
			    	btnLabel = nhMain.getLabel('huy_giao_va_nhan_lai_hang');
			    break;
			    
			}

			_modal.find('[label-title]').text(title);
			_modal.find('[label-message]').text(message);
			_modal.find('#btn-change-status').text(btnLabel);
			_modal.find('#btn-change-status').attr('data-status', status);

			_modal.modal('show');
		});

		$(document).on('click', '#btn-change-status', function(e) {
			e.preventDefault();
			var shipping_id = $(this).data('shipping-id');
			var status = $(this).data('status');

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
				url: adminPath + '/order/shipping-change-status/' + shipping_id,
				data: {
					status: status
				}
			}).done(function(response) {
				KTApp.unblockPage();

				$('#shipping-status-modal').modal('hide');

			   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	toastr.clear();

	            if (code == _SUCCESS) {
	            	toastr.info(message);

	            	location.reload();
	            } else {
	            	toastr.error(message);
	            }
			});
		});
	}
	
	return {
		init: function() {
			init();
		}
	};
}();


$(document).ready(function() {
	nhShipmentDetail.init();
});
