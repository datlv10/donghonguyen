"use strict";

var nhQuickAddCoupon = {
	idWrap: '#wrap-item-coupon',
	classItem: '.wrap-item',
	itemHtml: null,
	init: function(options = {}){
		var formEl = $('#quick-add-coupon-form');	
		var formElRandom = $('#quick-add-coupon-form-random');	

		var self = this;
		self.itemHtml = $(self.idWrap + ' ' + self.classItem + ':first-child').length ? $(self.idWrap + ' ' + self.classItem + ':first-child')[0].outerHTML : '';

		$(self.idWrap).find(self.classItem).each(function(index) {
		  	self.initInputItem($(this));
		});

		self.clearAllErrorItem();

		$(document).on('click', '#add_coupon', function(e) {
			self.addNewItem();
		});

		$(document).on('click', self.idWrap + ' .btn-delete-item', function(e) {
			var item = $(this).closest(self.classItem);
			item.remove();
		});

		$(document).on('click', '.kt-radio--tick', function(e) {
			var val = $(this).find('input').val();

			if (typeof(val) != _UNDEFINED && val == 1) {
				$(this).closest('.form-group').find('.number-use').attr('type', 'text');
				$(this).closest('.form-group').find('.number-use').val('1');
			} else {
				$(this).closest('.form-group').find('.number-use').attr('type', 'hidden');
				$(this).closest('.form-group').find('.number-use').val('0');
			}
		});	

		$.validator.addMethod('regexUser', function(code, element) {
			return code.match(/^[a-zA-Z0-9]+$/);
		}, nhMain.getLabel('ma_coupon_khong_duoc_chua_ky_tu_dac_biet'));

		var validator = formEl.validate({
			ignore: ':hidden',
			rules: {
				code: {
					required: true,
					minlength: 6,
					maxlength: 255,
					regexUser: true
				},
			},
			messages: {
				code: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
            },

            errorPlacement: function(error, element) {
                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error.addClass('invalid-feedback'));
                }else{                	
                    element.after(error.addClass('invalid-feedback'));
                }
            },
			invalidHandler: function(event, validator) {
				KTUtil.scrollTop();
			}
		})

		formEl.on('click', '#quick-add-coupon', function(e){
	  		e.preventDefault();

	  		var validate = true;
	  		$('input', self.classItem).each(function () {
	  			if (!validator.form()){
	  				validate = false;
	  				return;
	  			};
				self.replaceNameInput(this);
			});

	  		if(validate){
	  			nhMain.initSubmitForm(formEl, $(this));	
	  		}
			
		});      

		var validatorRandom = formElRandom.validate({
			ignore: ':hidden',
			rules: {
				total_code: {
					required: true,
					max: 1000,
				},
				length_code: {
					required: true,
					min: 6,
					max: 10
				},
			},
			messages: {
				total_code: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    max: nhMain.getLabel('so_luong_ma_nho_hon_1000')
                },
                length_code: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    min: nhMain.getLabel('so_ky_tu_ngau_nhien_lon_hon_6_ky_tu'),
                    max: nhMain.getLabel('so_ky_tu_ngau_nhien_nho_hon_10_ky_tu')
                },
            },

            errorPlacement: function(error, element) {
                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error.addClass('invalid-feedback'));
                }else{                	
                    element.after(error.addClass('invalid-feedback'));
                }
            },
			invalidHandler: function(event, validator) {
				KTUtil.scrollTop();
			}
		})

		formElRandom.on('click', '#quick-add-coupon-random', function(e){
	  		e.preventDefault();

	  		var validate = true;
	  		$('input', self.classItem).each(function () {
	  			if (!validatorRandom.form()){
	  				validate = false;
	  				return;
	  			};
				self.replaceNameInput(this);
			});

	  		if(validate){
	  			nhMain.initSubmitForm(formElRandom, $(this));	
	  		}
			
		});      
	},
	initInputItem: function(item = null){
		var self = this;
		if(item == null || item.length == 0) return;

		var indexItem = $(self.classItem).index(item[0]);

		// replace value input
		$('input', item).each(function () {
			$(this).val('');
			$(this).attr('data-key', indexItem);
		});
	},
	replaceNameInput: function(item){
		var self = this;
		var indexItem = $(item).attr('data-key');
		var name = $(item).attr('data-name') + '['+ indexItem +']';
		$(item).attr('name', name);
	},
	addNewItem: function(){
		var self = this;

		$(self.idWrap).append(self.itemHtml);
		var item = $(self.idWrap).find(self.classItem + ':last-child');

		self.clearDataItem(item);
		self.initInputItem(item);
	},
	clearDataItem: function(item = null){
		if(item == null || item.length == 0) return;
		var self = this;

		$('input', item).each(function () {
			$(this).val('');
		});
	},
	showErrorItem: function(item = null){
		var self = this;
		if(item == null || item == _UNDEFINED || item.length == 0) return;
		item.addClass('item-error');
		KTUtil.scrollTo(item[0], nhMain.validation.offsetScroll);
	},
	clearAllErrorItem: function(){
		var self = this;
		$(self.idWrap).find(self.classItem).removeClass('item-error');
	}
}

