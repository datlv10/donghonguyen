"use strict";

var nhExportData = {
	init: function(){
		var self = this;
		self.initializationStep.init();
		self.migrateStep.init();
		self.exportStep.init();
	},
	initializationStep: {
		init: function(){
			var self = this;

			self.event();
		},
		event: function () {
			var self = this;
			$(document).on('click', '.btn-export-data:not(.disabled)', function(e) {
				e.preventDefault();

				var type = $(this).attr('type');
				var url = null;
				switch(type){
					case 'initialization':
						url = adminPath + '/transform-data/export/initialization';
					break;

					case 'read_database':
						url = adminPath + '/transform-data/export/read-database';
					break;
				}

				if(url == null) return false;

				KTApp.blockPage(blockOptions);
				nhMain.callAjax({
		            url: url,
		            type: 'POST'
		        }).done(function(response) {
		        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	if (code == _SUCCESS) {
		        		toastr.success(message);
		        		location.reload();
		            } else {
		            	toastr.error(message);
		            	setTimeout(function(){ location.reload(); }, 2000);
		            }
		            
		            KTApp.unblockPage();
		        });
			});

			$(document).on('click', '[nh-show-config]', function(e) {
				e.preventDefault();
				var type = $(this).attr('type');
				var wrapHtml = $('[nh-list-config]');

				if ($(this).hasClass('btn-success')) {
					wrapHtml.find('.listbox-content').html('');
					wrapHtml.addClass('d-none');
					$(this).removeClass('btn-success');

					return false;
				}

				if (typeof(type) == _UNDEFINED || type == null || type == '' || wrapHtml.length == 0) return false;

				var labelName = '';
				switch(type){
					case 'categories_article':

						labelName = nhMain.getLabel('cau_hinh_danh_muc_bai_viet');
						break;
					case 'categories_product':

						labelName = nhMain.getLabel('cau_hinh_danh_muc_san_pham');
						break;
					case 'attributes_article':

						labelName = nhMain.getLabel('cau_hinh_thuoc_tinh_bai_viet');
						break;
					case 'attributes_product':

						labelName = nhMain.getLabel('cau_hinh_thuoc_tinh_san_pham');
						break;
					case 'attributes_product_item':

						labelName = nhMain.getLabel('cau_hinh_thuoc_tinh_phien_ban_san_pham');
						break;
				}

				$('[nh-show-config]').removeClass('btn-success');
				$(this).addClass('btn-success');

				nhMain.callAjax({
		            url: adminPath + '/transform-data/export/load-config-advanced',
		            type: 'POST',
		            data: {
	            		type: type
		            },
		            dataType: _HTML,
		        }).done(function(response) {
		        	wrapHtml.find('.listbox-content').html(response);
		        	wrapHtml.find('.title').html(labelName);

		        	wrapHtml.removeClass('d-none');

		        	self.eventCheckAll();
		        });
			});

			$(document).on('click', '[nh-save-config]', function(e) {
				e.preventDefault();
				var formElement = $('#config-advanced-form');
				if (formElement.length == 0) return false;

				var formData = formElement.serialize();
				nhMain.callAjax({
		            url: formElement.attr('action'),
		            type: 'POST',
		            data: formData
		        }).done(function(response) {
		        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	if (code == _SUCCESS) {
		        		toastr.success(message);
		            } else {
		            	toastr.error(message);
		            }
		            
		            KTApp.unblockPage();
		        });
			});

			$('.check-all').change(function () {
				var tableElement = $(this).closest('table');
				
			    if (this.checked) {
			        tableElement.find('.check-single').each(function () {
			            this.checked = true;
			        });
			    } else {
			        tableElement.find('.check-single').each(function () {
			            this.checked = false;
			        });
			    }
			});

			$('.check-single').change(function () {
			    if ($(this).is(':checked')) {
			        var isAllChecked = 0;
			        $('.check-single').each(function () {
			            if (!this.checked) isAllChecked = 1;
			        });
			        if (isAllChecked == 0) {
			            $('.check-all').prop('checked', true);
			        }
			    } else {
			        $('.check-all').prop('checked', false);
			    }
			});

			// event check all
			self.eventCheckAll();

			// cấu hình dữ liệu export
			self.eventModalConfigData();

			// cấu hình ID bản ghi
			self.eventModalConfigId();

			// cấu hình chung
			self.eventModalConfigCdn();
		},
		eventCheckAll: function () {
			var self = this;

			var inputCheckAll = $('[nh-check-all-config]');
			var wrapConfig = $('#config-advanced-form');
			var inputCheckSingle = wrapConfig.find('[nh-check-single]');

			if (inputCheckAll.length == 0 || wrapConfig.length == 0 || inputCheckSingle.length == 0) return false;

			inputCheckAll.change(function () {
				var _thisChecked = this.checked;

				inputCheckSingle.each(function () {
					if (_thisChecked) {
				        this.checked = true;
				    } else {
				        this.checked = false;
				    }
		        });
			});

			self.processInputCheckAll();

	        // khi check input check single thì check nếu tất cả các input được check thì input check all = checked
	        inputCheckSingle.change(function () {
				self.processInputCheckAll();
			});
		},
		processInputCheckAll: function () {
			var inputCheckAll = $('[nh-check-all-config]');
			var wrapConfig = $('#config-advanced-form');
			var inputCheckSingle = $('[nh-check-single]');

			if (inputCheckAll.length == 0 || wrapConfig.length == 0 || inputCheckSingle.length == 0) return false;

			// check xem khi mở cấu hình nếu tất cả input đều được check thì input check all = checked
			var checkAll = false;
			inputCheckAll.prop("checked", false);

			inputCheckSingle.each(function () {

	            if (this.checked) {
			        checkAll = true;
			    } else {
			        checkAll = false;
			        return false;
			    }
	        });

	        if (checkAll) {
	        	inputCheckAll.prop("checked", true);
	        }
		},
		eventModalConfigData: function(){
			var self = this;

			var formConfigDataElement = $('#config-data-form');
			var modalConfigData = $('#config-data-modal');
			if (formConfigDataElement.length == 0 || modalConfigData.length == 0) return false;
			
			$(document).on('click', '#btn-show-config-data:not(.disabled)', function(e) {
				e.preventDefault();
				modalConfigData.modal('show');
			});

			$(document).on('click', '#btn-config-data', function(e) {
				var languages = [];
				if ($('[nh-config-lang]').length != 0) {

					$('[nh-config-lang]:checked').each(function() {
						let lang = $(this).val();
						languages.push(lang);
					})
				}

				var formData = formConfigDataElement.serialize();
				formData = formData + '&languages=' + languages.join('-');
				nhMain.callAjax({
					url: formConfigDataElement.attr('action'),
					data: formData
				}).done(function(response) {
					// hide loading
					// KTApp.unprogress(btn_save);
					KTApp.unblockPage();

					//show message and redirect page
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
		eventModalConfigId: function(){
			var self = this;

			var formConfigIdElement = $('#config-id-form');
			var validatorConfigId = formConfigIdElement.validate({
				ignore: ':hidden',
				rules: {
					category_id_start: {
						required: true
					},
					article_id_start: {
						required: true,
					},
					product_id_start: {
						required: true,
					},
					tag_id_start: {
						required: true,
					},
					attribute_id_start: {
						required: true,
					}
				},
				messages: {
					category_id_start: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
	                },
	                category_id_start: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
	                },
	                product_id_start: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
	                },
	                tag_id_start: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
	                },
	                attribute_id_start: {
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
					validator.errorList[0].element.focus();
				},
			});

			$(document).on('click', '#btn-show-config-id:not(.disabled)', function(e) {
				e.preventDefault();
				var modal = $('#config-id-modal');

				modal.find('input.number-input').each(function() {
					nhMain.input.inputMask.init($(this), 'numeric');
				});

				modal.modal('show');
			});

			$(document).on('click', '#btn-config-id:not(.disabled)', function(e) {
				e.preventDefault();
				if (!validatorConfigId.form()) return false;

				var formData = formConfigIdElement.serialize();
				KTApp.blockPage(blockOptions);
				nhMain.callAjax({
		            url: formConfigIdElement.attr('action'),
		            type: 'POST',
		            data: formData
		        }).done(function(response) {
		        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	if (code == _SUCCESS) {
		        		toastr.success(message);
		        		location.reload();
		            } else {
		            	toastr.error(message);
		            	setTimeout(function(){ location.reload(); }, 2000);
		            }
		            
		            KTApp.unblockPage();
		        });
			});
		},
		eventModalConfigCdn: function(){
			var self = this;

			var formConfigGeneralElement = $('#config-cdn-form');
			var validatorConfigGeneral = formConfigGeneralElement.validate({
				ignore: ':hidden',
				rules: {
					url_cdn: {
						required: true
					},
					url_cdn_new: {
						required: true
					}
				},
				messages: {
					url_cdn: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
	                },
	                url_cdn_new: {
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
					validator.errorList[0].element.focus();
				},
			});

			$(document).on('click', '#btn-show-config-cdn:not(.disabled)', function(e) {
				e.preventDefault();
				var modal = $('#config-cdn-modal');
				modal.modal('show');
			});

			$(document).on('click', '#btn-config-cdn:not(.disabled)', function(e) {
				e.preventDefault();
				if (!validatorConfigGeneral.form()) return false;

				var formData = formConfigGeneralElement.serialize();
				KTApp.blockPage(blockOptions);
				nhMain.callAjax({
		            url: formConfigGeneralElement.attr('action'),
		            type: 'POST',
		            data: formData
		        }).done(function(response) {
		        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	if (code == _SUCCESS) {
		        		toastr.success(message);
		        		location.reload();
		            } else {
		            	toastr.error(message);
		            	setTimeout(function(){ location.reload(); }, 2000);
		            }
		            
		            KTApp.unblockPage();
		        });
			});
		}
	},
	migrateStep:{
		init: function(){
			var self = this;
			self.event();
		},
		event: function(){
			var self = this;

			$(document).on('click', '#btn-migrate:not(.disabled)', function(e) {
				e.preventDefault();
				
				var btnMigrate = $(this);

				// show loading
				btnMigrate.find('.icon-spinner').removeClass('d-none');
				btnMigrate.addClass('disabled');

				// start migrate data
				var type = typeof($(this).attr('type')) != _UNDEFINED ? $(this).attr('type') : null;
				self.migrateData(type, function(e) {
					// remove loading
		            btnMigrate.find('.icon-spinner').addClass('d-none');
					btnMigrate.removeClass('disabled');

					location.reload();
				});

	            
			});
		},
		migrateData: function(type = null, callback = null){
			var self = this;
			if(type == null || type.length == 0) return false;

			if (typeof(callback) != 'function') {
 		        callback = function () {};
  		    }

			nhMain.callAjax({
	            url: adminPath + '/transform-data/export/migrate-data',
	            type: 'POST',
	            data: {
	            	type: type,
	            }
	        }).done(function(response) {
	        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : '';
	        	if (code == _SUCCESS) {
	        		if(typeof(data.migrated) != _UNDEFINED && data.migrated > 0){
	        			$('#label-migrated').html(data.migrated);
	        		}

	        		setTimeout(function(){
	        			toastr.success(message);
        				callback(response);
	        		},2000);
        			
	            } else {
	            	toastr.error(message);
	            }
	            
	            KTApp.unblockPage();
	        });

		}
	},
	exportStep: {
		init: function(){
			var self = this;

			self.event();
		},
		event: function(){
			var self = this;

			$(document).on('click', '#btn-export:not(.disabled)', function(e) {
				e.preventDefault();
				
				var btnMigrate = $(this);

				// show loading
				btnMigrate.find('.icon-spinner').removeClass('d-none');
				btnMigrate.addClass('disabled');

				// start migrate data
				var type = typeof($(this).attr('type')) != _UNDEFINED ? $(this).attr('type') : null;
				self.exportData(function(e) {
					// remove loading
		            btnMigrate.find('.icon-spinner').addClass('d-none');
					btnMigrate.removeClass('disabled');

					location.reload();
				});

	            
			});
		},
		exportData: function(callback) {
			var self = this;

			if (typeof(callback) != 'function') {
 		        callback = function () {};
  		    }

			nhMain.callAjax({
	            url: adminPath + '/transform-data/export/export-data',
	            type: 'POST'
	        }).done(function(response) {
	        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : '';
	        	if (code == _SUCCESS) {
	        		toastr.success(message);
	        		callback(response);
	            } else {
	            	toastr.error(message);
	            }
	            
	            KTApp.unblockPage();
	        });
		}
	}
}

$(document).ready(function() {
	nhExportData.init();
});