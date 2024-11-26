"use strict";

var nhBillForm = {
	idWrap: '#main-form',
	idTable: '#table-products',
	idProductSuggest: '#product-suggest',
	template: {
		rowTable: '',
	},
	data: {},
	init: function(params = {}){
		var self = this;
		if(typeof(params.idTable) != _UNDEFINED && $(params.idTable).length > 0){
			self.idTable = params.idTable;
		}

		// validate
		if($(self.idTable).length == 0) return false;
		
		var firstRow = $(self.idTable).find('tbody tr:not(.no-product)').first();
		self.template.rowTable = firstRow.clone()[0].outerHTML;
		if(firstRow.data('id') == '' && firstRow.data('product-id') == '' && firstRow.data('product-item-id') == ''){
			firstRow.remove();
		}

		self.parseData();

		$(document).on('keyup keypress paste focus', self.idProductSuggest, function(e) {
			nhMain.autoSuggest.basic({
				inputSuggest: 'input' + self.idProductSuggest,
				url: adminPath + '/product/auto-suggest',
				fieldLabel: 'name_price',
				itemMore: {
					label: nhMain.getLabel('them_moi_san_pham'),
					value: 'add_product'
				}
			}, function(response){
				if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED){
					if(response.id == 'add_product'){
						$('#quick-add-product-modal').modal('show');
						return false;	
					}

					self.loadInfoProductToList(response);
					self.parseData();
					self.updateLabelOrder();
					self.checkClearPromotion();

				}
			});
			
			if(e.type == 'focusin'){
				$(this).autocomplete('search', $(this).val());
			}
		});

		$(document).on('click', self.idTable + ' i[action-item="delete"]', function(e) {
			var rowItem = $(this).closest('tr');
			rowItem.remove();

			self.parseData();
			self.updateLabelOrder();
			self.checkClearPromotion();
		});

		$(document).on('click focus', self.idTable + ' tr:not(.no-product) input[input-price]', function(e) {
			e.preventDefault();

			var _this = $(this);
			var contentPopover = typeof($('#popover-price-bill').html()) != _UNDEFINED ? $('#popover-price-bill').html() : '';
			if(contentPopover.length == 0){
				return false;
			}

			var rowItem = _this.closest('tr');
			$(self.idTable + ' input[input-price]').not(this).popover('hide');

			_this.popover({
    			placement: 'left',
    			html: true,
    			sanitize: false,
    			trigger: 'manual',
	            content: contentPopover,
	           	template: '\
		            <div class="popover lg-popover" role="tooltip">\
		                <div class="arrow"></div>\
		                <div class="popover-body"></div>\
		            </div>'
	        });

			var price = typeof(_this.attr('input-price')) != _UNDEFINED ? nhMain.utilities.parseFloat(_this.attr('input-price')) : '';
			var originPrice = price;

			var vat = typeof(rowItem.attr('data-vat')) != _UNDEFINED ? nhMain.utilities.parseInt(rowItem.attr('data-vat')) : '';
			if(vat > 0){
				originPrice -= price / (100 + vat) * vat;
			}

			var discountValue = typeof(rowItem.attr('data-discount')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.attr('data-discount')) : '';
			var typeDiscount = _MONEY;
			if(discountValue > 0){
				typeDiscount = typeof(rowItem.attr('data-discount-type')) != _UNDEFINED ? rowItem.attr('data-discount-type') : _MONEY;	

				if(typeDiscount == _PERCENT){			
					originPrice = originPrice / (100 - discountValue) * 100;
				}else{
					originPrice += discountValue;
				}		
			}

	        _this.popover('show');
	        _this.on('shown.bs.popover', function (e) {		        	
	        	var idPopover = _this.attr('aria-describedby');
	        	var _popover = $('#' + idPopover);
	        	
	        	_popover.find('#price').attr('data-price', price).val(originPrice).focus();
	        	_popover.find('#discount').val(discountValue);
	        	_popover.find('.discount-type-item').removeClass('active');
	        	_popover.find('.discount-type-item[data-discount-type="'+ typeDiscount +'"]').addClass('active');

	        	_popover.find('#vat').val(vat);

	        	_popover.find('.number-input').each(function() {
					nhMain.input.inputMask.init($(this), 'number');
				});
			})
			return false;
		});

		$(document).on('click', '#confirm-price-bill', function(e) {
			e.preventDefault();

			var _popover = $(this).closest('.popover.lg-popover');
			var idPopover = _popover.attr('id');
			var btnPopover = $('input[input-price][aria-describedby="'+ idPopover +'"]');
			var rowItem = btnPopover.closest('tr');

			var priceInput = _popover.find('#price');
			var discountInput = _popover.find('#discount');
			var vatInput = _popover.find('#vat');

			var priceOrigin = nhMain.utilities.parseTextMoneyToNumber(priceInput.val());
			var priceOld = nhMain.utilities.parseFloat(priceInput.attr('data-price'));

			var discount = nhMain.utilities.parseTextMoneyToNumber(discountInput.val());
			var typeDiscount = _popover.find('.discount-type-item.active').attr('data-discount-type');

			var vat = nhMain.utilities.parseTextMoneyToNumber(vatInput.val());
			

			// validate value
			nhMain.validation.error.clear(_popover);

			if(discount < 0){
				nhMain.validation.error.show(discountInput, nhMain.getLabel('gia_tri_chiet_khau_khong_hop_le'));
				return false;
			}

			if((typeDiscount == _PERCENT && discount > 100) || (typeDiscount == _MONEY && discount > priceOrigin)){
				nhMain.validation.error.show(discountInput, nhMain.getLabel('chiet_khau_vuot_qua_gia_tri_san_pham'));
				return false;
			}

			if(vat < 0){
				nhMain.validation.error.show(discountInput, nhMain.getLabel('gia_tri_thue_khong_hop_le'));
				return false;
			}

			if(vat >= 100){
				nhMain.validation.error.show(vatInput, nhMain.getLabel('gia_tri_thue_qua_cao'));
				return false;
			}

			var discountItem = 0;
			if(typeDiscount == _PERCENT){
				discountItem = priceOrigin / 100 * discount;
			}else{
				discountItem = discount;
			}

			var price = priceOrigin - discountItem;
			if(vat > 0){
				price += price / 100 * vat;
			}

			// set value
			rowItem.attr('data-discount-type', typeDiscount);
			rowItem.attr('data-discount', discount);
			rowItem.attr('data-vat', vat);

			rowItem.find('input[input-price]').attr('input-price', price).val(price);

			btnPopover.popover('dispose');

			self.parseData();
			self.updateLabelOrder();
		});

		$(document).on('change', self.idTable + ' input[input-price]', function(e) {
			var price = nhMain.utilities.parseTextMoneyToNumber($(this).val());
			$(this).attr('input-price', price);

			self.parseData();
			self.updateLabelOrder();
		});

		$(document).on('change', self.idTable + ' input[input-quantity]', function(e) {
			var _this = $(this);
			var quantity = nhMain.utilities.parseTextMoneyToNumber(_this.val());
			_this.attr('input-quantity', quantity);

			self.parseData();
			self.updateLabelOrder();
			self.checkClearPromotion();
			self.checkClearPromotion();
		});

		$(document).on('keyup keypess', self.idTable + ' input[input-quantity]', function(e) {
			var _this = $(this);	

			if(typeof(_this.attr('max-quantity')) != _UNDEFINED){
				var quantity = nhMain.utilities.parseTextMoneyToNumber(_this.val());
				var maxQuantity = nhMain.utilities.parseInt(_this.attr('max-quantity'));
				if(quantity > maxQuantity){
					_this.val(maxQuantity);
				}
			}			
		});

		$(document).on('click', '#cancel-price-bill', function(e) {
			var idPopover = $(this).closest('.popover.lg-popover').attr('id');
			var btnPopover = $('input[input-price][aria-describedby="'+ idPopover +'"]');
			btnPopover.popover('dispose');
		});

		$(document).on('click', '.popover-body .discount-type-item', function(e) {
			var popover = $(this).closest('.popover');
			popover.find('.discount-type-item').removeClass('active');
			$(this).addClass('active');

			popover.find('input#discount').val(0);
		});

		$(document).on('click', self.idWrap +  ' a[data-discount]', function(e) {
			var _this = $(this);
			var rowItem = _this.closest('tr');
			$(self.idTable + ' input[input-price]').not(this).popover('hide');

			_this.popover({
    			placement: 'left',
    			html: true,
    			sanitize: false,
    			trigger: 'manual',
	            content: $('#popover-discount-bill').html(),
	           	template: '\
		            <div class="popover lg-popover" role="tooltip">\
		                <div class="arrow"></div>\
		                <div class="popover-body"></div>\
		            </div>'
	        });

			var discount = typeof(_this.attr('data-discount')) != _UNDEFINED ? nhMain.utilities.parseFloat(_this.attr('data-discount')) : '';
			var typeDiscount = (typeof(_this.attr('data-discount-type')) != _UNDEFINED && _this.attr('data-discount-type').length > 0) ? _this.attr('data-discount-type') : _MONEY;
			var note = typeof(_this.attr('data-discount-note')) != _UNDEFINED ? _this.attr('data-discount-note') : _MONEY;

	        _this.popover('show');
	        _this.on('shown.bs.popover', function (e) {		        	
	        	var idPopover = _this.attr('aria-describedby');
	        	var _popover = $('#' + idPopover);
	        	
	        	_popover.find('#discount').val(discount);
	        	_popover.find('.discount-type-item').removeClass('active');
	        	_popover.find('.discount-type-item[data-discount-type="'+ typeDiscount +'"]').addClass('active');
	        	_popover.find('#note').val(note);

	        	_popover.find('.number-input').each(function() {
					nhMain.input.inputMask.init($(this), 'number');
				});
			})
			return false;
		});

		$(document).on('click', '#confirm-discount-bill', function(e) {
			var _popover = $(this).closest('.popover.lg-popover');
			var idPopover = _popover.attr('id');
			var btnPopover = $('a[data-discount][aria-describedby="'+ idPopover +'"]');

			var discount = nhMain.utilities.parseTextMoneyToNumber(_popover.find('#discount').val());		
			var typeDiscount = _popover.find('.discount-type-item.active').attr('data-discount-type');
			var note = _popover.find('#note').val();

			// validate value
			nhMain.validation.error.clear(_popover);

			if(discount < 0){
				nhMain.validation.error.show(_popover.find('#discount'), nhMain.getLabel('gia_tri_chiet_khau_khong_hop_le'));
				return false;
			}

			if((typeDiscount == _PERCENT && discount > 100) || (typeDiscount == _MONEY && discount > self.data.total_items)){
				nhMain.validation.error.show(_popover.find('#discount'), nhMain.getLabel('chiet_khau_vuot_qua_gia_tri_don_hang'));
				return false;
			}

			var totalDiscount = 0;
			if(typeDiscount == _PERCENT){
				totalDiscount = discount * self.data.total_items / 100;
			}else{
				totalDiscount = discount;
			}

			// set value
			btnPopover.attr('data-discount', discount);
			btnPopover.attr('data-discount-type', typeDiscount);
			btnPopover.attr('data-discount-note', note);

			btnPopover.popover('dispose');

			self.parseData();
			self.updateLabelOrder();
		});

		$(document).on('click', '#cancel-discount-bill', function(e) {
			var idPopover = $(this).closest('.popover.lg-popover').attr('id');
			var btnPopover = $('a[data-discount][aria-describedby="'+ idPopover +'"]');
			btnPopover.popover('dispose');
		});
	},
	parseData: function(){
		var self = this;

		self.initData();

		// parse data items
		$(self.idTable + ' tbody').find('tr:not(.no-product)').each(function(index) {
			var rowItem = $(this);

			var quantity = typeof(rowItem.find('input[input-quantity]').attr('input-quantity')) != _UNDEFINED ? nhMain.utilities.parseTextMoneyToNumber(rowItem.find('input[input-quantity]').val()) : 1;
			var price = typeof(rowItem.find('input[input-price]').attr('input-price')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.find('input[input-price]').attr('input-price')) : 0;		
			var priceOrigin = price;
			var totalItem = price * quantity;				

			var vatValue = typeof(rowItem.attr('data-vat')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.attr('data-vat')) : 0;
			var totalVat = 0;
			if(vatValue > 0){
				var vatItem = price / (100 + vatValue) * vatValue;

				priceOrigin -= vatItem;
				totalVat = vatItem * quantity;
			}
			
			var discountValue = typeof(rowItem.attr('data-discount')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.attr('data-discount')) : 0;

			var totalDiscount = 0;
			var discountType = '';

			if(discountValue > 0){
				discountType = typeof(rowItem.attr('data-discount-type')) != _UNDEFINED ? rowItem.attr('data-discount-type') : null;
				var discount = 0;
				if(discountType == _PERCENT){					
					discount = priceOrigin / (100 - discountValue) * discountValue;
				}else{
					discount = discountValue;
				}
				priceOrigin += discount;
				totalDiscount = discount * quantity;			
			}	

			var totalOrigin = priceOrigin * quantity;

			var product_item_id = typeof(rowItem.attr('data-product-item-id')) != _UNDEFINED ? rowItem.attr('data-product-item-id') : null;
			var item = {
				id: typeof(rowItem.attr('data-id')) != _UNDEFINED ? rowItem.attr('data-id') : null,
				product_id: typeof(rowItem.attr('data-product-id')) != _UNDEFINED ? rowItem.attr('data-product-id') : null,
				product_item_id: product_item_id,
				name: typeof(rowItem.find('span[label-name]').attr('label-name')) != _UNDEFINED ? rowItem.find('span[label-name]').attr('label-name') : '', 
				price: price,
				quantity: quantity,
				discount_type: discountType,
				discount_value: discountValue,
				vat_type: '',
				vat_value: vatValue,
				total_discount: totalDiscount,
				total_vat: totalVat,
				total_item: totalItem,

				width: typeof(rowItem.attr('data-width')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.attr('data-width')) : 0,
				length: typeof(rowItem.attr('data-length')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.attr('data-length')) : 0,
				height: typeof(rowItem.attr('data-height')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.attr('data-height')) : 0,
				weight: typeof(rowItem.attr('data-weight')) != _UNDEFINED ? nhMain.utilities.parseFloat(rowItem.attr('data-weight')) : 0,

				width_unit: typeof(rowItem.attr('data-width_unit')) != _UNDEFINED ? rowItem.attr('data-width_unit') : '',
				length_unit: typeof(rowItem.attr('data-length_unit')) != _UNDEFINED ? rowItem.attr('data-length_unit') : '',
				height_unit: typeof(rowItem.attr('data-height_unit')) != _UNDEFINED ? rowItem.attr('data-height_unit') : '',
				weight_unit: typeof(rowItem.attr('data-weight_unit')) != _UNDEFINED ? rowItem.attr('data-weight_unit') : ''
			};
			
			self.data.items[product_item_id] = item;
			self.data.total_origin += totalOrigin;
			self.data.total_vat += totalVat;
			self.data.total_items += totalItem;

			self.data.total_discount_items += totalDiscount;

			self.data.number_items += quantity;
		});

		var buttonDiscount = $(self.idWrap).find('a[data-discount]');
		self.data.discount_value = typeof(buttonDiscount.attr('data-discount')) ? nhMain.utilities.parseFloat(buttonDiscount.attr('data-discount')) : 0;
		self.data.discount_type = '';

		if(self.data.discount_value > 0){
			self.data.discount_type = typeof(buttonDiscount.attr('data-discount-type')) ? buttonDiscount.attr('data-discount-type') : _MONEY;
			self.data.discount_note = typeof(buttonDiscount.attr('data-discount-note')) ? buttonDiscount.attr('data-discount-note') : '';
			if(self.data.discount_type == _PERCENT){
				self.data.total_discount = self.data.total_items * self.data.discount_value / 100;
			}else{
				self.data.total_discount = self.data.discount_value;
			}
		}

		var inputShippingFeeCustomer = $('input[name="shipping_fee_customer"]').length > 0 ? $('input[name="shipping_fee_customer"]') : null;
		self.data.shipping_fee_customer = inputShippingFeeCustomer != null ? nhMain.utilities.parseFloat(inputShippingFeeCustomer.val().replaceAll(',', '')) : 0;
		self.data.total = self.data.total_items - self.data.total_discount - self.data.total_coupon + self.data.total_vat + self.data.total_other_service + self.data.shipping_fee_customer;

		if(typeof(nhBillShipping) != _UNDEFINED && $('#cb-confirm-shipping').is(':checked')){
			nhBillShipping.showShippingMethod();
		}
	},
	updateLabelOrder: function(){
		var self = this;

		$(self.idTable + ' tbody').find('tr:not(.no-product)').each(function() {
			var rowItem = $(this);
			var product_item_id = typeof(rowItem.attr('data-product-item-id')) != _UNDEFINED ? nhMain.utilities.parseInt(rowItem.attr('data-product-item-id')) : null;
			var itemData = typeof(self.data.items[product_item_id]) != _UNDEFINED ? self.data.items[product_item_id] : {};

			var totalItem = typeof(itemData.total_item) != _UNDEFINED ? itemData.total_item : 0;
			var totalDiscount = typeof(itemData.total_discount) != _UNDEFINED ? itemData.total_discount : 0;
			var totalVat = typeof(itemData.total_vat) != _UNDEFINED ? itemData.total_vat : 0;

			rowItem.find('[label-total-item]').attr('label-total-item', totalItem).text(nhMain.utilities.parseNumberToTextMoney(totalItem));
			rowItem.find('[label-total-discount-product]').attr('label-total-discount-product', totalDiscount);
			if(totalDiscount > 0){
				rowItem.find('[label-total-discount-product]').text('-' + nhMain.utilities.parseNumberToTextMoney(totalDiscount));	
			}
			
			rowItem.attr('data-total-discount', totalDiscount);
			rowItem.attr('data-total-vat', totalVat);
		});

		$(self.idWrap).find('span[label-number-items]').attr('label-number-items', self.data.number_items).text(nhMain.utilities.parseNumberToTextMoney(self.data.number_items));

		$(self.idWrap).find('span[label-total-items]').attr('label-total-items', self.data.total_items).text(nhMain.utilities.parseNumberToTextMoney(self.data.total_items));
		$(self.idWrap).find('span[label-total-discount]').attr('label-total-discount', self.data.total_discount).text(self.data.total_discount > 0 ? '- ' + nhMain.utilities.parseNumberToTextMoney(self.data.total_discount): self.data.total_discount);
		$(self.idWrap).find('span[label-total-vat]').attr('label-total-vat', self.data.total_vat).text(nhMain.utilities.parseNumberToTextMoney(self.data.total_vat));
		$(self.idWrap).find('span[label-shipping-fee-customer]').attr('label-shipping-fee-customer', self.data.shipping_fee_customer).text(nhMain.utilities.parseNumberToTextMoney(self.data.shipping_fee_customer));
		$(self.idWrap).find('span[label-total]').attr('label-total', self.data.total).text(nhMain.utilities.parseNumberToTextMoney(self.data.total));
	},
	initData: function(){
		var self = this;

		self.data = {
			another_service: '',
			coupon_code: '',
			voucher_code: '',
			voucher_value: 0,

			discount_value: 0,
			discount_type: '',
			discount_note: '',

			total_coupon: 0,
			total_discount: 0,
			total_vat: 0,
			total_other_service: 0,
			shipping_fee_customer: 0,
			total_origin: 0,
			total_items: 0,
			total_discount_items: 0,
			total: 0,

			number_items: 0,

			items: {}
		}
	},
	loadInfoProductToList: function(data = {}){
		var self = this;	

		// check exist item
		var product_item_id = typeof(data.product_item_id) != _UNDEFINED ? data.product_item_id : '';
		if(product_item_id != '' && $(self.idTable).find('tbody tr[data-product-item-id="'+ product_item_id +'"]').length){
			var origin_quantity = $(self.idTable).find('tbody tr[data-product-item-id="'+ product_item_id +'"]').find('input[input-quantity]').attr('input-quantity');
			var quantity = nhMain.utilities.parseInt(origin_quantity) + 1;
			$(self.idTable).find('tbody tr[data-product-item-id="'+ product_item_id +'"]').find('input[input-quantity]').attr('input-quantity', quantity).val(quantity);
			return false;
		}

		// add new item
		self.toggleRowItemNoProduct(false);
		$(self.idTable).find('tbody').prepend(self.template.rowTable);

		var rowItem = $(self.idTable + ' tbody').find('tr').first();
		self.clearInfoRowItem(rowItem);
		
		var product_id = typeof(data.product_id) != _UNDEFINED ? data.product_id : '';
		var code = typeof(data.code) != _UNDEFINED ? data.code : '';
		var name = typeof(data.name) != _UNDEFINED ? data.name : '';
		var nameExtend = typeof(data.name_extend) != _UNDEFINED ? data.name_extend : '';
		var quantity = 1;
		var price = typeof(data.price) != _UNDEFINED ? nhMain.utilities.parseFloat(data.price) : 0;
		var totalItem = quantity * price;
		var priceSpecial = typeof(data.price_special) != _UNDEFINED ? nhMain.utilities.parseFloat(data.price_special) : 0;
		var timeStart = typeof(data.time_start_special) != _UNDEFINED ? data.time_start_special : 0;
		var timeEnd = typeof(data.time_end_special) != _UNDEFINED ? data.time_end_special : 0;
		var applySpecial = typeof(data.apply_special) != _UNDEFINED ? data.apply_special : false;

		var width = typeof(data.width) != _UNDEFINED ? nhMain.utilities.parseFloat(data.width) : 0;
		var length = typeof(data.length) != _UNDEFINED ? nhMain.utilities.parseFloat(data.length) : 0;
		var height = typeof(data.height) != _UNDEFINED ? nhMain.utilities.parseFloat(data.height) : 0;
		var weight = typeof(data.weight) != _UNDEFINED ? nhMain.utilities.parseFloat(data.weight) : 0;

		var width_unit = typeof(data.width_unit) != _UNDEFINED ? data.width_unit : '';
		var length_unit = typeof(data.length_unit) != _UNDEFINED ? data.length_unit : '';
		var height_unit = typeof(data.height_unit) != _UNDEFINED ? data.height_unit : '';
		var weight_unit = typeof(data.weight_unit) != _UNDEFINED ? data.weight_unit : '';


		rowItem.attr('data-id', '');
		rowItem.attr('data-product-id', product_id);
		rowItem.attr('data-product-item-id', product_item_id);
		rowItem.attr('data-price-special', priceSpecial);
		rowItem.attr('data-time-start', timeStart);
		rowItem.attr('data-time-end', timeEnd);

		rowItem.attr('data-width', width);
		rowItem.attr('data-length', length);
		rowItem.attr('data-height', height);
		rowItem.attr('data-weight', weight);

		rowItem.attr('data-width_unit', width_unit);
		rowItem.attr('data-length_unit', length_unit);
		rowItem.attr('data-height_unit', height_unit);
		rowItem.attr('data-weight_unit', weight_unit);

		rowItem.find('span[label-code]').attr('label-code', code).text(code);
		rowItem.find('span[label-name]').attr('label-name', nameExtend).text(nameExtend);
		rowItem.find('span[label-total-item]').attr('label-total-item', totalItem).text(nhMain.utilities.parseNumberToTextMoney(totalItem));

		rowItem.find('input[input-quantity]').attr('input-quantity', quantity).val(quantity);
		rowItem.find('input[input-price]').attr('input-price', price).val(price);
		if(applySpecial){
			rowItem.find('input[input-price]').attr('input-price', priceSpecial).val(priceSpecial);
			// var discount = price - priceSpecial;			
			// rowItem.attr('data-discount-type', _MONEY);
			// rowItem.attr('data-discount', discount);
		}

		rowItem.find('input.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		rowItem.find('input[input-quantity]').focus();
	},
	toggleRowItemNoProduct: function(show = true){
		var self = this;
		if(show){
			$(self.idTable + ' tbody').find('tr.no-product').removeClass('d-none');
		}else{
			$(self.idTable + ' tbody').find('tr.no-product').addClass('d-none');
		}
	},
	clearInfoRowItem: function(rowItem = null){
		rowItem.attr('data-id', '');
		rowItem.attr('data-product-id', '');
		rowItem.attr('data-product-item-id', '');
		rowItem.attr('data-discount-type', '');
		rowItem.attr('data-discount', '');
		rowItem.attr('data-total-discount', '');
		rowItem.attr('data-vat', '');
		rowItem.attr('data-total-vat', '');

		if(typeof(rowItem.find('span[label-code]')) != _UNDEFINED){
			rowItem.find('span[label-code]').attr('label-code', '').text('');
		}

		if(typeof(rowItem.find('span[label-name]')) != _UNDEFINED){
			rowItem.find('span[label-name]').attr('label-name', '').text('');
		}

		if(typeof(rowItem.find('span[label-total-row]')) != _UNDEFINED){
			rowItem.find('span[label-total-row]').attr('label-total-row', '').text('');
		}

		if(typeof(rowItem.find('input[input-quantity]')) != _UNDEFINED){
			rowItem.find('span[input-quantity]').attr('input-quantity', '').val('');
		}

		if(typeof(rowItem.find('input[input-price]')) != _UNDEFINED){
			rowItem.find('span[input-price]').attr('input-price', '').val('');
		}

		rowItem.removeClass('d-none');
	},
	checkClearPromotion: function(){
		var self = this;

		if(typeof(nhPromotion) != _UNDEFINED && typeof(nhPromotion.promotionInfo) != _UNDEFINED && !$.isEmptyObject(nhPromotion.promotionInfo)){
			self.clearPromotion();
			
			nhPromotion.promotionInfo = {};
			nhPromotion.hideInfoPromotion();
		}
	},
	clearPromotion: function(){
		var self = this;
		$(self.idWrap).find('input[name="promotion_id"]').val('');

		var buttonDiscount = $(self.idWrap).find('a[data-discount]');
		buttonDiscount.attr('data-discount', '');
		buttonDiscount.attr('data-discount-type', '');

		$(self.idWrap).find('[label-total-discount]').text(0);

		$(self.idTable + ' tbody').find('tr:not(.no-product)').each(function(index) {

			var discountValue = typeof($(this).attr('data-discount')) != _UNDEFINED ? nhMain.utilities.parseInt($(this).attr('data-discount')) : 0;
			if(!discountValue > 0) return;

			var inputPrice = $(this).find('input[input-price]');
			if(inputPrice.length == 0 ) return;

			var price = nhMain.utilities.parseInt(inputPrice.attr('input-price'));			
			var discountType = typeof($(this).attr('data-discount-type')) != _UNDEFINED ? $(this).attr('data-discount-type') : null;

			var newPrice = 0;
			if(discountType == _PERCENT){
				newPrice = price / (100 - discountValue) * 100;
			}else{
				newPrice = price + discountValue;
			}

			inputPrice.val(newPrice);
			inputPrice.attr('input-price', newPrice);

			$(this).find('[label-total-discount-product]').attr('label-total-discount-product', '').text('');
			$(this).attr('data-discount-type', '');
			$(this).attr('data-total-discount', '');
			$(this).attr('data-discount', '');
		});

		self.parseData();
	}
}