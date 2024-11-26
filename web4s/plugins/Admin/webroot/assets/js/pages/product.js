"use strict";

var nhProduct = function () {

	var formEl;
	var validator;
	var listItems;

	var initValidation = function() {

  		nhMain.validation.url.init();

		validator = formEl.validate({
			ignore: ':hidden',
			rules: {
				name: {
					required: true,
					maxlength: 255
				},
				link: {
					required: true,
					maxlength: 255,
					url: true
				},				
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                link: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
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
	}

	var initSubmit = function() {		

		// copy embed attribute
		$(document).on('click', '[nh-embed-attribute]', function(e) {
			var embed = $(this).attr('nh-embed-attribute');
			if(embed.length == 0) return;

			nhMain.nhEvents.copy(embed, function(){
				toastr.success(nhMain.getLabel('da_copy_ma_nhung'));
			});
		});
		
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			if (validator.form()) {
				if(!itemProduct.validateInputOnListItem()){
					return false;
				}

		        var resultScore	= nhSeoAnalysis.getScore();
				$('#seo-score').val(resultScore.seoScore);
				$('#keyword-score').val(resultScore.seoKeywordScore);

				// get content tinymce editor
				$('#description').val(tinymce.get('description').getContent());
				$('#content').val(tinymce.get('content').getContent());

				var itemData = itemProduct.getDataBeforeSubmitForm();

				$('#nh-item-product').val(JSON.stringify(itemData));
				nhMain.initSubmitForm(formEl, $(this));
			}
		});

		$(document).on('click', '.btn-save-draft', function() {
			formEl.find('input[name="draft"]').val(1);
			$('#btn-save').trigger('click');
		});		
	}

	var itemProduct = {
		wrap: $('#products-item-wrap'),
		idSelectAttributeImage: '#select-attribute-image',	
		classItemAttributeImage: '.item-attribute-image',
		idTable: '#table-items',
		attributeHasImageTemplate: '',
		itemTemplate: '',
		data: {},
		dataGenerate: {},
		dataSubmit: {},
		init: function(){
			var self = this;
			self.getHtmlTemplate();

			self.data = self.getData();
			self.dataGenerate = self.getDataGenerate();
			self.initInputForAllItem();
			self.checkDuplicateOnListItem();
			self.disableAttributeOptionSelected();

			nhMain.attributeInput.init({
				wrap: self.wrap,
				accept_init: true
			});

			$(self.idTable).find('tbody').sortable({
                items: 'tr',
                opacity: 1,
                handle : '.sort-item',
                coneHelperSize: true,
                placeholder: 'h-60 bg-grey',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                helper: 'clone',
                tolerance: 'pointer',
                forcePlaceholderSize: !0,
                // cancel: '.kt-portlet--sortable-empty',
                // revert: 250,
                update: function( event, ui ) {
                	$(self.idWrap).find(self.classItem).each(function(index) {
					  	self.replaceNameInput($(this));
					});
                }
            });

			$(document).on('click', '#apply-attribute', function(e) {
				var _attributeSelected = $('#select-attribute-item').val();
				var mainCategoryId = $('#main_category_id').val();

				self.applyAttributes(_attributeSelected, mainCategoryId);				
			});
			
			$(document).on('change', '.special-attribute-select select', function(e) {
				var _thisSelect = $(this);			
				if(_thisSelect.closest('#select-attribute-image').length > 0){
					// check this option selected exist
					if(!$.isEmptyObject(self.data)){
						var attributeSelected = $(this).val();
						var attributeCode = $(this).attr('id');
						var check = false;

						$.each(self.data, function(code, list) {
						  	if(code == attributeCode){
						  		$.each(list, function(index, item) {									
									if(attributeSelected == item.value){
								  		check = true;
								  	}
								});
						  	}
						});

						if(check){
							toastr.error(nhMain.getLabel('gia_tri_bi_thuoc_tinh_nay_da_duoc_chon_vui_long_chon_gia_tri_khac'));
							_thisSelect.selectpicker('val', '');
							return false;
						}
					}

					self.newAttributeHasImage($(this).val());					
					_thisSelect.selectpicker('val', '');
				}

				self.data = self.getData();
				self.dataGenerate = self.getDataGenerate();
				self.generateListItem();

				self.disableAttributeOptionSelected();
			});

			$(document).on('click', '.delete-attribute-image', function(e) {
				$(this).closest('.item-attribute-image').remove();

				self.data = self.getData();
				self.dataGenerate = self.getDataGenerate();

				self.generateListItem();
			});

			$(document).on('click', '.collapse-attribute-image', function(e) {
				var itemAttribute = $(this).closest('.item-attribute-image');
				var wrapAlbum = itemAttribute.find('.wrap-album');
				var arrowIcon = $(this).find('i');

			 	if(wrapAlbum.hasClass('show')){						
					arrowIcon.removeClass('fa-angle-double-down').addClass('fa-angle-double-right');
				}else{
					arrowIcon.removeClass('fa-angle-double-right').addClass('fa-angle-double-down');
				}

				wrapAlbum.collapse('toggle');
			});

			$(document).on('click', '#change-attribute', function(e) {
				$('#wrap-select-attribute').collapse('show');
				$(this).remove();
			});

			$(document).on('click', '.delete-item', function(e) {
				var row = $(this).closest('tr');
				if($(self.idTable + ' tbody tr').length > 1){
					row.remove();
				}else{
					row.find('input, select').val('');
				}
			});

			$(document).on('click', '#add-new-item', function(e) {
				$(self.idTable + ' tbody').append(self.itemTemplate);
				var rowItem = $(self.idTable + ' tbody').find('tr').last();

				self.clearValueInputItem(rowItem);
				self.reloadSelectOptionOfAttributeOnListItem(rowItem);				
				self.initInputItem(rowItem);

				var checkExist = self.checkDuplicateOnListItem();
				if(checkExist){
					toastr.error(nhMain.getLabel('phien_ban_san_pham_bi_trung_lap_thuoc_tinh_vui_long_dieu_chinh_lai'));
				}
			});

			$(document).on('change', self.idTable + ' tbody tr select.special-attribute', function(e) {
				var rowItem = $(this).closest('tr');

				var attributeSelected = [];
				rowItem.find('select.special-attribute').each(function(index) {
					attributeSelected.push($(this).attr('id'));
					attributeSelected.push($(this).val());
				});
				rowItem.attr('data-attribute', attributeSelected.join('_'));

				var checkExist = self.checkDuplicateOnListItem();
				if(checkExist){
					toastr.error(nhMain.getLabel('phien_ban_san_pham_bi_trung_lap_thuoc_tinh_vui_long_dieu_chinh_lai'));
				}
			});			

			$(document).on('click', self.idTable + ' .change-price-special', function(e) {
				var _this = $(this);
				_this.popover({
	        		title: nhMain.getLabel('gia_khuyen_mai'),
	    			placement: 'right',
	    			html: true,
	    			sanitize: false,
	    			trigger: 'manual',
		            content: $('#popover-price-special').html(),
		           	template: '\
			            <div class="popover lg-popover" role="tooltip">\
			                <div class="arrow"></div>\
			                <h3 class="popover-header"></h3>\
			                <div class="popover-body"></div>\
			            </div>'
		        });

				var priceSpecial = $(this).closest('td').find('input[name="item_price_special[]"]').val();
				var dateSpecial = $(this).closest('td').find('input[name="item_date_special[]"]').val();

		        _this.popover('show');
		        _this.on('shown.bs.popover', function (e) {		        	
		        	var idPopover = _this.attr('aria-describedby');
		        	var _popover = $('#' + idPopover);

		        	_popover.find('#price-special').val(priceSpecial);
		        	_popover.find('#date-special').val(dateSpecial);

		        	_popover.find('input.number-input').each(function() {
						nhMain.input.inputMask.init($(this), 'number');
					});

		        	_popover.find('input.date-ranger-picker').each(function() {
						nhMain.input.dateRangerPicker($(this), {timePicker: true});
					});
				})
			});

			$(document).on('click', '#confirm-special-price', function(e) {
				var _popover = $(this).closest('.popover.lg-popover');
				var idPopover = _popover.attr('id');
				var btnPopover = $('.change-price-special[aria-describedby="'+ idPopover +'"]');
				var priceSpecial = _popover.find('#price-special').val();
				var dateSpecial = _popover.find('#date-special').val();

				btnPopover.closest('td').find('input[name="item_price_special[]"]').val(priceSpecial);
				btnPopover.closest('td').find('input[name="item_date_special[]"]').val(dateSpecial);
				btnPopover.closest('td').find('.price-special').text(priceSpecial);

				btnPopover.popover('dispose');
			});

			$(document).on('click', '#cancel-special-price', function(e) {
				var idPopover = $(this).closest('.popover.lg-popover').attr('id');
				var btnPopover = $('.change-price-special[aria-describedby="'+ idPopover +'"]');
				btnPopover.popover('dispose');
			});

			$(document).on('click', '#delete-special-price', function(e) {
				var _popover = $(this).closest('.popover.lg-popover');
				_popover.find('#date-special').val('');
			});

			$(document).on('click', '.copy-item, .copy-quantity, .copy-price', function(e) {
				var firstRow = $(self.idTable + ' tbody tr').first();
				if(!firstRow.length > 0){
					return false;
				}

				var copy = 'item';
				if($(this).hasClass('copy-quantity')){
					copy = 'quantity';
				}

				if($(this).hasClass('copy-price')){
					copy = 'price';
				}

				var _data = {
					code: firstRow.find('input[name="item_code[]"]').val(),
					price: firstRow.find('input[name="item_price[]"]').val(),
					quantityAvailable: firstRow.find('input[name="item_quantity_available[]"]').val(),
					priceSpecial: firstRow.find('input[name="item_price_special[]"]').val(),
					dateSpecial: firstRow.find('input[name="item_date_special[]"]').val()
				}

				$(self.idTable + ' tbody').find('tr').each(function(index) {
					if(index == 0){
						return;
					}

					var _tr = $(this);

					switch(copy){
						case 'quantity':
							_tr.find('input[name="item_quantity_available[]"]').val(_data.quantityAvailable);
						break;

						case 'price':
							_tr.find('input[name="item_price[]"]').val(_data.price);
							_tr.find('input[name="item_price_special[]"]').val(_data.priceSpecial);
							_tr.find('.price-special').text(_data.priceSpecial);
							_tr.find('input[name="item_date_special[]"]').val(_data.dateSpecial);
						break;

						case 'item':
						default:
							_tr.find('input[name="item_code[]"]').val(_data.code);
							_tr.find('input[name="item_price[]"]').val(_data.price);
							_tr.find('input[name="item_price_special[]"]').val(_data.priceSpecial);
							_tr.find('.price-special').text(_data.priceSpecial);
							_tr.find('input[name="item_date_special[]"]').val(_data.dateSpecial);
							_tr.find('input[name="item_quantity_available[]"]').val(_data.quantityAvailable);
							
						break;
					}
					
				});
			});

			$(document).on('click', '.clear-item', function(e) {
				$(self.idTable + ' tbody').find('tr').each(function(index) {
					var _tr = $(this);
					_tr.find('input[name="item_code[]"]').val('');
					_tr.find('input[name="item_price[]"]').val('');
					_tr.find('input[name="item_price_special[]"]').val('');
					_tr.find('.price-special').text('');
					_tr.find('input[name="item_date_special[]"]').val('');
					_tr.find('input[name="item_quantity_available[]"]').val('');
				});
			});
		},
		applyAttributes: function(_attributeSelected = null, main_category_id = null){
			var self = this;

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
				url: adminPath + '/product/item/select-attribute-special',
				dataType: 'html',
				data:{
					attribute_selected: _attributeSelected,
					main_category_id: main_category_id
				}
			}).done(function(response) {
				self.wrap.html(response);
				self.getHtmlTemplate();
				self.wrap.find('.item-attribute-image').remove();

				self.data = self.getData();
				self.dataGenerate = self.getDataGenerate();
				self.initInputForAllItem();
				
				self.wrap.find('.kt-select2').select2({
					width: '100%'
				});
				self.wrap.find('.kt-selectpicker').selectpicker({'noneSelectedText': ''});

				// self.wrap.find('.btn-select-image-album').fancybox({
				//    	closeExisting: true,
				//    	iframe : {
				//    		preload : false
				//    	}
				// });

				nhMain.attributeInput.init({
					wrap: self.wrap,
					accept_init: true
				});

				KTApp.unblockPage();
			})
		},
		getHtmlTemplate: function(){
			var self = this;

			if(self.wrap.find(self.classItemAttributeImage).length){
				self.attributeHasImageTemplate = self.wrap.find(self.classItemAttributeImage).first().clone()[0].outerHTML;	
			}
			self.itemTemplate = $(self.idTable + ' tbody tr').first().clone()[0].outerHTML;
		},
		newAttributeHasImage: function(value = null){
			var self = this;

			var attributeHasImage = $(self.attributeHasImageTemplate);
			var attributeName = attributeHasImage.find('select').attr('name');
			var fieldIdAlbum = attributeName + '_' + value;
			var src = attributeHasImage.find('.btn-select-image-album').data('src');
			var srcSplit = src.split('field_id=');
			var srcNew = srcSplit[0] + 'field_id=' + fieldIdAlbum;

			attributeHasImage.find('.btn-select-image-album').attr('data-src', srcNew);
			attributeHasImage.find('.list-image-album').find('input').attr('name', 'images[' + fieldIdAlbum + ']');
			attributeHasImage.find('.list-image-album').find('input').attr('id', fieldIdAlbum);
			attributeHasImage.find('.list-image-album').find('input').attr('value', '');
			attributeHasImage.find('.list-image-album').find('.item-image-album').remove();

			$(self.idSelectAttributeImage).closest('.form-group').before(attributeHasImage[0].outerHTML);

			var newAttribute = self.wrap.find(self.classItemAttributeImage).last();
			newAttribute.find('select').val(value).attr('disabled', 'disabled');

			// newAttribute.find('.btn-select-image-album').fancybox({
			//    	closeExisting: true,
			//    	iframe : {
			//    		preload : false
			//    	}
			// });

			newAttribute.find('.kt-selectpicker').selectpicker();
		},
		getData: function(){
			var self = this;
			var data = {};
			self.wrap.find('select[data-type="special"]').each(function(index) {
				var code = $(this).attr('id');
				var value = $(this).val();				
				var text = $(this).find('option:selected').text();
				if(typeof(data[code]) == _UNDEFINED){
					data[code] = [];
				}

				if($.isArray(value)){
					text = $(this).find('option:selected').toArray().map(item => item.text).join();
					var listText = text.split(',');
					$.each(value, function(index, value) {
						if(value.length != 0){
							data[code].push({
								value: value,
								text: typeof(listText[index]) ? listText[index] : ''
							});
						}
					});
				}else if(value != null && value != ''){
					data[code].push({
						value: value,
						text: text
					});
				}				
			});

			return data;
		},
		getDataGenerate: function(){
			var self = this;
			var dataGenerate = {};

			var dataFormat = [];
			var numberAttribute = Object.keys(self.data).length;
			$.each(self.data, function(code, list) {
				$.each(list, function(index, item) {
			  		dataFormat.push(code + '_' + item.value);
				});
			});

			var data = self.getDataCombination(dataFormat);

			var listCombination = [];
			if(!$.isEmptyObject(data)){
				$.each(data, function(index, item) {
					if(item.length == numberAttribute){
						listCombination.push(item);
					}
				});
			}

			if(listCombination.length > 0){
				$.each(listCombination, function(index, listItem) {
					var item = {};
					var keyItem = [];
					$.each(listItem, function(index, codeCombine) {
						var itemCombine = codeCombine.split('_');
						var code = typeof itemCombine[0] != _UNDEFINED ? itemCombine[0] : null;
						var value = typeof itemCombine[1] != _UNDEFINED ? itemCombine[1] : null;
						if(typeof itemCombine[code] != _UNDEFINED){
							return;
						}
						item[code] = value;
						keyItem.push(codeCombine);
					});

					if(Object.keys(item).length == numberAttribute){
						dataGenerate[keyItem.join('_')] = item;
					}
				});
			}

			return dataGenerate;
		},
		getDataCombination: function(set) {
		  	return (function acc(xs, set) {
			    var x = xs[0];
			    if(typeof x === _UNDEFINED)
			        return set;
			    for(var i = 0, l = set.length; i < l; ++i)
			    	set.push(set[i].concat(x));
			    return acc(xs.slice(1), set);
		  	})(set, [[]]).slice(1);
		},
		generateListItem: function(){
			var self = this;
			if(!$.isEmptyObject(self.dataGenerate)){
				// remove item not in dataGenerate
				$(self.idTable + ' tbody').find('tr[data-attribute]').each(function(index) {
					var attributeCode = $(this).data('attribute');
					if(typeof(self.dataGenerate[attributeCode]) == _UNDEFINED){
						$(this).remove();
					}
				});

				// generate item html
				$.each(self.dataGenerate, function(codeItemGenerate, itemGenerate) {
					if($(self.idTable + ' tr[data-attribute="'+ codeItemGenerate +'"]').length == 0){
						$(self.idTable + ' tbody').append(self.itemTemplate);
						var rowItem = $(self.idTable + ' tbody').find('tr').last();
						self.clearValueInputItem(rowItem);
						rowItem.attr('data-attribute', codeItemGenerate);

						self.reloadSelectOptionOfAttributeOnListItem(rowItem);
						self.setValueForSelectOptionOfAttributeOnListItem(codeItemGenerate);

						self.initInputItem(rowItem);
						nhMain.attributeInput.init({
							wrap: rowItem,
							accept_init: true
						});
					}					
				});
				
			}else{
				$(self.idTable + ' tbody').find('tr').remove();
				$(self.idTable + ' tbody').append(self.itemTemplate);
				
				self.reloadSelectOptionOfAttributeOnListItem();

				var rowItem = $(self.idTable + ' tbody').find('tr').last();
				self.initInputItem(rowItem);

				nhMain.attributeInput.init({
					wrap: rowItem,
					accept_init: true
				});
			}
		},
		reloadSelectOptionOfAttributeOnListItem: function(rowItem = null){
			var self = this;
			var listOptions = {};

			$.each(self.data, function(code, list) {
				var options = '';
				$.each(list, function(index, item) {
					options += '<option value="'+ item.value +'">'+ item.text +'</option>'
				});

				listOptions[code] = options;
			});


			if(!$.isEmptyObject(rowItem)){
				$.each(self.data, function(code, list) {
					rowItem.find('select[id="'+ code +'"]').each(function() {
						$(this).empty().append(listOptions[code]);
					});
				});
			}else{
				$(self.idTable + ' tbody').find('tr').each(function() {
					var rowItem = $(this);
					$.each(self.data, function(code, list) {
						rowItem.find('select[id="'+ code +'"]').each(function() {
							$(this).empty().append(listOptions[code]);
						});
					});
				});
			}		
		},
		setValueForSelectOptionOfAttributeOnListItem: function(codeItemGenerate = null){
			var self = this;

			if(codeItemGenerate != null){
				var rowItem = $(self.idTable + ' tr[data-attribute="'+ codeItemGenerate +'"]');
				var itemGenerate = self.dataGenerate[codeItemGenerate];

				$.each(itemGenerate, function(codeGenerate, valueGenerate) {
					rowItem.find('select[id="'+ codeGenerate +'"]').val(valueGenerate);
				});

			}else{
				$.each(self.dataGenerate, function(attributeCode, itemGenerate) {
					var rowItem = $(self.idTable + ' tr[data-attribute="'+ attributeCode +'"]');

					$.each(itemGenerate, function(codeGenerate, valueGenerate) {
						rowItem.find('select[id="'+ codeGenerate +'"]').val(valueGenerate);
					});
				});
			}			
		},
		clearValueInputItem: function(rowItem = null){
			if(!$.isEmptyObject(rowItem)){
				rowItem.attr('data-id', '');
				rowItem.attr('data-attribute', '')
				rowItem.find('input').val('');
				// rowItem.find('select').empty();
			}
		},
		clearValueInputOnListItem: function(){
			var self = this;

			$(self.idTable + ' tbody').find('tr').each(function() {
				var rowItem = $(this);
				self.clearValueInputItem(rowItem);
			});
		},
		checkDuplicateOnListItem: function(){
			var self = this;
			var dataCheck = [];
			var check = false;
			if($.isEmptyObject(self.data)){
				return;
			}

			$(self.idTable + ' tbody').find('tr').each(function() {
				var rowItem = $(this);
				var keyItem = [];
				$.each(self.data, function(code, list) {
					rowItem.find('select[id="'+ code +'"]').each(function() {
						var value = $(this).val() != null ? $(this).val() : '';
						keyItem.push(code);
						keyItem.push(value);
					});
				});
				var keyCheck = keyItem.join('_');
				if($.inArray(keyCheck, dataCheck) > -1){
					$(self.idTable + ' tbody').find('tr[data-attribute="'+ keyCheck +'"] .bootstrap-select').each(function() {
						$(this).addClass('is-invalid');
						check = true;
					});
				}else{
					dataCheck.push(keyCheck);
					$(self.idTable + ' tbody').find('tr[data-attribute="'+ keyCheck +'"] .bootstrap-select').each(function() {
						$(this).removeClass('is-invalid');
					});
				}
			});

			return check;
		},
		validateInputOnListItem: function(){
			var self = this;

			$(self.idTable + ' tbody').find('input').removeClass('is-invalid');

			var result = true;
			var checkDulicate = self.checkDuplicateOnListItem();
			if(checkDulicate){
				result = false;
				toastr.error(nhMain.getLabel('phien_ban_san_pham_bi_trung_lap_thuoc_tinh_vui_long_dieu_chinh_lai'));				
				return false;
			}

			var listCode = [];
			$(self.idTable + ' tbody').find('tr').each(function() {
				var rowItem = $(this);

				var code = $(this).find('input[name="item_code[]"]').val();
				if(code.length > 0){
					if($.inArray(code, listCode) >= 0){
						result = false;
						rowItem.find('input[name="item_code[]"]').addClass('is-invalid');
						toastr.error(nhMain.getLabel('ma_phien_ban_san_pham_khong_the_trung_nhau_vui_long_dieu_chinh_lai'));
						return false;
					}
					listCode.push(code);
				}
				

				var price = $(this).find('input[name="item_price[]"]').val().replaceAll(',', '');
				var price_special = $(this).find('input[name="item_price_special[]"]').val().replaceAll(',', '');

				if(parseFloat(price) > 0 && parseFloat(price) <= parseFloat(price_special)){
					result = false;
					rowItem.find('input[name="item_price[]"]').addClass('is-invalid');
					toastr.error(nhMain.getLabel('phien_ban_san_pham_bi_trung_lap_thuoc_tinh_vui_long_dieu_chinh_lai'));
					return false;
				}
			});

			return result;
		},
		initInputItem: function(rowItem = null){

			rowItem.find('input.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			rowItem.find('.kt-selectpicker').selectpicker({
				'noneSelectedText': ''
			});
		},
		initInputForAllItem: function(){
			var self = this;

			$(self.idTable + ' tbody').find('tr').each(function() {
				self.initInputItem($(this));
			});
		},
		disableAttributeOptionSelected: function(){
			var self = this;
			var element = $(self.idSelectAttributeImage).find('select');

			if(element.length && !$.isEmptyObject(self.data)){
				var code = element.attr('id');				
				var list = self.data[code];
				element.find('option').each(function() {
					var _option = $(this);
					var value = _option.attr('value');
					$.each(list, function(index, item) {
						if(item.value == value){
							_option.attr('disabled', 'disabled');
						}
					});
				});
				element.selectpicker('refresh');
			}
		},
		getDataBeforeSubmitForm: function(){
			var self = this;
			var result = [];
			var images = {};

			self.wrap.find('.list-image-album input').each(function() {
				var idInput = $(this).attr('id');
				var valueInput = $(this).val();
				if($(this).val().length > 0){
					images[idInput] = valueInput;
				}
			});

			$(self.idTable + ' tbody').find('tr').each(function() {
				var rowItem = $(this);
				var attribute = [];
				var image = typeof(images.album) != _UNDEFINED ? JSON.parse(images.album) : '';

				if(rowItem.find('select.special-attribute').length > 0){
					rowItem.find('select.special-attribute').each(function() {
						var code = $(this).attr('id');
						var value = $(this).val();
						attribute.push({
							attribute_code: code,
							value: value
						});

						if(typeof(images[code + '_' + value]) != _UNDEFINED){
							image = JSON.parse(images[code + '_' + value]);
						}
					});
				}

				if(rowItem.find('.item-attribute').length > 0){
					rowItem.find('.item-attribute').each(function() {
						var code = $(this).attr('id');
						var value = null;						

						if($(this).is('input:radio') && !$(this).is(':checked')) return true;
						value = $(this).val();

						attribute.push({
							attribute_code: code,
							value: value
						});
					});
				}

				var item = {
					id: typeof(rowItem.data('id')) != _UNDEFINED ? rowItem.data('id') : '',
					code: typeof(rowItem.find('input[name="item_code[]"]')) != _UNDEFINED ? rowItem.find('input[name="item_code[]"]').val() : '',
					// status: rowItem.find('input[name="item_status[]"]').is(':checked') ? 1 : 0,
					status: 1,
					price: typeof(rowItem.find('item_code[name="item_price[]"]')) != _UNDEFINED ? rowItem.find('input[name="item_price[]"]').val() : '',
					price_special: typeof(rowItem.find('input[name="item_price_special[]"]')) != _UNDEFINED ? rowItem.find('input[name="item_price_special[]"]').val() : '',
					date_special: typeof(rowItem.find('input[name="item_date_special[]"]')) != _UNDEFINED ? rowItem.find('input[name="item_date_special[]"]').val() : '',
					quantity_available: typeof(rowItem.find('input[name="item_quantity_available[]"]')) != _UNDEFINED ? rowItem.find('input[name="item_quantity_available[]"]').val() : '',
					attribute: attribute,
					image: image
				}

				result.push(item);
			});

			return result;
		}
	}

	var brandByCategory = {
		mainCategoryInput: $('#main_category_id'),
		brandInput: $('#brand_id'),
		init: function(){
			var self = this;

			if(self.mainCategoryInput.length == 0 || self.brandInput.length == 0 ) return;

			var apply = self.mainCategoryInput.attr('nh-brand-by-category');
			if(typeof(apply) == _UNDEFINED || apply == 0 || !nhMain.utilities.parseInt(apply) > 0) return;

			
			var old_category_id = self.mainCategoryInput.val();
			self.mainCategoryInput.on('refreshed.bs.select changed.bs.select', function(e) {
				if(e.type == 'refreshed' && old_category_id == $(this).val()) return;
				old_category_id = $(this).val();

				self.loadBrand(this.value);
			});
		},
		loadBrand: function(category_id = null){
			var self = this;

			self.brandInput.find('option:not([value=""])').remove();
			self.brandInput.selectpicker('refresh');

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
	    		async: false,
				url: adminPath + '/product/load-brand-by-category',
				data: {
					category_id: category_id
				}
			}).done(function(response) {
				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	KTApp.unblockPage();
	        	if (code == _SUCCESS) {
                    if (!$.isEmptyObject(data)) {
                    	var listOption = '';
				        $.each(data, function (id, name) {
				            listOption += '<option value="' + id + '">' + name + '</option>';
				        });
				        self.brandInput.append(listOption);
				        self.brandInput.selectpicker('refresh');
                    }		                    
	            } else {
	            	toastr.error(message);
	            }
			});

		}
	}

	var attributeByCategory = {
		wrapElement: $('#attributes-product'),
		mainCategoryInput: $('#main_category_id'),
		init: function(){
			var self = this;

			if(self.wrapElement.length == 0 || self.mainCategoryInput.length == 0) return;

			var apply = self.mainCategoryInput.attr('nh-attribute-by-category');
			if(typeof(apply) == _UNDEFINED || apply == 0 || !nhMain.utilities.parseInt(apply) > 0) return;

			self.events();
		},
		events: function(){
			var self = this;

			var old_category_id = self.mainCategoryInput.val();
			self.mainCategoryInput.on('refreshed.bs.select changed.bs.select', function(e) {
				
				if(e.type == 'refreshed' && old_category_id == $(this).val()) return;
				old_category_id = $(this).val();
				
				self.loadAttributeProduct(this.value);
				self.loadAttributeProductItem(this.value);
				self.loadSpecialAttributeProductItem(this.value);				
			});	
		},
		loadAttributeProduct: function(category_id = null){
			var self = this;

			if(category_id == null || typeof(category_id) == _UNDEFINED || !category_id > 0) return;

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
	    		dataType: 'html',
				url: adminPath + '/product/load-attribute-by-category',
				data: {
					category_id: category_id
				}
			}).done(function(response) {

				self.wrapElement.html(response);
				nhMain.attributeInput.init();

	        	KTApp.unblockPage();
	        	
			});
		},
		loadAttributeProductItem: function(category_id = null){
			var self = this;

			if(category_id == null || typeof(category_id) == _UNDEFINED || !category_id > 0) return;
			if(typeof(itemProduct.wrap) == _UNDEFINED || itemProduct.wrap.length == 0) return;

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
	    		dataType: 'html',
				url: adminPath + '/product/load-attribute-item-by-category',
				data: {
					category_id: category_id
				}
			}).done(function(response) {
				itemProduct.wrap.html(response);
	        	itemProduct.initInputForAllItem();

	        	KTApp.unblockPage();
	        	
			});
		},
		loadSpecialAttributeProductItem: function(category_id = null){
			var self = this;

			if(category_id == null || typeof(category_id) == _UNDEFINED || !category_id > 0) return;
			if($('#wrap-change-item-attribute').length == 0) return;

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
	    		dataType: 'html',
				url: adminPath + '/product/load-special-attribute-item-by-category',
				data: {
					category_id: category_id
				}
			}).done(function(response) {
				$('#wrap-change-item-attribute').html(response);

				$('.kt-select2').select2({
		            width: '100%'
		        });

				// nếu đang thêm mới sản phẩm thì clear lại attributes phiên bản
				if($('input[name="product_id"]').val() == ''){
					itemProduct.applyAttributes();
				}		        
	        	KTApp.unblockPage();
	        	
			});
		}
	}

	return {
		init: function() {
			formEl = $('#main-form');
			initValidation();
			initSubmit();

			itemProduct.init();

			nhMain.mainCategory.init({
            	wrapCategory: ['#wrap-category']
			});

			brandByCategory.init();
			attributeByCategory.init();

			nhMain.tagSuggest.init();
					
			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			nhMain.input.touchSpin.init($('input[name="position"]'), {
				prefix: '<i class="la la-sort-amount-desc"></i>',
				max: 9999999999,
				step: 1
			});			

			nhMain.selectMedia.album.init();
			nhMain.selectMedia.video.init({
				input: $('#url_video')
			});
			nhMain.selectMedia.file.init();

			$('.kt-selectpicker').selectpicker();

			$('.kt-select2').select2({
	            placeholder: nhMain.getLabel('chon_thuoc_tinh'),
	            width: '100%'
	        });	

			$('.kt-select-multiple').select2();

			nhMain.attributeInput.init();
			
			nhMain.tinyMce.simple();
			nhMain.tinyMce.full({
	            keyup:function (a) {
	                nhSeoAnalysis.getContentWhenKeyUpTinyMCE(a);
	            }
	        });

            nhSeoAnalysis.init();
		}
	};
}();


$(document).ready(function() {
	nhProduct.init();
});
