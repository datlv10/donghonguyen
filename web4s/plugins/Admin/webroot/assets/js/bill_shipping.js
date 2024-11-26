"use strict";

var nhBillShipping = {
	idWarp: '#wrap-shipping-method',
	idCheckboxConfirm: '#cb-confirm-shipping',
	idWrapCarriers: '#wrap-shipping-carries',
	classWrapShippingMethod: '.wrap-shipping-method',
	confirm:{
		// idForm: '#shipping-confirm-form',
		idModal: '#shipping-confirm-modal',
		idBtnConfirm: '#shipping-confirm-btn',
	},
	data:{
		cod_money: 0,
		customer: {},
		items: {}
	},
	formEl: null,
	init: function(formEl = null){
		var self = this;
		self.formEl = formEl;
		if(formEl == null || self.formEl.length == 0) return;

		self.processDataBillForShipping();

		$(document).on('click', self.idWarp + ' a[data-shipping]', function(e) {
			self.clearValueShipping();

			var shippingMethod = $(this).data('shipping');
			$(self.idWarp).find('input[name="shipping_method"]').val(shippingMethod);
		  	self.showShippingMethod();


		  	// nếu chọn phương thức gửi hãng thì load thêm danh sách hãng vận chuyển
		  	var wrapCarriers = $(self.idWrapCarriers);
		  	wrapCarriers.html('');

		  	var showShipping = $(self.classWrapShippingMethod + '[data-type="shipping_info"]').hasClass('d-none') ? false : true;
		  	if(shippingMethod == _SHIPPING_CARRIER && showShipping){
				self.loadCarriers();
			}
		});

		$(document).on('keypress', 'input[name="shipping_fee_customer"]', function(e) {
			if(typeof(nhBillForm) == _UNDEFINED) return;

			nhBillForm.parseData();
			nhBillForm.updateLabelOrder();
		});

		$(document).on('change', self.idWarp + ' input[name="carrier_shipping_fee"]', function(e) {
			var carrier = $(this).attr('data-carrier');
			var carrierService = $(this).attr('data-carrier-service');
			var carrierServiceType = $(this).attr('data-carrier-service-type');
			var carrierShippingFee = nhMain.utilities.parseInt($(this).val());
			var carrierShopId = $('select[select-shop="'+ carrier +'"]').val();

			var applyForCustomer = $(self.idWarp).find('input[name=apply_for_customer]:checked').val() > 0 ? true : false;
			if(typeof(carrier) == _UNDEFINED || carrier.length == 0) return;

			$(self.idWarp).find('input[name="shipping_fee"]').val(carrierShippingFee);
			$(self.idWarp).find('input[name="carrier_code"]').val(carrier);
			$(self.idWarp).find('input[name="carrier_service_code"]').val(carrierService);
			$(self.idWarp).find('input[name="carrier_service_type_code"]').val(carrierServiceType);
			$(self.idWarp).find('input[name="carrier_shop_id"]').val(carrierShopId);
			
			if(applyForCustomer){
				$(self.idWarp).find('input[name="shipping_fee_customer"]').val(carrierShippingFee);
				self.processDataBillForShipping();

				$(self.idWarp).find('input[name="shipping_fee_customer"]').trigger('keypress');
			}

			$('[nh-carrier-shop]').addClass('d-none');
			$('[nh-carrier-shop="'+ carrier +'"]').removeClass('d-none');
		});

		$(document).on('keypress', self.idWarp + ' input[name="shipping_fee"]', function(e) {
			var radioApplyForCustomer = $(self.idWarp).find('input[name=apply_for_customer]:checked');
			var applyForCustomer = $(self.idWarp).find('input[name=apply_for_customer]:checked').val() > 0 ? true : false;

			var carrierShippingFee = nhMain.utilities.parseInt($(this).val().replaceAll(',', ''));
			if(applyForCustomer){
				$(self.idWarp).find('input[name="shipping_fee_customer"]').val(carrierShippingFee);
				self.processDataBillForShipping();

				$(self.idWarp).find('input[name="shipping_fee_customer"]').trigger('keypress');
			}			
		});

		$(document).on('change', self.idWarp + ' input[name="apply_for_customer"]', function(e) {
			var inputShippingFeeCustomer = $(self.idWarp).find('input[name="shipping_fee_customer"]');
			if(inputShippingFeeCustomer.length == 0) return;

			if($(this).val() == 0){
				inputShippingFeeCustomer.val('');
				self.processDataBillForShipping();
				inputShippingFeeCustomer.trigger('keypress');
			}else{
				var shippingFee = nhMain.utilities.parseFloat($(self.idWarp).find('input[name="shipping_fee"]').val().replaceAll(',', ''));

				inputShippingFeeCustomer.val(shippingFee);
				self.processDataBillForShipping();
				inputShippingFeeCustomer.trigger('keypress');
			}
		});

		$(document).on('change', self.idWarp + ' select[select-shop]', function(e) {
			$('input[name="carrier_shop_id"]').val($(this).val());
			self.loadCarriers();
		});

		// ----------------------------- event on add new bill ------------------------------------------------

		$(document).on('change', self.idCheckboxConfirm, function(e) {
			$(self.idWarp).collapse($(this).is(':checked') ? 'show' : 'hide');
			self.clearValueShipping();
		});

		$(self.idWarp).on('hidden.bs.collapse', function () {
			self.clearValueShipping();
		});

		$(document).on('click', '.trigger-customer', function(e) {
			KTUtil.scrollTo($('input#customer-suggest')[0], nhMain.validation.offsetScroll);
			setTimeout(function(){ 
				$('input#customer-suggest').focus();
			}, 500);
		});

		$(document).on('click', '.trigger-district', function(e) {
			$('.modal').modal('hide');
			
			if($('#order-contact-modal').length > 0){
				$('#order-contact-modal').modal('show');
				return false;
			}			

			if($('#customer-info').length > 0){
				KTUtil.scrollTo($('#customer-info')[0], nhMain.validation.offsetScroll);
				return false;
			}
		});

		$(document).on('click', '.trigger-product', function(e) {
			KTUtil.scrollTo($('input#product-suggest')[0], nhMain.validation.offsetScroll);
			setTimeout(function(){ 
				$('input#product-suggest').focus();
			}, 500);
		});

		// ----------------------------- event of shipping-confirm modal on bill detail -----------------------------------------

		$(document).on('click', '.icon-arrow-shipping', function(e) {
			if($(this).attr('aria-expanded') == 'false'){
				$(this).removeClass('fa-caret-down').addClass('fa-caret-right');
			}else{
				$(this).removeClass('fa-caret-right').addClass('fa-caret-down');
			}
		});

		$(document).on('click', '.btn-confirm-shipping', function(e) {
			$(self.idWarp).removeClass('collapse');
		  	self.showShippingMethod();
			$(self.confirm.idModal).modal('show');
		});

		$(document).on('click', self.confirm.idBtnConfirm, function(e) {
			e.preventDefault();
			var confirmForm = $(self.confirm.idModal).find('form');
			var btnSave = $(this);

			KTApp.progress(btnSave);
			KTApp.blockPage(blockOptions);

			var formData = confirmForm.serialize();
			nhMain.callAjax({
				url: confirmForm.attr('action'),
				data: formData,
				async: false
			}).done(function(response) {
				KTApp.unblockPage();
				KTApp.unprogress(btnSave);

				$(self.idModal).modal('hide');

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

		$(document).on('click', '.btn-shipping-status', function(e) {
			var status = $(this).data('status');
			var shipping_id = $(this).data('shipping-id');
			var _modal = $('#shipping-status-modal');

			var title = '';
			var message = '';
			var btnLabel = '';

			switch(status){				
				case _DELIVERY:
			    	title = nhMain.getLabel('xuat_kho_cho_don_giao_hang');
			    	message = nhMain.getLabel('ban_co_chac_chan_xuat_kho_don_giao_hang_nay_khong');
			    	btnLabel = nhMain.getLabel('xuat_kho');
			    break;

			    case _DELIVERED:
			    	title = nhMain.getLabel('cap_nhat_trang_thai_don_giao_hang');
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
			_modal.find('#btn-change-status').attr('data-shipping-id', shipping_id);

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
	},
	processDataBillForShipping: function(){
		var self = this;

		var data = {
			cod_money: 0,
			customer: {},
			items: {}
		}

		var dataItems = [];
		var codMoney = 0;
		// xử lý dữ liệu khi ở trang chi tiết
		if($('#products-item').length > 0){
			dataItems = nhMain.utilities.parseJsonToObject($('#products-item').val());

			// khi ở trang chi tiết cod sẽ bằng số tiền chưa thanh toán + phí ship của khách hàng
			var debt = nhMain.utilities.parseFloat(self.formEl.find('input#debt').val())

			var inputShippingFeeCustomer = $(self.idWarp).find('input[name="shipping_fee_customer"]').length > 0 ? $(self.idWarp).find('input[name="shipping_fee_customer"]') : null;
			var shippingFeeCustomer = inputShippingFeeCustomer != null ? nhMain.utilities.parseFloat(inputShippingFeeCustomer.val().replaceAll(',', '')) : 0;

			var fixShippingFeeCustomer = $(self.idWarp).find('input[name="apply_for_customer"]') != null ? nhMain.utilities.parseInt($(self.idWarp).find('input[name="apply_for_customer"]:checked').val()) : 0;
			if(fixShippingFeeCustomer > 0){
				codMoney = debt + shippingFeeCustomer;
			}else{
				codMoney = debt;
			}
			


		}else if(typeof(nhBillForm.data.items) != _UNDEFINED && !$.isEmptyObject(nhBillForm.data.items)){
			// xử lý dữ liệu khi ở trang thêm mới
			dataItems = nhBillForm.data.items;
			codMoney = typeof(nhBillForm.data.total) != _UNDEFINED ? nhMain.utilities.parseInt(nhBillForm.data.total) : 0;
		}

		data.items = dataItems;

		var inputContact = self.formEl.find('input[name="contact"]');
		if(inputContact.length > 0){
			data.customer = nhMain.utilities.parseJsonToObject(inputContact.val());
		}

		$('input[name="cod_money"]').val(codMoney);
		
		data.cod_money = codMoney;
		self.data = data;
	},
	showShippingMethod: function(){
		var self = this;	
		self.processDataBillForShipping();

		$(self.classWrapShippingMethod).addClass('d-none');

		var shipping_method = $(self.idWarp).find('a.active[data-shipping]').data('shipping');
		$(self.idWarp).find('input[name="shipping_method"]').val(shipping_method);


		// validate data for shipping
		var codMoney = typeof(self.data.cod_money) != _UNDEFINED ? nhMain.utilities.parseInt(self.data.cod_money) : 0;
		var hasProduct = typeof(self.data.items) && !$.isEmptyObject(self.data.items) != _UNDEFINED ? 1 : 0;
		var customerInfo = typeof(self.data.customer) != _UNDEFINED ? self.data.customer : {};

		// check selected customer
		if($.isEmptyObject(customerInfo)){		
			$(self.classWrapShippingMethod + '[data-type="no_customer"]').removeClass('d-none');
			self.clearValueShipping();
			return false;
		}

		// check no_district
		if(typeof(customerInfo.district_id) == _UNDEFINED || !customerInfo.district_id > 0){
			$(self.classWrapShippingMethod + '[data-type="no_district"]').removeClass('d-none');
			self.clearValueShipping();
			return false;
		}

		// check no_ward
		if(shipping_method == _SHIPPING_CARRIER && (typeof(customerInfo.ward_id) == _UNDEFINED || !customerInfo.ward_id > 0)){
			$(self.classWrapShippingMethod + '[data-type="no_ward"]').removeClass('d-none');
			self.clearValueShipping();
			return false;
		}

		// check selected products
		if(!hasProduct > 0){
			$(self.classWrapShippingMethod + '[data-type="no_product"]').removeClass('d-none');
			self.clearValueShipping();
			return false;
		}
		

		// show label shipping address
		var addressName = typeof(customerInfo.address_name) != _UNDEFINED ? customerInfo.address_name : '';
		var phone = typeof(customerInfo.phone) != _UNDEFINED ? customerInfo.phone : '';
		var address = typeof(customerInfo.full_address) != _UNDEFINED ? customerInfo.full_address : '';

		$(self.classWrapShippingMethod + ' [label-shipping-address-name]').text(addressName);
		$(self.classWrapShippingMethod + ' [label-shipping-phone]').text(phone);
		$(self.classWrapShippingMethod + ' [label-shipping-address]').text(address);
		
		// show shipping method
		$(self.idWarp).find('input[name="cod_money"]').val(codMoney);
		var unitInfo = self.getDataWeightAndLength();
		
		var weight = typeof(unitInfo.weight) != _UNDEFINED ? nhMain.utilities.parseFloat(unitInfo.weight) : _WEIGHT_PRODUCT_DEFAULT;
		var length = typeof(unitInfo.length) != _UNDEFINED ? nhMain.utilities.parseFloat(unitInfo.length) : _LENGTH_PRODUCT_DEFAULT;
		var width = typeof(unitInfo.width) != _UNDEFINED ? nhMain.utilities.parseFloat(unitInfo.width) : _WIDTH_PRODUCT_DEFAULT;
		var height = typeof(unitInfo.height) != _UNDEFINED ? nhMain.utilities.parseFloat(unitInfo.height) : _HEIGHT_PRODUCT_DEFAULT;

	  	$(self.idWarp).find('input[name="weight"]').val(weight);
	  	$(self.idWarp).find('input[name="length"]').val(length);
	  	$(self.idWarp).find('input[name="width"]').val(width);
	  	$(self.idWarp).find('input[name="height"]').val(height);

		$(self.classWrapShippingMethod + '[data-type="shipping_info"]').removeClass('d-none');
	},
	clearValueShipping: function(){
		var self = this;

		var wrap = $(self.idWarp);
		if(wrap.length == 0) return;

		wrap.find('input[name="shipping_fee"]').val('');
		wrap.find('input[name="cod_money"]').val('');
		wrap.find('input[name="shipping_note"]').val('');
		wrap.find('input[name="carrier_code"]').val('');
		wrap.find('input[name="carrier_service_code"]').val('');
	},
	loadCarriers: function(params = {}) {
		var self = this;

		var wrapCarriers = $(self.idWrapCarriers);
		if(wrapCarriers.length == 0) return;

		var data = self.getDataWeightAndLength();
		data.address_info = self.data.customer;			

		data.carrier_code = $(self.idWarp).find('input[name="carrier_code"]').val();
		data.carrier_service_code = $(self.idWarp).find('input[name="carrier_service_code"]').val();
		data.carrier_service_type_code = $(self.idWarp).find('input[name="carrier_service_type_code"]').val();
		data.carrier_shop_id = $(self.idWarp).find('input[name="carrier_shop_id"]').val();
		

		KTApp.blockPage(blockOptions);
		nhMain.callAjax({
    		// async: false,
    		dataType: 'html',
			url: adminPath + '/order/load-carries-for-order',
			data: data
		}).done(function(response) {
			wrapCarriers.html(response);
			wrapCarriers.find('.kt-selectpicker').selectpicker();
			KTApp.unblockPage();
		});
	},
	getDataWeightAndLength: function(){
		var self = this;

		var result = {
			width: 0,
			height: 0,
			length: 0,
			weight: 0,
		}

		var dataItems = [];
		// nếu có input products-item thì lấy data items từ input này
		if($('#products-item').length > 0){
			dataItems = nhMain.utilities.parseJsonToObject($('#products-item').val());
		}else if(typeof(nhBillForm.data.items) != _UNDEFINED && !$.isEmptyObject(nhBillForm.data.items)){
			// nếu không thì lấy data từ BillForm
			dataItems = nhBillForm.data.items;
		}else{
			return result;
		}
		
		var width = 0;
		var height = 0;
		var length = 0;
		var weight = 0;

		$.each(dataItems, function(index, item) {
			var quantity = typeof(item.quantity) != _UNDEFINED ? nhMain.utilities.parseInt(item.quantity) : 1;
			if(quantity < 1) quantity = 1;

			var width_item = typeof(item.width) != _UNDEFINED ? nhMain.utilities.parseFloat(item.width) : 0;
			var height_item = typeof(item.height) != _UNDEFINED ? nhMain.utilities.parseFloat(item.height) : 0;
			var length_item = typeof(item.length) != _UNDEFINED ? nhMain.utilities.parseFloat(item.length) : 0;
			var weight_item = typeof(item.weight) != _UNDEFINED ? nhMain.utilities.parseFloat(item.weight) : 0;

		  	var width_unit = typeof(item.width_unit) != _UNDEFINED ? item.width_unit : '';		  	
		  	var height_unit = typeof(item.height_unit) != _UNDEFINED ? item.height_unit : '';
		  	var length_unit = typeof(item.length_unit) != _UNDEFINED ? item.length_unit : '';
		  	var weight_unit = typeof(item.weight_unit) != _UNDEFINED ? item.weight_unit : '';

		  	width_item = self.exchangeLengthUnit(width_item, width_unit, 'cm');
		  	height_item = self.exchangeLengthUnit(height_item, length_unit, 'cm');
		  	length_item = self.exchangeLengthUnit(length_item, length_unit, 'cm');
		  	weight_item = self.exchangeWeightUnit(weight_item, weight_unit, 'g');

		  	width += width_item * quantity;
		  	height += height_item * quantity;
		  	length += length_item * quantity;
		  	weight += weight_item * quantity;
		});

		return {
			width: width,
			height: height,
			length: length,
			weight: weight,
		};
	},
	exchangeLengthUnit: function(value = null, current_unit = null, to_unit = 'cm'){
		var self = this;
		value = nhMain.utilities.parseFloat(value);

		if(!nhMain.utilities.notEmpty(value)) return 0;
		if($.inArray(current_unit, ['mm', 'cm', 'm']) == -1) return 0;

		var result = 0;
		switch(current_unit){
			case 'mm':
				switch(to_unit){
					// mm -> mm
					case 'mm':
						result = value;
					break;

					// mm -> cm
					case 'cm':
						result = value / 10;
					break;

					// mm -> m
					case 'm':
						result = value / 1000;
					break;
				}
			break;

			case 'cm':
				switch(to_unit){
					// cm -> mm
					case 'mm':
						result = value * 10;
					break;

					// cm -> cm
					case 'cm':
						result = value;
					break;

					// cm -> m
					case 'm':
						result = value / 100;
					break;
				}
			break;

			case 'm':
				switch(to_unit){
					// m -> mm
					case 'mm':
						result = value * 1000;
					break;

					// m -> cm
					case 'cm':
						result = value * 100;
					break;

					// m -> m
					case 'm':
						result = value;
					break;
				}
			break;
		}

		return result;
	},
	exchangeWeightUnit: function(value = null, current_unit = null, to_unit = 'g'){
		var self = this;
		value = nhMain.utilities.parseFloat(value);

		if(!nhMain.utilities.notEmpty(value)) return 0;
		if($.inArray(current_unit, ['g', 'kg']) == -1) return 0;

		var result = value;
		switch(current_unit){
			case 'g':
				switch(to_unit){
					// g -> g
					case 'g':
						result = value;
					break;

					// g -> kg
					case 'kg':
						result = value / 10;
					break;
				}
			break;

			case 'kg':
				switch(to_unit){
					// kg -> g
					case 'g':
						result = value * 10;
					break;

					// kg -> kg
					case 'kg':
						result = value;
					break;
				}
			break;
		}

		return result;
	}
}