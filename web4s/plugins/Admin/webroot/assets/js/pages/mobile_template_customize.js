"use strict";

var nhMobileTemplateCustomize = function () {
	var formEl;

	var initSubmit = function() {
		var btn = $('.btn-save');

		btn.on('click', function(e) {
			e.preventDefault();

			var config = nhThemeConfig.getConfig();
			formEl.find('input[name="config"]').val(JSON.stringify(config));
			var btn_save = $(this);

			// show loading
			KTApp.progress(btn_save);
			KTApp.blockPage(blockOptions);
			
			var formData = formEl.serialize();
			nhMain.callAjax({
				url: formEl.attr('action'),
				data: formData
			}).done(function(response) {

				// hide loading
				KTApp.unprogress(btn_save);
				KTApp.unblockPage();

			   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

	        	toastr.clear();
	            if (code == _SUCCESS) {	            
	            	toastr.info(message);
	            } else {
	            	toastr.error(message);
	            }
			});
		});
	}

    var nhPageConfig = {
    	idModal: '#page-info-modal',    	
    	idForm: '#page-info-form',
    	idWrapCategory: '#wrap-category',
    	idWrapLink: '#wrap-link',
    	idModalLayout: '#layout-info-modal',
    	idFormLayout: '#layout-info-form',
    	validator: null,
    	typeCategory: null,
    	init: function(){
    		var self = this;    		

    		// event for UI create page --------------------------------
    		$(document).on('click', '.btn-create-page', function(e) {
    			$(self.idModal).modal('show');
    			$(self.idModal).find(self.idForm).html('');
    			self.loadView();
    		});

    		$(document).on('click', '#btn-save-page', function(e) {
    			e.preventDefault();

    			var btnSave = $(this);
    			if (self.validator.form()) {
					KTApp.progress(btnSave);

					var formData = $(self.idForm).serialize();
					nhMain.callAjax({
						url: $(self.idForm).attr('action'),
						data: formData
					}).done(function(response) {
						KTApp.unprogress(btnSave);

					   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
			        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
			        	var pageCode = typeof(data.code) != _UNDEFINED ? data.code : '';

			        	toastr.clear();
			            if (code == _SUCCESS) {
			            	toastr.info(message);
			            	self.loadDropdownPage(pageCode);
			            	nhThemeConfig.loadStructureTemplate(pageCode);
			            } else {
			            	toastr.error(message);
			            }
			            $(self.idModal).modal('hide');
			            
					});
				}
    		});

			$(document).on('change', self.idForm + ' select#type', function (e) {
				var type = $(this).val();
				self.toggleLink(type);
				
				if(type.indexOf('_detail') > -1){
					type = type.substr(0, type.indexOf('_detail'));
				}

				if(self.typeCategory != type){
					self.typeCategory = type;
					$(self.idWrapCategory).html('');
					self.loadDropdownCategory(type);					
				}				
			});

			// event for UI update page -----------------------------
			$(document).on('click', '.btn-update-page', function(e) {
				var pageCode = $('#select-page').val();

				nhMain.callAjax({
					url: adminPath + '/mobile-app/template/load-config-page',
					data: {
						code: pageCode
					},
				}).done(function(response) {
					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

		        	if(code == _ERROR){
		        		toastr.error(message);
		        	}

		            if (code == _SUCCESS) {
		            	$(self.idModal).modal('show');
						$(self.idModal).find(self.idForm).html('');
				    	self.loadView({code: pageCode});
		            }
				});
			});

			//event UI delete page -----------------------------------------
			$(document).on('click', '.btn-delete-config-page', function(e) {
				var code = $('#select-page').val();
				var device = $('input[name="device"]').val();
				
				swal.fire({
			        title: nhMain.getLabel('xoa_cau_hinh_trang'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_cau_hinh_trang_nay'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		nhMain.callAjax({
							url: adminPath + '/mobile-app/template/delete-config-page',
							data:{
								code: code,
								device: device
							}
						}).done(function(response) {
							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	toastr.info(message);
				            	nhThemeConfig.loadStructureTemplate(code);
				            } else {
				            	toastr.error(message);
				            }

						})
			    	}
			    });
			});

			$(document).on('click', '.btn-delete-page', function(e) {
				var code = $('#select-page').val();
				swal.fire({
			        title: nhMain.getLabel('xoa_trang'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_trang_nay'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		nhMain.callAjax({
							url: adminPath + '/mobile-app/template/delete-page',
							data:{
								code: code
							}
						}).done(function(response) {
							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	toastr.info(message);

				            	location.reload();
				            } else {
				            	toastr.error(message);
				            }				            
						})
			    	}
			    });
			});

			//event select device ---------------------------------------
    		$(document).on('click', '.btn-select-device', function(e) {
    			if(!$(this).hasClass('active')){
    				$('.btn-select-device').removeClass('active');
    				$(this).addClass('active');

    				var device = typeof($(this).data('device')) != _UNDEFINED ? $(this).data('device') : 0;
    				formEl.find('input[name="device"]').val(device);

    				var pageCode = $('#select-page').val();
    				nhThemeConfig.loadStructureTemplate(pageCode);
    			}
    			
    		});
    	},
    	validate: function(){
    		var self = this;    		
    		
    		var formEl = $(self.idForm);	

    		$.validator.addMethod('url', function (url, element) {
		            if (url != nhMain.utilities.parseToUrl(url)) {
		                return false;
		            }
	                return true;
	            },
	            nhMain.getLabel('duong_dan_chua_dung_dinh_dang')
	        );
    		
    		self.validator = formEl.validate({
				ignore: ':hidden',
				rules: {
					name: {
						required: true,
						maxlength: 100
					},
					link: {
		    			required: true,
						maxlength: 255,
						url: true,
					}
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
	                var group = element.closest('.input-group');
	                if (group.length) {
	                    group.after(error.addClass('invalid-feedback'));
	                }else{                	
	                    element.after(error.addClass('invalid-feedback'));
	                }
	            }
			});
    	},
    	loadView: function(params = {}){
    		var self = this;
    		KTApp.blockPage(blockOptions);
    		var data = {
    			code: typeof(params.code) != _UNDEFINED ? params.code : null,
    		};
			nhMain.callAjax({
				async: true,
				url: adminPath + '/mobile-app/template/load-info-page',
				data: data,
				dataType: 'html'
			}).done(function(response) {				
				$(self.idModal + ' ' + self.idForm).html(response);
				var type = $(self.idForm).find('select#type').val();								
				self.validate();
				nhPageConfig.toggleLink(type);

				$('.kt-selectpicker').selectpicker();
			   	KTApp.unblockPage();
			});
    	},
    	loadDropdownCategory: function(type = null){
    		var self = this;

    		nhMain.callAjax({
    			async: true,
				url: adminPath + '/mobile-app/template/load-dropdown-category/' + type,
				dataType: 'html'
			}).done(function(response) {
				$(self.idWrapCategory).html(response);
				$(self.idWrapCategory + ' .kt-selectpicker').selectpicker();
			});
    	},
    	toggleLink: function(type = null){
    		var self = this;    		
    		if(!nhMain.utilities.notEmpty(type)) return false;

    		if(type == _NORMAL || type == _PRODUCT || type == _ARTICLE || type == _PRODUCT_DETAIL || type == _ARTICLE_DETAIL){
    			$(self.idWrapLink).find('input').each(function(index) {
				  	$(this).rules('add', 'required');
				});
				$(self.idWrapLink).removeClass('d-none');
    		}else{
    			$(self.idWrapLink).find('input').each(function(index) {
				  	$(this).rules('remove', 'required');
				});			
				$(self.idWrapLink).addClass('d-none');
    		}    		
    	},
    	loadDropdownPage: function(pageCode = null){
    		var self = this;

    		nhMain.callAjax({
    			async: true,
				url: adminPath + '/mobile-app/template/load-dropdown-page',
				data:{
					code: pageCode
				},
				dataType: 'html'
			}).done(function(response) {
				$('#wrap-dropdown-page').html(response);
				$('#wrap-dropdown-page' + ' .kt-selectpicker').selectpicker();
			});
    	}
    }

    var nhThemeConfig = {
    	idWrap: '#wrap-structure-template',
    	classRowItem: '.nh-row-item',
    	classColumnItem: '.nh-column-item',
    	rowTemplate: '<div class="row nh-row-item"></div>',
    	columnTemplate: '<div class="nh-column-item"><div class="nh-content-column"></div></div>',
    	init: function(){
    		var self = this;
    		
    		var pageCode = $('#select-page').val();
			self.loadStructureTemplate(pageCode);

   			$(document).on('click', function (e) {
			    if ($(e.target).closest('.nh-row-item').length === 0 && $(e.target).closest('.popover-setting-row').length === 0 && 
			    	$(e.target).closest('.block-item').length === 0 && $(e.target).closest('.modal-setting-template').length === 0) {
			        self.clearRowItem();
			    }
			});

			$(document).on('change', '#select-page', function(e) {
				self.loadStructureTemplate($(this).val());
			});

			$(document).on('click', self.idWrap + ' ' + self.classRowItem + ':not(.disable)', function(e) {
				if(!$(this).hasClass('active')){
					self.activeRowItem($(this));
				}
				self.showPopoverRowItem($(this));
    		});

    		$(document).on('click', '.nh-btn-add-row', function(e) {
    			var wrapListItem = $(this).closest('.nh-template-portlet').find('.wrap-list-item');
    			self.addRow(wrapListItem);
    		});

    		$(document).on('click', self.idWrap + ' .nh-btn-toggle-row', function(e) {
				var itemPortlet = $(this).closest('.nh-template-portlet');

				var hidden = itemPortlet.hasClass('kt-portlet--collapse');

				if(hidden){
					itemPortlet.find('.kt-portlet__body').slideDown();
					itemPortlet.removeClass('kt-portlet--collapse');
				}else{
					itemPortlet.find('.kt-portlet__body').slideUp();
					itemPortlet.addClass('kt-portlet--collapse');
				}
			});

    		$(document).on('click', '.row-setting-general', function(e) {
    			var _popover = $(this).closest('.popover.lg-popover');
    			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');    			
    			var config = typeof(rowItem.attr('data-config')) != _UNDEFINED && rowItem.attr('data-config').length > 0 ? JSON.parse(rowItem.attr('data-config')) : {};

    			nhGeneralConfig.init(config);

    			$('#row-setting-general-modal').modal('show');
    			self.hiddenPopoverRowItem(rowItem);
    		});

    		$(document).on('click', '.row-setting-column', function(e) {
    			$('#row-setting-column-modal').modal('show');

    			var _popover = $(this).closest('.popover.lg-popover');
    			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');
    			self.hiddenPopoverRowItem(rowItem);
    		});

    		$('#row-setting-column-modal').on('shown.bs.modal', function (e) {
    			var rowItem = $(self.classRowItem + '.active');
    			var numberColumn = rowItem.find(self.classColumnItem).length;
    			var valueColumn = [];
    			rowItem.find(self.classColumnItem).each(function(index) {
	    			var value = typeof($(this).attr('data-column-value') != _UNDEFINED) ? nhMain.utilities.parseInt($(this).attr('data-column-value')) : null;
				  	valueColumn.push(value);
	    		});
    			
			 	nhColumnConfig.init({
			 		column: numberColumn, 
			 		value: valueColumn
			 	});
			})

    		$(document).on('click', '.row-setting-delete', function(e) {
    			var _popover = $(this).closest('.popover.lg-popover');
    			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');

   				self.hiddenPopoverRowItem(rowItem);

   				swal.fire({
			        title: nhMain.getLabel('xoa_dong'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_dong_nay'),
			        type: 'error',
			        
			        confirmButtonText: '<i class="la la la-trash-o"></i>' + nhMain.getLabel('xoa_dong'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-secondary'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		rowItem.remove();
			    		self.clearRowItem();
			    	}
			    });
    		});

    		$(document).on('click', '.row-setting-copy', function(e) {
    			var _popover = $(this).closest('.popover.lg-popover');
    			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');

   				self.hiddenPopoverRowItem(rowItem);

    			swal.fire({
			        title: nhMain.getLabel('nhan_ban_dong'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_nhan_ban_dong_nay'),
			        type: 'info',
			        
			        confirmButtonText: '<i class="fa fa-copy"></i>' + nhMain.getLabel('nhan_ban'),
			        confirmButtonClass: 'btn btn-sm btn-brand',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-secondary'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		var htmlRow = rowItem[0].outerHTML;
			    		rowItem.after(htmlRow);
			    		self.activeRowItem(rowItem.next());
			    	}
			    });
    		});

    		$(document).on('click', '.row-setting-down', function(e) {
    			var _popover = $(this).closest('.popover.lg-popover');
    			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');    		
    			var htmlRowItem = rowItem[0].outerHTML;
    			var rowAfter = rowItem.next(self.classRowItem);

    			self.hiddenPopoverRowItem(rowItem);
    			if(rowAfter.length > 0){
    				rowItem.remove();
    				rowAfter.after(htmlRowItem);
    				self.showPopoverRowItem(rowItem);
    			}    	
    		});

    		$(document).on('click', '.row-setting-up', function(e) {
    			var _popover = $(this).closest('.popover.lg-popover');
    			var rowItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');
    			var htmlRowItem = rowItem[0].outerHTML;
    			var rowBefore = rowItem.prev(self.classRowItem);

    			self.hiddenPopoverRowItem(rowItem);
    			if(rowBefore.length > 0){
    				rowItem.remove();
	    			rowBefore.before(htmlRowItem);
	    			self.showPopoverRowItem(rowItem);
    			}
    		});
    	},
    	initObject: function(){
    		var self = this;
    		$(self.idWrap + ' .nh-content-column').sortable({
    			start: function( event, ui ) {
    				// hidden all popover
    				$('.popover').popover('hide');
    			}
    		});

			$('.nh-list-block li.block-item').draggable({
				helper: 'clone',
        		connectToSortable: '.nh-row-item:not(.disable) .nh-content-column:not(.disable)',
        		stop: function( event, ui ) {
        			if(typeof(ui.helper) != _UNDEFINED){
        				ui.helper.draggable({
        					containment: 'parent',
			        		connectToSortable: '.nh-content-column',
						});
        			}
        		}
			});
    	},
    	getConfig: function(){
    		var self = this;
    		var result = [];
 
    		$(self.idWrap).find('.wrap-list-item').each(function(i) {
    			$(this).find(self.classRowItem + ':not(.disable)').each(function(i) {
    				var blocks = [];
    				$(this).find('.block-item').each(function(j) {
	    				var blockCode = $(this).data('code');
						if(typeof(blockCode) == _UNDEFINED || blockCode.length == 0) return;
						blocks.push(blockCode);
	    			});
	    			result.push(blocks);
    			});			
    		});

    		return result;
    	},
    	clearRowItem: function(){
    		var self = this;
    		$(self.classRowItem).removeClass('active');

    		$('.popover-setting-row').popover('hide');
    	},
    	activeRowItem: function(rowItem = null){
    		if(typeof(rowItem) == _UNDEFINED){
    			return;
    		}

    		var self = this;
    		self.clearRowItem();
    		rowItem.addClass('active');
    	},
    	showPopoverRowItem: function(rowItem = null){
    		var self = this;
    		
    		if(typeof(rowItem) == _UNDEFINED || rowItem.length == 0) return;

    		// check exist popover of row
    		// if(typeof(rowItem.attr('aria-describedby'))  != _UNDEFINED) return;

    		// hidden all popover
    		$('.popover').popover('hide');

    		// set content popover
    		var contentPopover = $('#popover-setting-row').html();
    		var divContentPopover = $('<div></div>').html($('#popover-setting-row').html());
    		if(rowItem.is(':first-child')){
        		divContentPopover.find('.row-setting-up').remove();
        	}

        	if(rowItem.is(':last-child')){
        		divContentPopover.find('.row-setting-down').remove();
        	}

        	// init popover
			rowItem.popover({
    			placement: 'top',
    			html: true,
    			sanitize: false,
    			trigger: 'focus',
	            content: divContentPopover.html(),
	           	template: '\
		            <div class="popover lg-popover popover-setting-row" role="tooltip">\
		                <div class="arrow"></div>\
		                <div class="popover-body p-10"></div>\
		            </div>'
	        });

			rowItem.popover('show');

			// event after show
	        rowItem.on('shown.bs.popover', function (e) {
	        	var idPopover = rowItem.attr('aria-describedby');
	        	var _popover = $('#' + idPopover);

	        	_popover.find('[data-toggle="kt-tooltip"]').tooltip({
	        		trigger: 'hover'
	        	});
			})		
    	},
    	hiddenPopoverRowItem: function(rowItem = null){
    		if(typeof(rowItem) == _UNDEFINED){
    			return;
    		}

    		var idPopover = rowItem.attr('aria-describedby');
    		var _popover = $('#' + idPopover);
    		_popover.find('[data-toggle="kt-tooltip"]').tooltip('hide');
    		rowItem.popover('dispose');
    	},
    	addRow: function(wrapListItem = null){
    		var self = this;

    		if(typeof(wrapListItem) == _UNDEFINED) return;
    		
    		wrapListItem.append(self.rowTemplate);

    		var newRow = wrapListItem.find(self.classRowItem + ':last-child');
    		self.setNumberColumn(newRow, {column: 1, value: [12]})

    		self.initObject();
    	},
    	setNumberColumn: function(rowItem = null, config = {}){
    		if(typeof(rowItem) == _UNDEFINED){
    			return;
    		}

    		var self = this;
    		var column = typeof(config.column != _UNDEFINED) ? parseInt(config.column) : null;
    		var valueColumn = typeof(config.value != _UNDEFINED) ? config.value : [];

    		if(!column > 0 || $.isEmptyObject(valueColumn)){
    			return;
    		}

    		rowItem.html('');
    		for (var i = 0; i < column; i++) {
    			rowItem.append(self.columnTemplate);
    			var newColumn = rowItem.find(self.classColumnItem + ':last-child');
    			var value = typeof(valueColumn[i] != _UNDEFINED) ? valueColumn[i] : 1;

    			newColumn.addClass('col-' + value);
    			newColumn.attr('data-column-value', value);
    		}

    		self.initObject();
    	},
    	loadStructureTemplate: function(page_code = null){
    		var self = this;
    		var device = formEl.find('input[name="device"]').val();

    		KTApp.blockPage(blockOptions);
    		nhMain.callAjax({
    			async: true,
    			dataType: 'html',
				url: adminPath + '/mobile-app/template/load-structure-page',
				data: {
					code: page_code,
					device: device
				}
			}).done(function(response) {
				KTApp.unblockPage();
				if (typeof(response.code) != _UNDEFINED && response.code == _ERROR) return;

	            $(self.idWrap).html(response);
				self.initObject();		
			});
    	}
    }

    var nhGeneralConfig = {
    	idModal: '#row-setting-general-modal',
    	idWrap: '#wrap-setting-general',
    	init: function(config = {}){
    		var self = this;

    		self.clearInput();		
			self.loadConfig(config);

			$(document).on('click', '#save-row-setting-general', function(e) {
				var config = self.getConfig();

				var rowItem = $('.nh-row-item.active');
				rowItem.attr('data-config', JSON.stringify(config));

    			$(self.idModal).modal('hide');
    		});    		
    	},
    	clearInput: function(){
    		var self = this;

    		$('input', $(self.idWrap)).each(function () {             
                if($(this).attr('type') == 'checkbox'){
                	$(this).prop('checked', false);
                }else{
                	$(this).val('');
                }
            });
    	},
    	loadConfig: function(config = {}){
    		var self = this;

    		$('input', $(self.idWrap)).each(function () {
                var inputName = $(this).attr('name');
                if(typeof(config[inputName]) == _UNDEFINED) return;

                if($(this).attr('type') == 'checkbox'){
                	if (config[inputName] == 1) {
                        $(this).prop('checked', true);
                    }
                }else{
                	$(this).val(config[inputName]);
                }
            });
    	},
    	getConfig: function(){
    		var self = this;
    		var config = {};
    		$('input', $(self.idWrap)).each(function () {
                var inputName = $(this).attr('name');
                var inputValue = '';

                if($(this).attr('type') == 'checkbox'){
                	if ($(this).is(':checked')) {
                        inputValue = 1;
                    }
                }else{
                	inputValue = $(this).val();
                }
                config[inputName] = inputValue;
            });

            return config;
    	}
    }

    var nhColumnConfig = {
    	idModal: '#row-setting-column-modal',
    	idWrap: '#wrap-setting-column',
    	table: null,
    	widthTable: 0,
    	numberColumn: 1,
    	minWidth: 0,
    	tdTemplate: '<td><span class="column-value"></span></td>',
    	init: function(config = {}){
    		var self = this;

    		var numberColumn = typeof(config.column != _UNDEFINED) ? parseInt(config.column) : null;
    		var configValue = typeof(config.value != _UNDEFINED) ? config.value : [];

    		if(numberColumn > 0){
    			self.numberColumn = numberColumn;
    			$(self.idWrap + ' #number-column-select').val(self.numberColumn);
    		}else{
    			self.numberColumn = parseInt($(self.idWrap + ' #number-column-select').val());
    		}

    		self.table = $(self.idWrap + ' .nh-table-config-column');
    		self.widthTable = parseInt(self.table.width());
    		
    		self.minWidth = parseInt(self.widthTable / 12);
    		self.setNumberColumn(configValue);

    		$(document).on('change', self.idWrap + ' #number-column-select', function(e) {
    			self.numberColumn = parseInt($(this).val());
    			self.setNumberColumn();
    		});

    		$(document).on('click', '#save-row-setting-column', function(e) {
    			var tr = self.table.find('tr:first-child');
    			var numberColumn = tr.find('td').length;

    			var valueColumn = [];
    			tr.find('td').each(function(index) {
				  	var value = $(this).find('.column-value').text();
				  	if(parseInt(value) > 0){
				  		valueColumn.push(parseInt(value));
				  	}				  	
				});

				var rowItem = $('.nh-row-item.active');

				nhThemeConfig.setNumberColumn(rowItem, {
					column: numberColumn,
					value: valueColumn
				});

    			$(self.idModal).modal('hide');
    		});
    	},
    	setNumberColumn: function(configValue = []){
    		var self = this;

    		var tr = self.table.find('tr');
			tr.html('');

			for (var i = 0; i < self.numberColumn; i++){
				tr.append(self.tdTemplate);
				var td = tr.find('td:last-child');
				var width = nhMain.utilities.parseInt(self.widthTable / self.numberColumn);

				if(!$.isEmptyObject(configValue) && typeof(configValue[i]) != _UNDEFINED){
					width = Math.round(nhMain.utilities.parseInt(self.widthTable / (12/configValue[i])));
				}

				td.css('width', width);	
				td.css('min-width', self.minWidth);
			}

			self.setValueColumn();
			self.resizableColumn(self.table);
    	},
    	resizableColumn: function(table = null){
    		var self = this;
    		var tr = typeof(table.find('tr')) != _UNDEFINED ? table.find('tr') : null;
			if (typeof(tr) == _UNDEFINED || tr == null) return;

			tr.find('td:not(:last-child)').each(function(index) {
			  	var div = $('<div class="nh-resize-column">');
			  	self.setListeners(div[0]);			  	
			  	$(this).append(div);			  	
			});
    	},
    	setListeners: function(div){
    		var self = this;
    		var pageX, curCol, nxtCol, curColWidth, nxtColWidth;

			div.addEventListener('mousedown', function (e) {
				curCol = e.target.parentElement;
				nxtCol = curCol.nextElementSibling;
				pageX = e.pageX; 

				var padding = 0;
				curColWidth = curCol.offsetWidth - padding;
				if (typeof(nxtCol) != _UNDEFINED && nxtCol != _UNDEFINED){
					nxtColWidth = nxtCol.offsetWidth - padding;
				}				
			});

			document.addEventListener('mousemove', function (e) {
				if (typeof(curCol) != _UNDEFINED && curCol != _UNDEFINED) {
					var diffX = e.pageX - pageX;

					if (typeof(nxtCol) != _UNDEFINED && nxtCol != _UNDEFINED){
						nxtCol.style.width = (nxtColWidth - (diffX)) + 'px';
					}				 
					curCol.style.width = (curColWidth + diffX) + 'px';

					self.setValueColumn();
				}
			});

			document.addEventListener('mouseup', function (e) {
				curCol = _UNDEFINED;
				nxtCol = _UNDEFINED;
				pageX = _UNDEFINED;
				nxtColWidth = _UNDEFINED;
				curColWidth = _UNDEFINED
			});
    	},
    	setValueColumn: function(){
    		var self = this;    	
    		var totalValue = 12;

    		self.table.find('td').each(function(index) {
 				var _width = $(this).width();
 				var value = Math.round(_width / nhMain.utilities.parseInt(self.widthTable / 12));
 				totalValue -= value;
 				if (index === (self.numberColumn - 1) && totalValue != 0) {
		       		value += totalValue;
		        }

		        $(this).find('.column-value').text(value);
    		});
    	},
    	resetModal: function(){
    		var self = this;
    	}
    }

    var nhBlockConfig = {
    	idWrap: '#wrap-structure-template',
    	idModal: '#block-info-modal',
    	init: function(){
    		var self = this;

    		$(document).on('click', function (e) {
			    if ($(e.target).closest('.block-item').length === 0 && $(e.target).closest('.popover-setting-block').length === 0) {
			        $('.popover-setting-block').popover('hide');
			    }
			});

    		$(document).on('click', self.idWrap + ' .block-item', function(e) {
    			e.stopPropagation();
    			self.showPopoverBlock($(this));
    		});

    		$(document).on('click', '.block-setting-general', function(e) {
    			var _popover = $(this).closest('.popover.lg-popover');
    			var blockItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');
    			var codeBlock = nhMain.utilities.notEmpty(blockItem.attr('data-code')) ? blockItem.attr('data-code') : null;

    			blockItem.popover('hide');
    			if(codeBlock != null){
    				window.open(adminPath + '/mobile-app/block/update/' + codeBlock, '_blank');
    			}    			
    		});

    		$(document).on('click', '.block-setting-delete', function(e) {
    			var _popover = $(this).closest('.popover.lg-popover');
    			var blockItem = $('[aria-describedby="'+ _popover.attr('id') +'"]');

    			blockItem.popover('hide');

    			swal.fire({
			        title: nhMain.getLabel('xoa_block'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_block_nay_khoi_cau_hinh_trang'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		blockItem.remove();	
			    	}
			    });    			
    		});
    	},
    	showPopoverBlock: function(blockItem = null){
    		var self = this;
    		if(typeof(blockItem) == _UNDEFINED || !blockItem.length > 0) return;

    		// check exist popover of row
    		if(typeof(blockItem.attr('aria-describedby'))  != _UNDEFINED) return;

    		// hidden other popover
    		$('.popover').popover('hide');

    		// init popover
			blockItem.popover({
    			placement: 'top',
    			html: true,
    			sanitize: false,
    			trigger: 'focus',
	            content: '\
	            	<div class="row">\
				        <div class="col-12">\
				            <div class="btn-toolbar" role="toolbar">\
				                <span class="btn-group" role="group">\
				                    <span class="btn btn-sm btn-secondary block-setting-delete" data-toggle="kt-tooltip" data-placement="top" title="'+ nhMain.getLabel('xoa') +'">\
				                        <i class="fa fa-trash-alt m-0"></i>\
				                    </span>\
				                    <span class="btn btn-sm btn-secondary block-setting-general" data-toggle="kt-tooltip" data-placement="top" title="'+ nhMain.getLabel('thiet_lap') +'">\
				                        <i class="fa fa-cog m-0"></i>\
				                    </span>\
				                </span>\
				            </div>\
				        </div>\
				    </div>',
	           	template: '\
		            <div class="popover lg-popover popover-setting-block" role="tooltip">\
		                <div class="arrow"></div>\
		                <div class="popover-body p-10"></div>\
		            </div>'
	        });

	        blockItem.popover('show');

	        // event after show
	        blockItem.on('shown.bs.popover', function (e) {
	        	var idPopover = blockItem.attr('aria-describedby');
	        	var _popover = $('#' + idPopover);

	        	_popover.find('[data-toggle="kt-tooltip"]').tooltip({
	        		trigger: 'hover'
	        	});
			})		
    	}
    }

    var nhListBlock = {
    	init: function(){

			$(document).on('keyup', '#search-block', function(e) {
				var keyword = $(this).val().toLowerCase();
			    $('.nh-list-block li.block-item').each(function(i) {
			        var text = $('span', this).text().toLowerCase();
			        if(text.indexOf(keyword) < 0){
			            $(this).addClass('d-none');
			        } else {
			            $(this).removeClass('d-none');
			        }
			    });
			});
    	}
    }

	return {
		init: function() {
			formEl = $('#main-form');
   			nhThemeConfig.init();
   			nhPageConfig.init(); 
   			nhBlockConfig.init();
   			nhListBlock.init();
   			initSubmit();

   			// sticky left block
   			if (KTLayout.onAsideToggle) {
	            var sticky = new Sticky('.sticky');
	            KTLayout.onAsideToggle(function() {
	                setTimeout(function() {
	                    sticky.update();
	                }, 500);
	            });
	        }
   			$('.kt-selectpicker').selectpicker();
		}
	};
}();

$(document).ready(function() {
	nhMobileTemplateCustomize.init();
});
