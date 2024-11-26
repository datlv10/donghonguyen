"use strict";

var nhMobileTemplateList = {
	init: function(){
		var self = this;

		self.event();
		self.export.init();
		self.import.init();
		self.delete.init();
	},
	event: function(){
		$(document).on('click', '.nh-set-default', function() {
			var _id = $(this).data('id');

			if(_id.length == 0){
		    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi_da_chon'));
		    	return false;
		    }

			swal.fire({
		        title: nhMain.getLabel('chon_giao_dien_mac_dinh'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_giao_dien_nay_lam_mac_dinh'),
		        type: 'question',
		        
		        confirmButtonText: nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
		    		nhMain.callAjax({
						url: adminPath + '/mobile-app/template/set-default',
						data:{
							id: _id
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

			return false;
		});
	},
	export: {
		modal: null,
		form: null,
		validator: null,
		init: function(){
			var self = this;

			self.modal = $('#export-template-modal');
			if(self.modal.length == 0) return false;

			self.event();
		},
		event: function(){
			var self = this;

			$(document).on('click', '.nh-export', function(e) {
				e.preventDefault();

				self.loadModal($(this).data('id'));
			});

			$(document).on('click', '#btn-export-template:not(.disabled)', function(e) {
				e.preventDefault();
				var _thisButton = $(this);

				if (self.validator != null && self.validator.form()) {
					KTApp.progress(_thisButton);
					KTApp.blockPage(blockOptions);

					// var formData = self.form.serialize();
					var formData = new FormData(self.form.get(0));

					nhMain.callAjax({
			            url: adminPath + '/mobile-app/template/export',
			            type: 'POST',
			            dataType: 'json',
			            data: formData,
			            contentType: false,
	    				processData: false,
			        }).done(function(response) {
			        	KTApp.unprogress(_thisButton);
			        	KTApp.unblockPage();
			        	
			        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
			        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
			        	var template_file = typeof(data.template_file) != _UNDEFINED ? data.template_file : null;

			            if (code == _SUCCESS) {		            	
			            	if(template_file != null && template_file.length > 0){
			            		self.modal.modal('hide');
			            		window.location.href = template_file;
			            	}else{
			            		toastr.error(nhMain.getLabel('khong_lay_duoc_duong_dan_download'));
			            	}
			            } else {
			            	toastr.error(message);
			            }
			        });
				}
			});
		},
		initForm: function(){
			var self = this;

			self.validator = self.form.validate({
				ignore: ':hidden',
				rules: {
					name: {
						required: true,
						maxlength: 255
					},
					code: {
						required: true,
						maxlength: 50
					},
					author: {
						required: true,
						maxlength: 50
					},
					version: {
						required: true,
						maxlength: 10
					}
				},
				messages: {
	                name: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    	maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
	                },
	                code: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
	                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
	                },
	                author: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
	                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
	                },
	                version: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
	                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
	                },
	            },

	            errorPlacement: function(error, element) {            	
	            	error.addClass('invalid-feedback');

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
					validator.errorList[0].element.focus();
				},
			});

			self.selectAvatarTemplate();
		},
		loadModal: function(template_id = null){
			var self = this;

			if(template_id == null || typeof(template_id) == _UNDEFINED || !template_id > 0) return false;

			self.modal.find('.modal-body').html('');
			self.modal.modal('show');

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
	            url: adminPath + '/mobile-app/template/load-form-export',
	            type: 'POST',
	            dataType: 'html',
	            data:{
	            	template_id: template_id
	            }
	        }).done(function(response) {
	            self.modal.find('.modal-body').html(response);

	            self.form = self.modal.find('form');
	            self.initForm();

	            KTApp.unblockPage();
	        });
		},
		selectAvatarTemplate: function(){
			var self = this;

			self.form.on('click', '#btn-select-image', function(e) {
				$('#input-image-avatar').trigger('click');
			});

			self.form.on('change', '#input-image-avatar', function(e) {
				e.preventDefault();

				var _thisInput = this;
				var reader = new FileReader();
				reader.readAsDataURL(_thisInput.files[0]);
				reader.onload = function (e) {
					var theHoder = $(_thisInput).closest('.kt-avatar').find('.kt-avatar__holder');
					theHoder.css('background-image', 'url('+ e.target.result +')');
		        }
				
			});
		}
	},
	import: {
		modal: null,
		form: null,
		init: function(){
			var self = this;

			self.modal = $('#import-template-modal');
			if(self.modal.length == 0) return false;

			self.form = $('#form-import-template');
			if(self.form.length == 0) return false;

			self.initForm();
			self.event();
		},
		event: function(){
			var self = this;
			
			$(document).on('click', '.nh-import-template', function(e) {
				e.preventDefault();
				self.modal.modal('show');
			});

			$(document).on('click', '#btn-import-template:not(.disabled)', function(e) {
				e.preventDefault();
				var _thisButton = $(this);
				
				if (self.validator != null && self.validator.form()) {

					//check template code exist in system
					var filename = self.form.find('input[name=template_file]').val().split('\\').pop();
					filename = filename.replace('.zip', '');
					if(!filename.length > 0) return false;

					self.checkExistTemplate(filename, function(res){
						if(typeof(res.exist) != _UNDEFINED && res.exist){
							swal.fire({
						        title: nhMain.getLabel('cai_dat_giao_dien'),
						        text: nhMain.getLabel('giao_dien_nay_da_duoc_cai_dat_tren_he_thong_ban_co_muon_tiep_tuc_cai_dat_khong'),
						        type: 'warning',
						        
						        confirmButtonText: '<i class="fa fa-check"></i>' + nhMain.getLabel('dong_y'),
						        confirmButtonClass: 'btn btn-sm btn-danger',

						        showCancelButton: true,
						        cancelButtonText: nhMain.getLabel('huy_bo'),
						        cancelButtonClass: 'btn btn-sm btn-default'
						    }).then(function(result) {
						    	if(typeof(result.value) != _UNDEFINED && result.value){
						    		self.excuteImportTemplate(_thisButton);
						    	}
						    });
						}else{
							self.excuteImportTemplate(_thisButton);
						}
					});
				}
			});
		},
		initForm: function(){
			var self = this;

			self.validator = self.form.validate({
				ignore: ':hidden',
				rules: {
					template_file: {
						required: true
					}
				},
				messages: {
	                template_file: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
	                }
	            },
	            errorPlacement: function(error, element) {
	            	error.addClass('invalid-feedback');

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
					validator.errorList[0].element.focus();
				},
			});
		},
		excuteImportTemplate: function(buttonSave = null){
			var self = this;

			if(buttonSave.length > 0){
				buttonSave.addClass('disabled');
			}

			KTApp.progress(buttonSave);
			KTApp.blockPage(blockOptions);

			var formData = new FormData(self.form.get(0));

			nhMain.callAjax({
	            url: adminPath + '/mobile-app/template/import',
	            type: 'POST',
	            dataType: 'json',
	            data: formData,
	            contentType: false,
				processData: false,
	        }).done(function(response) {
	        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

	        	KTApp.unprogress(buttonSave);
			    KTApp.unblockPage();

	            if (code == _SUCCESS) {
		           	self.modal.modal('hide');
		            toastr.success(nhMain.getLabel('cai_dat_giao_dien_thanh_cong'));

		            location.reload();
	            } else {
	            	toastr.error(message);
	            }
	        });
		},
		checkExistTemplate: function(template_code = null, callback = null){
			var self = this;
			if (typeof(callback) != 'function') {
				callback = function () {};
			}

			nhMain.callAjax({
	            url: adminPath + '/mobile-app/template/check-exist',
	            type: 'POST',
	            dataType: 'json',
	            data: {
	            	template_code: template_code
	            },
	        }).done(function(response) {	        	
	        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	            if (code == _SUCCESS) {
		           	callback(data);
	            } else {
	            	toastr.error(message);
	            }
	        });
		}
	},
	delete: {
		init: function(){
			var self = this;

			$(document).on('click', '.nh-delete-template', function() {
				var _id = $(this).data('id');

				if(_id.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi_da_chon'));
			    	return false;
			    }

				swal.fire({
			        title: nhMain.getLabel('xoa_giao_dien'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_giao_dien_nay'),
			        type: 'warning',
			        
			        confirmButtonText: nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		nhMain.callAjax({
							url: adminPath + '/mobile-app/template/delete',
							data:{
								id: _id
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

				return false;
			});
		}	
	}
}

$(document).ready(function() {
	nhMobileTemplateList.init();
});