var nhListPromotionCoupon = function() {
	var id = $('input[name*=promotion_id]').val();

	var url = adminPath + '/promotion/coupon/list/json';
	if (typeof id !== _UNDEFINED && id !== '') {
		url = adminPath + '/promotion/coupon/list/json/' + id;
	}

	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: url,
					headers: {
						'X-CSRF-Token': csrfToken
					},
					map: function(raw) {
						var dataSet = raw;
						if (typeof raw.data !== _UNDEFINED) {
							dataSet = raw.data;
						}
						return dataSet;
					},
				},
			},
			pageSize: paginationLimitAdmin,
			serverPaging: true,
			serverFiltering: true,
			serverSorting: true,
		},

		data_filter: {
			lang: nhMain.lang,
			keyword: $('#nh-keyword').val(),
			status: $('#nh-status').val()
		},
		
		layout: {
			scroll: false,
			footer: false,
			class: 'table-hover',
		},

		sortable: true,

		pagination: true,
		extensions: {
			checkbox: true
		},
		search: {
			input: $('#nh-keyword'),
		},

		translate: {
            records: {
                processing: nhMain.getLabel('vui_long_cho') +  ' ...',
                noRecords: nhMain.getLabel('khong_co_ban_ghi_nao'),
            }
        },

		columns: [
			{
				field: 'id',
				title: '',
				width: 18,
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
				autoHide: false,
				sortable: false,
			},
			{
				field: 'code',
				title: nhMain.getLabel('ma_coupon'),
				sortable: false,
				autoHide: false,
				width: 155,
			},
			{
				field: 'used',
				title: nhMain.getLabel('da_su_dung'),
				class: 'text-center',
				sortable: false,
				width: 150,
			},
			{
				field: 'number_use',
				title: nhMain.getLabel('luot_su_dung'),
				class: 'text-center',
				sortable: false,
				width: 150,
				template: function(row) {
					var number_use = nhMain.utilities.notEmpty(row.number_use) ? row.number_use : 0;
					var usages = 0;

					if (number_use > 0) {
						usages = number_use;
					} else {
						usages = '<span class="fs-20">∞</span>'
					}
					return usages;
				}
			},
			{
				field: 'promotion_name',
				title: nhMain.getLabel('ten_chuong_trinh'),
				sortable: false,
				width: 200,
				template: function(row) {
					var promotion_name = nhMain.utilities.notEmpty(row.promotion_name) ? row.promotion_name : '';

					return '<span class="kt-font-bold text-primary">'+ promotion_name +'</span>';
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 110,
				autoHide: false,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.statusPromotionCoupon(row.status);
					}

					return status;

				},
			}]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#promotion_id').on('change', function() {
		      	datatable.search($(this).val(), 'promotion_id');
		    });
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/promotion/coupon/delete',
			    	status: adminPath + '/promotion/coupon/change-status'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();

		    lightbox.option({
              'resizeDuration': 200,
              'wrapAround': true,
              'albumLabel': ' %1 '+ nhMain.getLabel('tren') +' %2'
            });   

            $(document).on('keyup keypress paste focus', '[input-suggest]', function(e) {
            	var wrap = $(this).closest('[promotion-suggest]');
            	var _this = $(this);
				var inputValue = wrap.find('input[name="promotion_id"]');

            	if(_this.length == 0) return;

				if(e.type != 'focusin'){
					$(inputValue).val('');	
				}			

				nhMain.autoSuggest.basic({
					inputSuggest: _this,
					inputValue: inputValue,
					fieldLabel: 'name',
					url: adminPath + '/promotion/auto-suggest'
				}, function(response){	
					if(!$.isEmptyObject(response) && typeof(response.name) != _UNDEFINED){
						_this.val(response.name);
						inputValue.trigger('change');
					}
				});

				if(e.type == 'focusin' && $(this).val() == ''){
					$(this).autocomplete('search', $(this).val());
				}
			});

			$(document).on('change', '[input-suggest]', function(e) {
				if($('#promotion_id').val() == ''){
					$('#promotion_id').trigger('change');
				}				
			});
		}
	};
}();

$(document).ready(function() {
	nhQuickAddCoupon.init();
	nhListPromotionCoupon.listData();
});
