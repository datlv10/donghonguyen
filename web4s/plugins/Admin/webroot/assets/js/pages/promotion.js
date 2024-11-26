"use strict";

var nhPromotion = {
	form: $('#promotion-form'),
	typeDiscount: null,
	init: function(params = {}){
		var self = this;

		if(self.form.length == 0) return false;

		if(self.form.find('#type-discount').val() != _UNDEFINED){
			self.typeDiscount = self.form.find('#type-discount').val();
		}

		self.initInput();
		self.event();
		self.giveProduct.init();
		self.suggestItem.init();
	},
	initInput: function(){
		var self = this;
		
		self.form.find('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		self.form.find('.kt-selectpicker').selectpicker();

        self.form.find('.select-date').datepicker({
				language: 'vi',
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true,
        });
	},
	event: function(){
		var self = this;

		$(document).on('change', 'select#type-discount', function(e) {
			self.typeDiscount = $(this).val();
			self.showValueByTypeDiscount();			
		});

		$(document).on('click', '[nh-value-discount-type]', function(e) {
			var wrap = $(this).closest('.input-group');
			wrap.find('[nh-value-discount-type]').removeClass('active');
			$(this).addClass('active');
			wrap.find('input').val('');
		});

		$(document).on('change', 'input[check-condition]', function(e) {
			var typeCondition = $(this).attr('check-condition');
			$(this).closest('[nh-wrap-condition="'+ typeCondition +'"]').find('[nh-condition-info]').collapse($(this).is(':checked') ? 'show': 'hide');
			self.clearConditions(typeCondition);
		});

		$(document).on('click', '[nh-value-discount-type]', function(e) {
			var discountType = $(this).attr('nh-value-discount-type');
			if(typeof(discountType) == _UNDEFINED || discountType.length == 0) return;

			$(this).closest('.form-group').find('input[input-value-discount-type]').val(discountType);
		});

		$(document).on('change', 'select#type-condition-product', function(e) {
			var wrapConditionProduct = $('[nh-condition-info="product"]');
			
			wrapConditionProduct.find('[nh-item-selected]').html('');
			wrapConditionProduct.find('[nh-wrap-select]').addClass('d-none');

			switch($(this).val()){
				case _PRODUCT:
					$('[nh-wrap-select="'+ _PRODUCT +'"]').removeClass('d-none');
				break;

				case _CATEGORY_PRODUCT:
					$('[nh-wrap-select="'+ _CATEGORY_PRODUCT +'"]').removeClass('d-none');
				break;

				case _BRAND:
					$('[nh-wrap-select="'+ _BRAND +'"]').removeClass('d-none');
				break;
			}
		});

		$(document).on('keyup', '[input-value]', function(e) {
			var wrapElement = $(this).closest('[nh-wrap-value]');
			if(wrapElement.length == 0) return;

			self.getValue(wrapElement.attr('nh-wrap-value'));
		});

		var validatorForm = $('#promotion-form').validate({
			ignore: ':hidden',
			rules: {
				name: {
					required: true,
					maxlength: 255
				},
				type_discount: {
					required: true,
				}
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                type_discount: {
                    required: nhMain.getLabel('vui_long_chon_thong_tin')
                }
            },

            errorPlacement: function(error, element) {
            	var messageRequired = element.attr('message-required');
            	if(typeof(messageRequired) != _UNDEFINED && messageRequired.length > 0){
            		error.text(messageRequired);
            	}
            	error.addClass('invalid-feedback')

                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error);
                }else if(element.hasClass('select2-hidden-accessible')){
            		element.closest('.form-group').append(error);
                }else{
                	element.after(error);
                }
            },

			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			},
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			if (!validatorForm.form()) return;
			if(!self.validate()) return;
			
			nhMain.initSubmitForm($('#promotion-form'), $(this));
		});
	},
	validate: function(){
		var self = this;

		var typeDiscount = $('#type-discount').val();
		if(typeDiscount == _UNDEFINED || typeDiscount.length == 0){
			nhMain.validation.error.show($('#type-discount'), nhMain.getLabel('vui_long_chon_loai_khuyen_mai'));
			return false;
		}

		switch(typeDiscount){
			case 'discount_order':
			case 'discount_product':
				var input = $('[input-value="value_discount"]');
				var valueDiscount = input.val();
				if(valueDiscount == _UNDEFINED || valueDiscount.length == 0){
					nhMain.validation.error.show(input, nhMain.getLabel('vui_long_nhap_gia_tri_chiet_khau'));
					return false;
				}
			break;

			case 'free_ship':		
			break;

			case 'give_product':
				if($.isEmptyObject(self.giveProduct.value)){
					toastr.error(nhMain.getLabel('vui_long_them_dieu_kien'));
					return false;
				}

				$.each(self.giveProduct.value, function( index, item) {
					if(typeof(item.buy) == _UNDEFINED){
						toastr.error(nhMain.getLabel('vui_long_chon_san_pham_khuyen_mai'));
						return false;
					};

					if(typeof(item.give) == _UNDEFINED){
						toastr.error(nhMain.getLabel('vui_long_cau_hinh_dieu_kien_san_pham_duoc_tang'));
						return false;
					};
				});				
			break;
		}

		return true;
	},
	showValueByTypeDiscount: function(){
		var self = this;

		self.form.find('[nh-wrap-value]').addClass('d-none');
		var typeWrap = '';

		var wrapConditionProduct = $('[nh-wrap-condition="product"]');
		wrapConditionProduct.removeClass('d-none');
		switch(self.typeDiscount){
			case 'discount_order':
			case 'discount_product':
				typeWrap = 'discount';
			break;

			case 'free_ship':
				typeWrap = 'free-ship';
			break;

			case 'give_product':
				typeWrap = 'give-product';

				wrapConditionProduct.find('input[check-condition="product"]').prop('checked', false);
				wrapConditionProduct.addClass('d-none');
				self.clearConditions(_PRODUCT);
			break;
		}

		self.form.find('[nh-wrap-value="'+ typeWrap +'"]').removeClass('d-none');
		self.getValue(typeWrap);
	},
	clearConditions: function(type = null){
		var self = this;

		var wrap = $('[nh-wrap-condition="'+ type +'"]');
		if(wrap.length == 0) return;	

		wrap.find('input').val('');
		wrap.find('[nh-item-selected]').html('');
	},
	getValue: function(typeValue = null){
		var self = this;

		$('input#value').val('');
		var wrapElement = $('[nh-wrap-value="' + typeValue + '"]');
		if(wrapElement.length == 0) return;

		var result = {};
		wrapElement.find('input[input-value]').each(function(index) {
		  	var code = $(this).attr('input-value');
		  	if(typeof(code) == _UNDEFINED) return;

		  	if(code == 'give_product'){
		  		result[code] = self.giveProduct.value;
		  	}else{
		  		result[code] = $.trim($(this).val().replaceAll(',', ''));		  		
		  	}
		  	
		});

		$('input#value').val(JSON.stringify(result));
	},
	giveProduct: {
		wrapElement: null,
		htmlItem: null,
		htmlRow: null,
		value: {},
		init: function(){
			var self = this;

			self.wrapElement = $('[nh-wrap-value="give-product"]');
			
			if(self.wrapElement.length == 0) return;
			self.htmlItem = self.wrapElement.find('[nh-give-product-item]').first()[0].outerHTML;
			self.htmlRow = self.wrapElement.find('[nh-give-product-item] tbody tr').first()[0].outerHTML;
			self.wrapElement.find('[nh-give-product-item] tbody tr[init-give-row]').remove();

			if($('#type-discount').val() == 'give_product'){
				var conditionValue = $('input#value').length > 0 ? $.parseJSON($('input#value').val()) : {};
				self.value = typeof(conditionValue.give_product) != _UNDEFINED ? conditionValue.give_product : {};
			}			
			self.event();
			self.chooseProduct.init();
		},
		event: function(){
			var self = this;

			$(document).on('click', '[nh-give-product-action="add-item"]', function(e) {
				var validate = self.validateItem();

				if(typeof(validate.error) != _UNDEFINED && validate.error == true){
					toastr.error(nhMain.getLabel('vui_long_chon_san_pham_khuyen_mai'));
					return false;
				}
				self.addNewItem()
			});

			$(document).on('click', '[nh-give-product-action="buy"], [nh-give-product-action="give"]', function(e) {
				var type = $(this).attr('nh-give-product-action');
				var index = $(this).closest('[nh-give-product-item]').index();

				self.chooseProduct.showModal(index, type);
			});

			$(document).on('click', '[nh-give-product-action="delete"]', function(e) {
				self.deleteItem($(this).closest('[nh-give-product-item]'));
			});
		},
		addNewItem: function(itemData = {}){
			var self = this;
			
			var lastItem = self.wrapElement.find('[nh-give-product-item]').last();
			if(lastItem.length > 0){
				lastItem.after(self.htmlItem);
			}else{
				self.wrapElement.prepend(self.htmlItem);
			}

			var itemElement = self.wrapElement.find('[nh-give-product-item]').last();
			itemElement.find('table[nh-give-product-table] tbody tr').remove();

			if(itemElement.length == 0) return;
			if($.isEmptyObject(itemData)) return;

			$.each(itemData, function(key, items) {
				if($.inArray(key, ['buy', 'give']) == -1) return;
				var table = itemElement.find('table[nh-give-product-table="'+ key +'"]');
				if(table.length == 0) return;
				
				for(var product_item_id in items){
					var item = items[product_item_id];

					table.find('tbody').append(self.htmlRow);
					var row = table.find('tbody tr').last();
					
					var productName = typeof(item.name) != _UNDEFINED ? item.name : '';
					var quantity = typeof(item.quantity) != _UNDEFINED ? nhMain.utilities.parseInt(item.quantity) : 1;

					row.find('[nh-give-product-label="product-name"]').text(productName);
					row.find('[nh-give-product-label="quantity"]').text(nhMain.utilities.parseNumberToTextMoney(quantity));
				}
			});
		},
		deleteItem: function(itemElement){
			var self = this;
			
			if(itemElement.length == 0) return;

			var index = itemElement.index();
			itemElement.remove();

			if(self.wrapElement.find('[nh-give-product-item]').length == 0){
				self.wrapElement.prepend(self.htmlItem);
			}

			if(typeof(self.value[index]) == _UNDEFINED) return;
			delete self.value[index];
		},
		loadValue: function(){
			var self = this;

			self.wrapElement.find('[nh-give-product-item]').remove();		
			$.each(self.value, function(i, item) {
				self.addNewItem(item);
			});
			nhPromotion.getValue('give-product');
		},
		validateItem: function(){
			var self = this;

			var result = {
				error: false,
				index: null
			};

			if($.isEmptyObject(self.value)) return result;

			for(var index in self.value){
				var item = self.value[index];
				if(typeof(item.buy) ==_UNDEFINED){
					result.error = true;
					result.index = nhMain.utilities.parseInt(index);
					return result;
				}

				if(typeof(item.give) ==_UNDEFINED){
					result.error = true;
					result.index = nhMain.utilities.parseInt(index);
					return result;
				}
			}

			return result;
		},
		chooseProduct:{
			modal: null,
			htmlItem: null,
			table: null,
			itemSelected: {},
			init: function(){
				var self = this;

				self.modal = $('#give-product-modal');
				self.table = self.modal.find('table[table-select-product]');
				if(self.modal.length == 0 || self.table.length == 0) return;

				self.htmlItem = self.table.find('tbody tr:first')[0].outerHTML;
				self.event();
			},
			event: function(){
				var self = this;

				self.modal.on('keyup keypress paste focus', 'input[suggest-item="give-product"]', function(e) {
					nhMain.autoSuggest.basic({
						inputSuggest: 'input[suggest-item="give-product"]',
						url: adminPath + '/product/auto-suggest',
						fieldLabel: 'name_extend',
						classes: {
						    'ui-autocomplete': 'suggest-on-modal'
						}
					}, function(response){
						if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED && typeof(response.name_extend) != _UNDEFINED){
							response.name = response.name_extend;
							self.selectItem(response);
						}
					});

					if(e.type == 'focusin'){
						$(this).autocomplete('search', $(this).val());
					}
				});

				self.modal.on('change', '[nh-field="quantity"]', function(e) {
					var quantity = nhMain.utilities.parseInt($(this).val());
					var trElement = $(this).closest('tr');
					var product_item_id = trElement.attr('product-item-select');					
					if(typeof(self.itemSelected[product_item_id]) == _UNDEFINED) return;

					self.itemSelected[product_item_id].quantity = quantity > 0 ? quantity : 1;
				});

				self.modal.on('click', '[nh-action="delete"]', function(e) {
					var trElement = $(this).closest('tr');
					var product_item_id = trElement.attr('product-item-select');

					var trElement = $(this).closest('tr').remove();

					if(typeof(self.itemSelected[product_item_id]) == _UNDEFINED) return;
					delete self.itemSelected[product_item_id];
				});

				self.modal.on('click', '#btn-apply-product', function(e) {
					var index = self.modal.find('table[table-select-product]').attr('table-index');
					var typeTable = self.table.attr('table-select-product');
					if(typeof(index) == _UNDEFINED || index < 0) return;
					if(typeTable == _UNDEFINED) return;
					if(typeof(nhPromotion.giveProduct.value[index]) == _UNDEFINED) nhPromotion.giveProduct.value[index] = {};
					if(typeof(nhPromotion.giveProduct.value[index][typeTable]) == _UNDEFINED) nhPromotion.giveProduct.value[index][typeTable] = {};

					nhPromotion.giveProduct.value[index][typeTable] = self.itemSelected;
					self.modal.modal('hide');
					nhPromotion.giveProduct.loadValue();
				});
			},
			showModal: function(index = null, typeTable = null){
				var self = this;

				if(self.modal.length == 0) return;
				if(index == null || index == _UNDEFINED) index = 0;
				if(index < 0) return;
				
				if(typeTable == null || typeTable == _UNDEFINED) return;
				var table = self.modal.find('table[table-select-product]');
				
				if(table.length == 0) return;
				self.clearModal();				
				
				table.attr('table-index', index);
				table.attr('table-select-product', typeTable);

				var items = typeof(nhPromotion.giveProduct.value[index]) != _UNDEFINED ? nhPromotion.giveProduct.value[index] : [];
				items = typeof(items[typeTable]) != _UNDEFINED ? items[typeTable] : [];

				if(!$.isEmptyObject(items)){
					for(var product_item_id in items){
					    self.selectItem(items[product_item_id]);
					}
				}

				self.modal.modal('show');
			},
			clearModal: function(){
				var self = this;

				if(self.modal.length == 0) return;
				var table = self.modal.find('table[table-select-product]');
				if(table.length == 0) return;

				table.attr('table-select-product', '');
				table.attr('table-index', '');
				table.find('tbody tr').remove();

				self.itemSelected = {};
			},
			selectItem: function(response){
				var self = this;

				if($.isEmptyObject(response)) return;
				if(typeof(response.product_item_id) == _UNDEFINED || !response.product_item_id > 0) return;
				if(typeof(response.name) == _UNDEFINED || response.name == null || response.name.length == 0) return;
				var exist = self.checkItemExist(response.product_item_id);
				if(exist) return;
				
				var quantity = typeof(response.quantity) != _UNDEFINED ? nhMain.utilities.parseInt(response.quantity) : 1;
				self.itemSelected[response.product_item_id] = {
					product_item_id: response.product_item_id,
					name: response.name,
					quantity: quantity
				}

				self.table.find('tbody').append(self.htmlItem);
				var item = self.table.find('tbody tr:last-child');

				item.attr('product-item-select', response.product_item_id);
				item.find('[nh-field="name"]').text(response.name);
				item.find('input[nh-field="quantity"]').val(quantity);
			},
			checkItemExist: function(product_item_id = null){
				var self = this;
				if(product_item_id == null) return false;
				if($.isEmptyObject(self.itemSelected)) return false;

				var exist = false;

				for(var key in self.itemSelected){
				    if(nhMain.utilities.parseInt(key) == product_item_id){
						exist = true;
					};
				}
				return exist;
			},
		}
	},
	suggestItem: {
		listSuggest: [_PRODUCT, _CATEGORY_PRODUCT, _BRAND, _LOCATION],
		wrapElement: null,
		type: null,
		init: function(){
			var self = this;

			self.event();
		},
		event: function(){
			var self = this;

			$(document).on('keyup keypress paste focus', 'input[suggest-item]', function(e) {
				self.type = $(this).attr('suggest-item');
				self.wrapElement = $(this).closest('[nh-wrap-select]');

				if($.inArray(self.type, [_PRODUCT, _CATEGORY_PRODUCT, _BRAND, _LOCATION]) == -1) return;
				if(self.wrapElement.length == 0) return;

				var inputSuggest = 'input[suggest-item="' + self.type + '"]';
				var url = '';
				var fieldLabel = 'name';

				switch(self.type){
					case _PRODUCT:
						url = adminPath + '/product/auto-suggest';
						fieldLabel = 'name_extend';
					break;

					case _CATEGORY_PRODUCT:
						url = adminPath + '/category/product/auto-suggest';
					break;

					case _BRAND:
						url = adminPath + '/brand/auto-suggest';
					break;

					case _LOCATION:
						url = adminPath + '/city/auto-suggest';
					break;
				}
				
				nhMain.autoSuggest.basic({
					inputSuggest: inputSuggest,
					url: url,
					fieldLabel: fieldLabel
				}, function(response){
					if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED && typeof(response[fieldLabel]) != _UNDEFINED){
						self.addItemSelected({
							id: response.id,
							name: response[fieldLabel]
						});
					}
				});
				
				if(e.type == 'focusin'){
					$(this).autocomplete('search', $(this).val());
				}
			});

			$(document).on('click', '[nh-wrap-select] .tagify__tag__removeBtn', function(e) {
				$(this).closest('.tagify__tag').remove();
			});
		},
		addItemSelected: function(item = {}){
			var self = this;
			
			if($.isEmptyObject(item)) return;
			if($.inArray(self.type, [_PRODUCT, _CATEGORY_PRODUCT, _BRAND, _LOCATION]) == -1) return;
			if(self.wrapElement == null || self.wrapElement.length == 0) return;
			if(self.checkItemExist(item.id, self.wrapElement)) return;

			var inputName = '';
			switch(self.type){
				case _PRODUCT:
				case _CATEGORY_PRODUCT:
				case _BRAND:
					inputName = 'condition_product[ids][]';
				break;

				case _LOCATION:
					inputName = 'condition_location[ids][]';
				break;
			}

			var itemHtml = 
			'<span class="tagify__tag">\
	            <x class="tagify__tag__removeBtn" role="button"></x>\
	            <div><span class="tagify__tag-text">' + item.name + '</span></div>\
	            <input name="'+ inputName +'" value="' + item.id + '" type="hidden">\
	        </span>';
			self.wrapElement.find('[nh-item-selected]').append(itemHtml);
		},
		checkItemExist: function(item_id = null){
			var self = this;

			if(item_id == null || !item_id > 0) return false;
			if(self.wrapElement == null || self.wrapElement.length == 0) return;

			if(self.wrapElement.find('input[value="'+ item_id +'"]').length > 0) return true;
			return false;
		}		
	}
};


$(document).ready(function() {
	nhPromotion.init();
});
