"use strict";

var nhBillSupplier = {
	wrap: {
		searchId: '#supplier-search',
		infoId: '#supplier-info'
	},
	attributeLabel:{
		name: 'label-customer-name',
		address_name: 'label-supplier-address-name',
		phone: 'label-supplier-phone',
		address: 'label-supplier-address'
	},
	address: {
		idBtnChange: '#supplier-change-address',
		idBtnAdd: '#add-address',
		idModal: '#add-address-modal',
		idWrapList: '#warp-list-address',
		idForm: '#add-address-form',
		idBtnSave: '#save-address-btn',
		classBtnEdit: '.edit-address'
	},
	formEl: null,
	validatorAddress: null,
	init: function(formEl){
		
		var self = this;
		self.formEl = formEl;

		nhMain.validation.phoneVn();
		var validatorAddress = $(self.address.idForm).validate({
			rules: {
				name: {
					required: true,
					minlength: 6,
					maxlength: 255
				},
				phone: {
					required: true,
					phoneVN: true
				}				
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                phone: {
                	required: nhMain.getLabel('vui_long_nhap_thong_tin')
                }
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
				KTUtil.scrollTo(validatorAddress.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});

		$(document).on('keyup keypress paste focus', 'input#supplier-suggest', function(e) {
			nhMain.autoSuggest.basic({
				inputSuggest: 'input#supplier-suggest',
				inputValue: 'input[name="supplier_id"]',
				url: adminPath + '/supplier/auto-suggest',
				itemMore: {
					label: nhMain.getLabel('them_moi_nha_cung_cap'),
					value: 'add_supplier'
				}
			}, function(response){	
				if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED){
					if(response.id == 'add_supplier'){
						$('#quick-add-supplier-modal').modal('show');
						return false;	
					}
					self.showInfo(response);
				}
			});

			if(e.type == 'focusin'){
				$(this).autocomplete('search', $(this).val());
			}
		});

		$(document).on('click', '#supplier-remove', function(e) {
			self.clearInfo();
			self.showSearch();
		});

		$(document).on('click', self.address.idBtnChange, function(e) {
			var _this = $(this);

			$(this).popover('dispose');

			var supplierId = formEl.find('input[name="supplier_id"]').val();
			var listAddressHtml = '';
			nhMain.callAjax({
	    		async: false,
	    		dataType: 'html',
				url: adminPath + '/supplier/addresses/list/' + supplierId
			}).done(function(response) {
				listAddressHtml = response;
			});

			_this.popover({
    			placement: 'bottom',
    			html: true,
    			sanitize: false,
    			trigger: 'manual',
	            content: listAddressHtml,
	           	template: '\
		            <div class="popover lg-popover" role="tooltip">\
		                <div class="arrow"></div>\
		                <div class="popover-body p-10"></div>\
		            </div>'
	        });
	        _this.popover('show');

	        var _popover = $('#' + $(this).attr('aria-describedby'));

	        var scrollEL = _popover.find('.kt-scroll');
        	KTUtil.scrollInit(scrollEL[0], {
                mobileNativeScroll: true,
                handleWindowResize: true,
                rememberPosition: (scrollEL.data('remember-position') == 'true' ? true : false),
                height: function() {
                    if (KTUtil.isInResponsiveRange('tablet-and-mobile') && scrollEL.data('mobile-height')) {
                        return scrollEL.data('mobile-height');
                    } else {
                        return scrollEL.data('height');
                    }
                }
            });
		});

		$(document).on('click', self.address.idWrapList + ' [data-address]', function(e) {
			var dataAddress = typeof($(this).data('address')) != _UNDEFINED ? $(this).data('address') : {};			
			self.showInfo(dataAddress, true);

			$(self.address.idBtnChange).popover('hide');
		});

		$(document).on('click', self.address.idBtnAdd, function(e) {
			$(self.address.idBtnChange).popover('hide');
			$(self.address.idBtnChange).popover('dispose');
			$(self.address.idModal).modal('show');

			self.clearInputAddressModal();
		});

		$(self.address.idModal).on('shown.bs.modal', function (e) {
		  	var supplierId = formEl.find('input[name="supplier_id"]').val();		  	
		  	$(self.address.idForm).attr('action', adminPath + '/supplier/save-address/' + supplierId);
		})

		$(document).on('click', self.address.idBtnSave, function(e) {
			e.preventDefault();
			var _form = $(self.address.idForm);
			if(validatorAddress.form()){
				KTApp.blockPage(blockOptions);

				nhMain.callAjax({
					url: _form.attr('action'),
					data: new FormData(_form[0]),
					processData: false,
					contentType: false
				}).done(function(response) {
					KTApp.unblockPage();

				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	toastr.clear();

		            if (code == _SUCCESS) {
		            	toastr.info(message);
		            	self.showInfo(data, true)			            
		            } else {
		            	toastr.error(message);
		            }

		            $(self.address.idModal).modal('hide');
				});
			}

			return false;
		});

		$(document).on('click', self.address.classBtnEdit, function(e) {
			self.clearInputAddressModal();

			var addressInfo = $(this).closest('.kt-widget5__item').data('address');		
			self.loadInfoAddressModal(addressInfo);

			$(self.address.idModal).modal('show');
		});
		
	},
	clearInfo: function(){
		var self = this;
		var wrapInfo = $(self.wrap.infoId);

		wrapInfo.find('span['+ self.attributeLabel.name +']').attr(self.attributeLabel.name, '').text('');
		wrapInfo.find('span['+ self.attributeLabel.address_name +']').attr(self.attributeLabel.address_name, '').text('');
		wrapInfo.find('span['+ self.attributeLabel.phone +']').attr(self.attributeLabel.phone, '').text('');
		wrapInfo.find('span['+ self.attributeLabel.address +']').attr(self.attributeLabel.address, '').text('');

		$(self.address.idBtnChange).popover('hide');

		self.formEl.find('input[name="contact"]').val('');
	},
	showInfo: function(data = {}, isUpdate = false){
		var self = this;
		var name = typeof(data.name) != _UNDEFINED && data.name != null ? data.name : '';
		var address_name = typeof(data.address_name) != _UNDEFINED && data.address_name != null ? data.address_name : '';
		var phone = typeof(data.phone) != _UNDEFINED && data.phone != null ? data.phone : '';
		var address = typeof(data.full_address) != _UNDEFINED && data.full_address != null ? data.full_address : '';

		if(!isUpdate){
			$(self.wrap.infoId).find('span['+ self.attributeLabel.name +']').attr(self.attributeLabel.name, name).text(name);	
		}			
		$(self.wrap.infoId).find('span['+ self.attributeLabel.address_name +']').attr(self.attributeLabel.name, address_name).text(address_name);
		$(self.wrap.infoId).find('span['+ self.attributeLabel.phone +']').attr(self.attributeLabel.phone, phone).text(phone);
		$(self.wrap.infoId).find('span['+ self.attributeLabel.address +']').attr(self.attributeLabel.address, address).text(address);

		$(self.wrap.infoId).removeClass('d-none');
		$(self.wrap.searchId).addClass('d-none');

		if(typeof(data.id) != _UNDEFINED){
			data.customer_id = data.id;
			delete data.id;
		}

		if(typeof(data.addresses) != _UNDEFINED){
			delete data.addresses;
		}

		self.formEl.find('input[name="contact"]').val(JSON.stringify(data));
	},
	showSearch: function(){
		var self = this;
		self.clearInfo();

		$(self.wrap.infoId).addClass('d-none');
		$(self.wrap.searchId).removeClass('d-none');
	},
	clearInputAddressModal: function(){
		var self = this;

		var _modal = $(self.address.idModal);		
		_modal.find('input').val('');

		var citySelect = _modal.find('select#city_id');
		var districtSelect = _modal.find('select#district_id');
		var wardSelect = _modal.find('select#ward_id');

		citySelect.val('');
		citySelect.selectpicker('refresh');

		districtSelect.find('option:not([value=""])').remove();
		districtSelect.selectpicker('refresh');

		wardSelect.find('option:not([value=""])').remove();
		wardSelect.selectpicker('refresh');
	},
	loadInfoAddressModal: function(addressInfo = {}){
		var self = this;

		var _modal = $(self.address.idModal);
		_modal.find('input[name="address_id"]').val(typeof(addressInfo.id) != _UNDEFINED ? addressInfo.id : '');
		_modal.find('input[name="name"]').val(typeof(addressInfo.name) != _UNDEFINED ? addressInfo.name : '');
		_modal.find('input[name="phone"]').val(typeof(addressInfo.phone) != _UNDEFINED ? addressInfo.phone : '');
		_modal.find('input[name="address"]').val(typeof(addressInfo.address) != _UNDEFINED ? addressInfo.address : '');

		_modal.find('select#city_id').val(typeof(addressInfo.city_id) != _UNDEFINED ? addressInfo.city_id : '');
		_modal.find('select#city_id').selectpicker('refresh');

		var city_id = typeof(addressInfo.city_id) != _UNDEFINED ? nhMain.utilities.parseInt(addressInfo.city_id) : null;
		var district_id = typeof(addressInfo.district_id) != _UNDEFINED ? nhMain.utilities.parseInt(addressInfo.district_id) : null;
		
		if(city_id > 0){
			var _data = {};
			_data[_PAGINATION] = {};
			_data[_PAGINATION][_PERPAGE] = 200;

			nhMain.callAjax({
	    		async: false,
				url: adminPath + '/district/json/' + city_id,
				data: _data,
			}).done(function(response) {
				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	if (code == _SUCCESS) {
	            	// append option
                    if (!$.isEmptyObject(data)) {
                    	var listOption = '';
				        $.each(data, function (key, item) {
				            listOption += '<option value="' + item.id + '">' + item.name + '</option>';
				        });
				        _modal.find('select#district_id').append(listOption);
				        _modal.find('select#district_id').selectpicker('refresh');
                    }		                    
	            } else {
	            	toastr.error(message);
	            }
			});
		}

		if(district_id > 0){
			var _data = {};
			_data[_PAGINATION] = {};
			_data[_PAGINATION][_PERPAGE] = 200;

			nhMain.callAjax({
	    		async: false,
				url: adminPath + '/ward/json/' + district_id,
				data: _data,
			}).done(function(response) {
				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	if (code == _SUCCESS) {
	            	// append option
                    if (!$.isEmptyObject(data)) {
                    	var listOption = '';
				        $.each(data, function (key, item) {
				            listOption += '<option value="' + item.id + '">' + item.name + '</option>';
				        });
				        _modal.find('select#ward_id').append(listOption);
				        _modal.find('select#ward_id').selectpicker('refresh');
                    }		                    
	            } else {
	            	toastr.error(message);
	            }
			});
		}

		_modal.find('select#district_id').val(typeof(addressInfo.district_id) != _UNDEFINED ? addressInfo.district_id : '');
		_modal.find('select#district_id').selectpicker('refresh');

		_modal.find('select#ward_id').val(typeof(addressInfo.ward_id) != _UNDEFINED ? addressInfo.ward_id : '');
		_modal.find('select#ward_id').selectpicker('refresh');
	}
}