"use strict";

var nhPromotion = {
	allow: false,
	formEl: null,
	modal: $('#select-promotion-modal'),
	promotionInfo: null,
	wrapInfoElement: $('[nh-wrap-promotion-info]'),
	labelPromotionName: $('[nh-label-promotion-name]'),	
	init: function(formEl = null){
		var self = this;
		
		if(formEl == null || formEl.length == 0) return;
		if(self.modal.length == 0) return;
		if(typeof(formEl.attr('check-promotion')) == _UNDEFINED || !Boolean(formEl.attr('check-promotion'))) return;
		self.allow = true;
		self.formEl = formEl;

		self.event();
	},
	event: function(){
		var self = this;
		if(!self.allow) return;

		$(document).on('click', '#select-promotion-btn', function(e) {
			var order_info = typeof(nhBillForm) != _UNDEFINED && !$.isEmptyObject(nhBillForm.data) ? nhBillForm.data : {};
			if(typeof(order_info.items) == _UNDEFINED || $.isEmptyObject(order_info.items)){
				toastr.error(nhMain.getLabel('vui_long_chon_san_pham'));
				return;
			}
			self.modal.modal('show');
			self.loadListPromotionInvalid(order_info);
		});


		self.modal.on('click', '#apply-promotion-btn', function(e) {
			self.modal.modal('hide');
			
			var radioSelectedElement = self.modal.find('input[radio-select-promotion]:checked');
			var trElement = radioSelectedElement.length > 0 ? radioSelectedElement.closest('tr[nh-promotion-info]') : null;
			if(trElement != null && trElement.length > 0){
				self.promotionInfo = nhMain.utilities.parseJsonToObject(trElement.attr('nh-promotion-info'));
			}

			if(typeof(nhBillForm.data) != _UNDEFINED){
				self.applyPromotionToBill(nhBillForm.data);
			}			
		});

		self.modal.on('click', '#cancel-promotion-btn', function(e) {
			self.modal.modal('hide');

			self.promotionInfo = {};
			nhBillForm.clearPromotion();
			self.hideInfoPromotion();
		});
	},
	loadListPromotionInvalid: function(order_info = {}){
		var self = this;
		if(!self.allow) return;

		self.modal.find('.modal-body').html('');
		nhMain.callAjax({
    		async: true,
    		dataType: 'html',
			url: adminPath + '/promotion/load-list-promotion-invalid',
			data: {
				order: order_info
			}
		}).done(function(response) {
			self.modal.find('.modal-body').html(response);
			
			if(self.promotionInfo != null && !$.isEmptyObject(self.promotionInfo)){
				var promotionId = typeof(self.promotionInfo.id) != _UNDEFINED ? nhMain.utilities.parseInt(self.promotionInfo.id) : null;
				
				self.modal.find('tbody tr[nh-promotion-info]').each(function(index) {
					var radioElement = $(this).find('input[radio-select-promotion]');
					if(radioElement.length == 0) return;

					if(nhMain.utilities.parseInt(radioElement.attr('radio-select-promotion')) == promotionId){
						radioElement.prop('checked', true);
					}
				});
			}
		});
	},
	applyPromotionToBill: function(order_info = {}){
		var self = this;

		if(!self.allow) return;
		if($.isEmptyObject(self.promotionInfo) || $.isEmptyObject(order_info)) return;

		var typeDiscount = typeof(self.promotionInfo.type_discount) != _UNDEFINED ? self.promotionInfo.type_discount : null;
		var value = typeof(self.promotionInfo.value) != _UNDEFINED ? self.promotionInfo.value : {};

		if(typeDiscount == null || $.isEmptyObject(value)) return;
		
		nhBillForm.clearPromotion();

		// thay đổi các attribute khuyến mãi của form bill
		switch(typeDiscount){
			case 'discount_order':
				var buttonDiscount = self.formEl.find('a[data-discount]');
				if(buttonDiscount.length == 0) return;

				var typeValueDiscount = typeof(value.type_value_discount) != _UNDEFINED ? value.type_value_discount : _MONEY;
				var valueDiscount = typeof(value.value_discount) != _UNDEFINED ? nhMain.utilities.parseInt(value.value_discount) : 0;
				//hien thi gia tri khuyen mai co vuot qua max_value da cau hinh
				var maxValue = typeof(value.max_value) != _UNDEFINED ? nhMain.utilities.parseInt(value.max_value) : 0;
				if(maxValue > 0){
					var totalOrderOrigin = typeof(order_info.total_origin) != _UNDEFINED ? nhMain.utilities.parseInt(order_info.total_origin) : 0;
					var tmpDiscount = valueDiscount;
					if(typeValueDiscount == _PERCENT){
						tmpDiscount = Math.round(totalOrderOrigin * valueDiscount / 100);
					}

					// neu vuot qua max_value thi gia tri khuyen mai se bang max_value
					if(tmpDiscount > maxValue){
						typeValueDiscount = _MONEY;
						valueDiscount = maxValue;
					}					
				}

				buttonDiscount.attr('data-discount', valueDiscount);
				buttonDiscount.attr('data-discount-type', typeValueDiscount);
				buttonDiscount.attr('data-discount-note', '');
			break;

			case 'discount_product':
				var tableProduct = $(nhBillForm.idTable);
				if(tableProduct.length == 0) return;

				var typeValueDiscount = typeof(value.type_value_discount) != _UNDEFINED ? value.type_value_discount : _MONEY;
				var valueDiscount = typeof(value.value_discount) != _UNDEFINED ? nhMain.utilities.parseInt(value.value_discount) : 0;

				tableProduct.find('tbody tr:not(.no-product)').each(function(index) {
					var inputPrice = $(this).find('input[input-price]');
					var inputQuantity = $(this).find('input[input-quantity]');
					if(inputPrice.length == 0 || inputQuantity.length == 0) return;

					var price = nhMain.utilities.parseInt(inputPrice.attr('input-price'));
					var quantity = nhMain.utilities.parseInt(inputQuantity.attr('input-quantity'));

					var totalDiscount = 0;
					var newPrice = 0;
					if(typeValueDiscount == _PERCENT){
						newPrice = price - Math.round(price * valueDiscount / 100);
						totalDiscount = Math.round(price * valueDiscount / 100) * quantity;
					}else{
						newPrice = price - valueDiscount;
						totalDiscount = valueDiscount * quantity;
					}
					
					$(this).attr('data-discount', valueDiscount);
					$(this).attr('data-discount-type', typeValueDiscount);
					$(this).attr('data-total-discount', totalDiscount);

					inputPrice.attr('input-price', newPrice);
					inputPrice.val(newPrice);
				});
			break;

			case 'free_ship':
				var labelShipping = $(nhBillForm.idWrap).find('[label-shipping-fee-customer]');
				var inputShippingValue = $(nhBillForm.idWrap).find('input[name="shipping_fee_customer"]');

				if(labelShipping.length > 0){
					labelShipping.attr('label-shipping-fee-customer', 0);
					labelShipping.text(0);
				}

				if(inputShippingValue.length > 0){
					inputShippingValue.val('');
				}
			break;

			case 'give_product':

			break;
		}

		nhBillForm.parseData();
		nhBillForm.updateLabelOrder();		
		$(nhBillForm.idWrap).find('input[name="promotion_id"]').val(self.promotionInfo.id);

		self.showInfoPromotion(self.promotionInfo);
	},
	showInfoPromotion: function(promotionInfo = {}){
		var self = this;
		if(!self.allow || $.isEmptyObject(promotionInfo)) return;
		if(self.wrapInfoElement.length == 0 || self.labelPromotionName.length == 0) return;
		var promotionName = typeof(promotionInfo.name) != _UNDEFINED ? promotionInfo.name : '';
		self.labelPromotionName.text(promotionName);
		self.wrapInfoElement.removeClass('d-none');

	},
	hideInfoPromotion: function(){
		var self = this;
		if(!self.allow) return;
		if(self.wrapInfoElement.length == 0 || self.labelPromotionName.length == 0) return;

		self.wrapInfoElement.addClass('d-none');
		self.labelPromotionName.text('');
		
	},
	check: function(order_info = {}, coupon = null){
		var self = this;
		if(!self.allow || $.isEmptyObject(order_info)) return [];

		nhMain.callAjax({
    		async: false,
			url: adminPath + '/promotion/check',
			data: {
				coupon: coupon,
				order: order_info
			}
		}).done(function(response) {
			if(response.code = _SUCCESS){
				
			}
		});

	}
}