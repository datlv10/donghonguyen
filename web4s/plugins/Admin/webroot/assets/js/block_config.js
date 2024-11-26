"use strict";

var copyMedia = false;
var previewMedia = false;

var nhBlockConfig = {
	block_code: null,
	idWrap: '#wrap-block-config',
	idWrapViewData: '#wrap-view-data',
	idWrapDataSelected: '#wrap-data-selected',

	editorDataExtend: null,
	editorModifyView: null,
	editorDifferent: null,
	editorHtmlBlock: null,
	init: function(){
		var self = this;
		self.block_code = $(self.idWrap).data('code');

		self.dataExtend.init();

		self.eventViewData();
		var typeBlock = $('#type-block').val();
		
		switch(typeBlock){
			case _HTML:
				self.blockHtml.init();
			break;

			case _MENU:
				self.managerItem.init();
				self.blockMenu.init();
			break;

			case _SLIDER:
				self.managerItem.init();
				self.blockSlider.init();
			break;

			case _TAB_PRODUCT:
			case _TAB_ARTICLE:
				self.managerItem.init();
				self.blockTab.init();
			break;
		}
				
		self.typeLoadBlock.init();
		self.initInput();		

		self.logUpdate.init();

		// save block
		$(document).on('click', self.idWrap + ' .btn-save', function(e) {
			e.preventDefault();

			var formEl = $(this).closest('form');
			var idForm = formEl.attr('id');

			var validateForm = true;

			if(idForm == 'general-config-form'){

				if(typeBlock == _HTML){
					var checkSyntax = self.editorHtmlBlock.getSession().getAnnotations();
					if(!$.isEmptyObject(checkSyntax)){
						toastr.error(nhMain.getLabel('loi_cu_phap_noi_dung_html_vui_long_kiem_tra_lai'));
						return;
					}

					var htmlContent = $.trim(self.editorHtmlBlock.getValue());
					$(formEl).find('input#html-content').val(htmlContent);
				}				

				if(typeBlock == _MENU){
					validateForm = self.blockMenu.validateDataBeforeSubmit();
				}

				if(typeBlock == _SLIDER){
					validateForm = self.blockSlider.validateDataBeforeSubmit();
				}

				if(typeBlock == _TAB_PRODUCT || typeBlock == _TAB_ARTICLE){
					validateForm = self.blockTab.validateDataBeforeSubmit();
				}
			}


			if(idForm == 'data-extend-form'){
				var validate = self.dataExtend.validateNormalData();
				if(!validate){
					$('.nav-tabs a[href="#tab-normal-data-extend"]').tab('show');
					return;
				}
				var normalData = self.dataExtend.getData();
				var normalDataJson = !$.isEmptyObject(normalData) ? JSON.stringify(normalData) : null;

				$(formEl).find('input#input-normal-data-extend').val(normalDataJson);
				// validate json data extend
				var checkSyntax = self.editorDataExtend.getSession().getAnnotations();
				if(!$.isEmptyObject(checkSyntax)){
					toastr.error(nhMain.getLabel('du_lieu_mo_rong_chua_chinh_xac_vui_long_kiem_tra_lai'));
					$('.nav-tabs a[href="#tab-json-data-extend"]').tab('show');
					return;
				}

				var dataValue = $.trim(self.editorDataExtend.getValue());
				var dataExtend = dataValue.length > 0 ? JSON.stringify(JSON.parse(self.editorDataExtend.getValue())) : null;
				$(formEl).find('input#input-data-extend').val(dataExtend);
			}

			if(idForm == 'modify-view-form'){
				var fileContent = $.trim(self.editorModifyView.getValue());
				$(formEl).find('input#input-view-file-content').val(fileContent);
			}

			
			if(validateForm){
				self.submitForm(formEl, $(this));
			}
		});		
	},
	initInput: function(){
		var self = this;
		ace.require('ace/ext/language_tools');

		// editor data extend
		if($('#editor-data-extend').length > 0){
			self.editorDataExtend = ace.edit('editor-data-extend', {
				mode: 'ace/mode/json'
			});

			var dataExtendValue = $(self.idWrap + ' form#data-extend-form').find('input#input-data-extend').val();
			var dataExtend = dataExtendValue.length > 0 ? JSON.stringify(JSON.parse(dataExtendValue), null, '\t') : '';
			self.editorDataExtend.setValue(dataExtend);
		}
		

		// editor modify view
		if($('#editor-modify-view').length > 0){
			self.editorModifyView = ace.edit('editor-modify-view', {
				mode: 'ace/mode/smarty',
				theme: 'ace/theme/monokai',
				enableBasicAutocompletion: true,
		        enableSnippets: true,
		        enableLiveAutocompletion: true,
		        showPrintMargin: false,
		        minLines: 40,
	        	maxLines: 40
			});

			var modifyViewValue = $(self.idWrap + ' form#modify-view-form').find('input#input-view-file-content').val();
			self.editorModifyView.setValue(modifyViewValue);


			// show full screen
			$(document).on('click', '[nh-btn="view-full-screen-editor"]', function(e) {
				if(!$('#editor-modify-view').hasClass('ace_editor')) return;
				$('#editor-modify-view').addClass('full-screen-editor');
			});

			$(document).on('keydown', function(e) {
				if($('#editor-modify-view').hasClass('full-screen-editor') && e.key == 'Escape'){
					$('#editor-modify-view').removeClass('full-screen-editor');
				};
			});
		}
		
		// editor different
	 	//self.editorDifferent = new AceDiff({
		// 	element: '.editor-different',
		// 	left: {content: ``},
		// 	right: {content: ``}
		// });

	    if($('input[nh-number-step]').length > 0){
	    	var step = $('input[nh-number-step]').attr('nh-number-step');
	    	var min = $('input[nh-number-step]').attr('data-min');

	    	nhMain.input.touchSpin.init($('input[nh-number-step]'), {
				max: 9999999999,
				step: step > 0 ? step : 1,
				min: min > 0 ? min : 0
			});
	    }
		
		$('.kt-selectpicker').selectpicker();

		self.selectMedia.init();

		// dừng event enter ở input submit form
		$(document).on('keydown', 'input[type="text"]', function(e) {			
			if (e.key == 'Enter') e.preventDefault();
		});	
	},
	eventViewData: function(){
		var self = this;

		$(document).on('change', self.idWrap +  ' select#' + _DATA_TYPE, function(e) {
			self.loadViewSelectData($(this).val());
		});

		$(document).on('keyup keypress paste focus', self.idWrap + ' #product-suggest', function(e) {
			var markItems = [];
			$(`input[name="config[data_ids][]"]`).each(function( index ) {
			  	markItems.push(nhMain.utilities.parseInt($(this).val()));
			});
			
			nhMain.autoSuggest.basic({
				inputSuggest: '#product-suggest',
				url: adminPath + '/product/auto-suggest-normal',
				markItems: markItems,
			}, function(response){
				if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED && typeof(response.name) != _UNDEFINED){
					self.addDataSelected({
						id: response.id,
						name: response.name
					});
				}
			});
			
			if(e.type == 'focusin'){
				$(this).autocomplete('search', $(this).val());
			}
		});

		$(document).on('keyup keypress paste focus', self.idWrap + ' #article-suggest', function(e) {
			var markItems = [];
			$(`input[name="config[data_ids][]"]`).each(function( index ) {
			  	markItems.push(nhMain.utilities.parseInt($(this).val()));
			});

			nhMain.autoSuggest.basic({
				inputSuggest: '#article-suggest',
				url: adminPath + '/article/auto-suggest',
				markItems: markItems,
			}, function(response){
				if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED && typeof(response.name) != _UNDEFINED){
					self.addDataSelected({
						id: response.id,
						name: response.name
					});
				}
			});
			
			if(e.type == 'focusin'){
				$(this).autocomplete('search', $(this).val());
			}
		});

		$(document).on('click', self.idWrap + ' ' + self.idWrapDataSelected + ' .tagify__tag__removeBtn', function(e) {
			$(this).closest('.tagify__tag').remove();
		});

		$(document).on('change', self.idWrap +  ' select#view-file', function(e) {
			var file = $(this).val();
			if(file.length > 0){
				self.loadContentFileView(file);
			}
		});

		var validatorAddView;
		var formAddView = $('#add-view-form');

		$(document).on('click', self.idWrap +  ' #btn-add-view', function(e) {

			$('#add-view-modal input').val('');
			$('#add-view-modal').modal('show');
			
			validatorAddView = formAddView.validate({
				ignore: ':hidden',
				messages: {
					name_file: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
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
		});

		$(document).on('click', '#add-view-modal #btn-save-view', function(e) {

			if (validatorAddView.form()) {
				var formData = formAddView.serialize();
				nhMain.callAjax({
					url: formAddView.attr('action'),
					data: formData
				}).done(function(response) {
					KTApp.unblockPage();

				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	toastr.clear();
		        	var file = typeof(data.file) != _UNDEFINED ? data.file : '';

		            if (code == _SUCCESS) {
		            	toastr.info(message);
		            	if(file.length > 0){
		            		$('select#view-file').append('<option value="' + file + '" selected="selected">' + file + '</option>');
		            		$('select#view-file').selectpicker('refresh');

		            		self.loadContentFileView(file);
		            		$('#add-view-modal').modal('hide');
		            	}
		            } else {
		            	toastr.error(message);
		            }
				});
			}
		});

		$(document).on('click', self.idWrap +  ' #btn-delete-view', function(e) {
			var file = $('select#view-file').val();
			if(file == null || file.length == 0){
				toastr.error(nhMain.getLabel('khong_tim_thay_giao_dien_can_xoa'));
				return;
			}
			
			swal.fire({
		        title: nhMain.getLabel('xoa_giao_dien'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_giao_dien_nay'),
		        type: 'warning',
		        
		        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
		    		KTApp.blockPage(blockOptions);
		    		nhMain.callAjax({
						url: adminPath + '/template/block/delete-view/' + self.block_code,
						data:{
							view_file: file
						}
					}).done(function(response) {
						var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
					    if (code == _SUCCESS) {
			            	toastr.success(message);
			            	$('select#view-file option[value="'+ file +'"]').remove();
			            	$('select#view-file').selectpicker('refresh');

			            	self.loadContentFileView($('select#view-file').val());
			            } else {
			            	toastr.error(message);
			            }
					})
		    	}    	
		    });						
		});
	},
	loadViewSelectData: function(data_type = null){
		var self = this;

		$(self.idWrapViewData).html('');
		if(data_type.length > 0){
			nhMain.callAjax({
	    		async: true,
	    		dataType: 'html',
	    		data: {
	    			code: self.block_code,
	    			data_type: data_type
	    		},
				url: adminPath + '/template/block/load-view-select-data'
			}).done(function(response) {
				$(self.idWrapViewData).html(response);
				$(self.idWrapViewData + ' .kt-selectpicker').selectpicker();
			});
		}	
	},
	addDataSelected: function(item = {}, params = {}){
		var self = this;

		if($.isEmptyObject(item) || self.checkDataExist(item.id)) return;

		var nameInput = 'config[data_ids][]';
		if(typeof(params.data_name) != _UNDEFINED){
			nameInput = params.data_name;
		}

		var tagHtml = 
		'<span class="tagify__tag">\
            <x class="tagify__tag__removeBtn" role="button"></x>\
            <div><span class="tagify__tag-text">' + item.name + '</span></div>\
            <input name="'+ nameInput +'" value="' + item.id + '" type="hidden">\
        </span>';
		$(self.idWrap + ' ' + self.idWrapDataSelected).append(tagHtml);
	},
	checkDataExist: function(id = null){
		var self = this;
		var result = false;

		if($(self.idWrap + ' ' + self.idWrapDataSelected).find('input[value="'+ id +'"]').length > 0){
			result = true;
		}

		return result;
	},
	loadContentFileView: function(file = null){
		var self = this;

		KTApp.blockPage(blockOptions);
		nhMain.callAjax({
    		async: true,
    		dataType: 'json',
    		data: {
    			view_file: file,
    		},
			url: adminPath + '/template/block/load-content-file-view/' + self.block_code
		}).done(function(response) {
			KTApp.unblockPage();
			
			var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
        	var file_content = typeof(data.file_content) != _UNDEFINED ? data.file_content : '';
        	var path = data.path || '';
        	toastr.clear();
            if (code == _SUCCESS) {
            	$('#input-view-file-content').val(file_content);
            	self.editorModifyView.setValue(file_content);

            	if(typeof(nhViewLogFile) != _UNDEFINED && typeof(nhViewLogFile.btnViewElement) != _UNDEFINED){
            		nhViewLogFile.btnViewElement.attr('data-path', path);
            	}
            } else {
            	toastr.error(message);
            }
		});
	},
	submitForm: function(formEl = null, btn_save = null){
		var self = this;

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

			//show message and redirect page
		   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
        	toastr.clear();
            if (code == _SUCCESS) {
            	toastr.info(message);
            } else {
            	toastr.error(message);
            }

            self.logUpdate.initLoad = true;
		});
	},
	translateLabel: function(label = null, callback = null){
		var self = this;
		if(label == null || label.length == 0) return;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

		KTApp.blockPage(blockOptions);
		nhMain.callAjax({
    		async: true,
			url: adminPath + '/template/block/translate-label',
			data: {
				label: label
			}
		}).done(function(response) {
			KTApp.unblockPage();

		   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

        	callback(data);
		});
	},	
	dataExtend: {
		wrapNormal: $('#tab-normal-data-extend'),
		tableNormal: $('#table-normal-label'),
		tableLocale: $('#table-locale-label'),
		rowNormal: '',
		rowLocale: '',
		wrapJson: $('#tab-json-data-extend'),	
		init: function(){
			var self = this;
			if(self.wrapNormal.length == 0 || self.wrapJson.length == 0) return;
			if(self.tableLocale.length == 0) return;

			// self.rowNormal = self.tableNormal.find('tbody tr:first-child')[0].outerHTML;
			self.rowLocale = self.tableLocale.find('tbody tr:first-child')[0].outerHTML;

			self.events();
		},
		events: function(){
			var self = this;

			$(document).on('click', '#btn-add-locale-label', function(e) {
				self.addItem('locale')
			});

			$(document).on('click', '#btn-add-normal-label', function(e) {
				self.addItem('normal')
			});

			$(document).on('click', '[nh-delete="data-extend"]', function(e) {
				var wrapElement = $(this).closest('table.table');				
				if(wrapElement.length == 0) return;

				$(this).closest('tr').remove();
				if(wrapElement.find('tbody').find('tr').length == 0){
					var type = null;
					if(wrapElement.attr('id') == 'table-normal-label') type = 'normal';
					if(wrapElement.attr('id') == 'table-locale-label') type = 'locale';
					if(type == null) return;

					self.addItem(type);
				}				
			});

			$(document).on('click input propertychange paste', 'textarea[nh-input="value"]', function(e) {
  				var height = this.scrollHeight;
  				if(height > 200) height = 200;
  				height -= 13;
  				$(this).height(0).height(height);
			});

			$(document).on('click', '[nh-btn="data-extend-translate"]', function(e) {
				var langDefault = $(this).attr('nh-language-default');				
				if(typeof(langDefault) == _UNDEFINED || langDefault.length == 0) return;

				var tr = $(this).closest('tr');
				var inputTranslate = tr.find('textarea[nh-language="'+ langDefault +'"]');
				var label = inputTranslate.val();

				if(typeof(label) == _UNDEFINED || label.length == 0) return;

				nhBlockConfig.translateLabel(label, function(translates){
					$.each(translates, function(lang, text) {
						tr.find('textarea[nh-language="'+ lang+'"]').val(text);
					});

				});
			});
		},
		addItem: function(type = null){
			var self = this;

			var tableElement = null;
			var rowHtml = null;
			if(type == 'normal'){
				tableElement = self.tableNormal;
				rowHtml = self.rowNormal;
			}

			if(type == 'locale'){
				tableElement = self.tableLocale;
				rowHtml = self.rowLocale;
			}

			if(tableElement == null || tableElement.length == 0) return;
			if(rowHtml == null || rowHtml.length == 0) return;

			tableElement.find('tbody').append(rowHtml);
			var rowElement = tableElement.find('tbody tr:last-child');
			self.clearItem(rowElement);
		},
		clearItem: function(rowElement){
			var self = this;
			if(rowElement.length == 0) return;
			rowElement.find('input, textarea').val('');
		},
		validateNormalData: function(){
			var self = this;

			var validate = true;
			self.tableLocale.find('tbody tr').each(function(index) {
			  	var keyInput = $(this).find('input[nh-input="key"]');
			  	var valueInputs = $(this).find('textarea[nh-input="value"]');
			  	if(keyInput.length == 0 || valueInputs.length == 0) return;

			  	keyInput.removeClass('is-invalid');

			  	var key = $.trim(keyInput.val());

			  	valueInputs.each(function(index) {
			  		var valueInput = $(this);
			  		valueInput.removeClass('is-invalid');

			  		var value = $.trim(valueInput.val());

			  		if(key.length > 0 && value.length == 0){
				  		valueInput.addClass('is-invalid');
				  		validate = false;
				  	}

				  	if(key.length == 0 && value.length > 0){
				  		keyInput.addClass('is-invalid');
				  		validate = false;
				  	}
			  	});
			});

			self.tableNormal.find('tbody tr').each(function(index) {
			  	var keyInput = $(this).find('input[nh-input="key"]');
			  	var valueInput = $(this).find('textarea[nh-input="value"]');
			  	
			  	if(keyInput.length == 0 || valueInput.length == 0) return;

			  	keyInput.removeClass('is-invalid');
			  	valueInput.removeClass('is-invalid');

			  	var key = $.trim(keyInput.val());
			  	var value = $.trim(valueInput.val());

			  	if(key.length == 0 && value.length == 0) return;
			  	if(key.length > 0 && value.length == 0){
			  		valueInput.addClass('is-invalid');
			  		validate = false;
			  	}

			  	if(key.length == 0 && value.length > 0){
			  		keyInput.addClass('is-invalid');
			  		validate = false;
			  	}
			});

			return validate;
		},
		getData: function(){
			var self = this;

			var data = {
				locale: {},
				normal: {}
			};
			self.tableLocale.find('tbody tr').each(function(index) {
			  	var keyInput = $(this).find('input[nh-input="key"]');
			  	var valueInputs = $(this).find('textarea[nh-input="value"]');			  	
			  	if(keyInput.length == 0 || valueInputs.length == 0) return;

			  	keyInput.removeClass('is-invalid');

			  	var key = $.trim(keyInput.val());
			  	valueInputs.each(function(index) {
			  		var lang = $(this).attr('nh-language');
			  		var value = $.trim($(this).val());
					
					if(lang == _UNDEFINED || lang.length == 0) return;
					if(typeof(data.locale[lang]) == _UNDEFINED) data.locale[lang] = {};
					data.locale[lang][key] = value;
			  	});


			});

			self.tableNormal.find('tbody tr').each(function(index) {
			  	var keyInput = $(this).find('input[nh-input="key"]');
			  	var valueInput = $(this).find('textarea[nh-input="value"]');

			  	if(keyInput.length == 0 || valueInput.length == 0) return;

			  	var key = $.trim(keyInput.val());
			  	var value = $.trim(valueInput.val());

			  	if(key.length == 0 && value.length == 0) return;
			  	
			  	data.normal[key] = value;
			});

			return data;
		}
	},
	typeLoadBlock: {
		idWrap: '#wrap-config-type-load',
		init: function(){
			var self = this;

			self.initInput();

			$(document).on('change', 'select#type-load', function(e) {
				var typeLoad = $(this).val();
				if(typeLoad.length > 0){
					nhMain.callAjax({
			    		async: true,
			    		dataType: 'html',
						url: adminPath + '/template/block/config-type-load/' + typeLoad
					}).done(function(response) {
						$(self.idWrap).html(response);
						self.initInput();	
					});
				}
			});
		},
		initInput: function(){
			var self = this;
			var timeout = $('#timeout-select').val() > 0 ? nhMain.utilities.parseInt($(self.idWrap + ' #timeout-select').val()) : 5;
			$(self.idWrap + ' #timeout-select').ionRangeSlider({
	            grid: true,
	            min: 1,
	            max: 21,
	            from: timeout,
	            step: 2
	        });

	        nhMain.input.inputMask.init($('input.number-input'), 'number');
		}
	},
	managerItem:{
		idWrap: '#wrap-item-config',
		classItem: '.wrap-item',
		itemHtml: null,
		init: function(options = {}){
			var self = this;
			self.itemHtml = $(self.idWrap + ' ' + self.classItem + ':first-child').length ? $(self.idWrap + ' ' + self.classItem + ':first-child')[0].outerHTML : '';

			$(self.idWrap).find(self.classItem).each(function(index) {
			  	self.initInputItem($(this));
			});

			self.clearAllErrorItem();

			$(document).on('click', '#add-item', function(e) {
				self.addNewItem();
			});

			$(document).on('click', self.idWrap + ' .btn-delete-item', function(e) {
				var item = $(this).closest(self.classItem);
				swal.fire({
			        title: nhMain.getLabel('xoa_ban_ghi'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_ban_ghi_nay'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		item.remove();
			    	}
			    });
			});

			$(document).on('click', self.idWrap + ' .btn-toggle-item', function(e) {
				var item = $(this).closest(self.classItem);

				var hidden = item.hasClass('kt-portlet--collapse');

				if(hidden){
					item.find('.kt-portlet__body').slideDown();
					item.removeClass('kt-portlet--collapse');
				}else{
					item.find('.kt-portlet__body').slideUp();
					item.addClass('kt-portlet--collapse');
				}
			});

			$(document).on('keyup', self.idWrap + ' ' + self.classItem +  ' input.item-name', function(e) {
				var name = $(this).val();
				if(name.length == 0){
					name = 'New Item';
				}

				var item = $(this).closest(self.classItem);
				item.find('.kt-portlet__head-title').text(name);
			});		

			$(document).on('click', '.btn-clear-image', function(e) {
		    	var wrap = $(this).closest('.kt-avatar');
		    	wrap.find('.kt-avatar__holder').css('background-image', '');
		    	wrap.removeClass('kt-avatar--changed');
		    	wrap.find('input[type="hidden"]').val('');
		    });  	
		},
		initInputItem: function(item = null){
			var self = this;
			if(item == null || item.length == 0) return;

			var indexItem = $(self.classItem).index(item[0]);

			// replace name input
			self.replaceNameInput(item);

			// replace data-url and id of input select image
			item.find('[btn-select-media-block]').each(function( index ) {
				var dataSrc = $(this).data('src');
				$(this).data('src', dataSrc + '_' + indexItem);
			});

			var inputImage = item.find('.input-select-image');			
			var _id = inputImage.attr('id');

			inputImage.attr('id', _id + '_' + indexItem);
			item.find('[block-preview-image]').attr('block-preview-image', _id + '_' + indexItem);
			item.find('[block-image-source]').attr('block-image-source', _id + '_' + indexItem);

			// init select single image
			nhBlockConfig.selectMedia.init({}, item);

			// init selectpicker
			item.find('.kt-selectpicker').each(function(index) {
			  	$(this).selectpicker();
			});

			// sortable item menu
			$(self.idWrap).sortable({
                connectWith: '.kt-portlet__head',
                items: '.kt-portlet' + self.classItem,
                opacity: 0.8,
                handle : '.kt-portlet__head',
                coneHelperSize: true,
                placeholder: 'kt-portlet--sortable-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                helper: 'clone',
                tolerance: 'pointer',
                forcePlaceholderSize: !0,
                cancel: '.kt-portlet--sortable-empty',
                revert: 250,
                update: function( event, ui ) {
                	$(self.idWrap).find(self.classItem).each(function(index) {
					  	self.replaceNameInput($(this));
					});
                }
            });
		},
		replaceNameInput: function(item){
			var self = this;
			var indexItem = item.index();
			$('input, select, textarea', item).each(function () {
				if (typeof($(this).attr('data-name')) == _UNDEFINED) return;

				var name = 'config[item]['+ indexItem +'][' + $(this).attr('data-name') + ']';
				if(typeof($(this).attr('data-mutiple')) != _UNDEFINED){
					name += '[]';
				}

				$(this).attr('name', name);
				
			});
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

			item.find('.kt-portlet__head-title').text('New Item');

			$('input, select, textarea', item).each(function () {
				var typeInput = $(this).attr('type');
				if(typeInput == 'checkbox'){
					$(this).prop('checked', false);
				}else{
					$(this).val('');
				}
			});

			item.find('#wrap-data-tab').html('');

			var wrapImage = item.find('.kt-avatar');
			wrapImage.removeClass('kt-avatar--changed');
			wrapImage.find('.kt-avatar__holder').css('background-image', '');
		},
		showErrorItem: function(item = null){
			var self = this;
			if(item == null || item == _UNDEFINED || item.length == 0) return;
			item.addClass('item-error');
			KTUtil.scrollTo(item[0], nhMain.validation.offsetScroll);
		},
		clearAllErrorItem: function(){
			var self = this;
			$(nhBlockConfig.managerItem.idWrap).find(nhBlockConfig.managerItem.classItem).removeClass('item-error');
		},
		addDataSelectedAfterSuggest: function(item = {}, params = {}, inputSuggest = null){
			var self = this;
			
			if($.isEmptyObject(item)) return;
			if(self.checkDataExistAfterSuggest(item.id)) return;
			if(inputSuggest == null || inputSuggest.length == 0) return;
			
			var nameInput = 'config[data_ids][]';
			if(typeof(params.data_name) != _UNDEFINED){
				nameInput = params.data_name;
			}

			var tagHtml = 
			'<span class="tagify__tag">\
	            <x class="tagify__tag__removeBtn" role="button"></x>\
	            <div><span class="tagify__tag-text">' + item.name + '</span></div>\
	            <input name="'+ nameInput +'" value="' + item.id + '" type="hidden">\
	        </span>';

			inputSuggest.closest(self.classItem).find(nhBlockConfig.idWrapDataSelected).append(tagHtml);
		},
		checkDataExistAfterSuggest: function(id = null, inputSuggest = null){
			var self = this;			
			if(inputSuggest == null || inputSuggest.length == 0) return false;

			if(inputSuggest.closest(self.classItem).find(nhBlockConfig.idWrapDataSelected).find('input[value="'+ id +'"]').length > 0){
				return true;
			}

			return false;
		},
	},
	blockMenu:{
		idWrapListUrl: '#wrap-list-url',
		idWrapCategories: '#wrap-categories',
		idWrapConfigSubMenu: '#wrap-config-sub-menu',
		idWrapDataSubMenu: '#wrap-data-sub-menu',
		editorDataExtendSubMenu: null,
		init: function(){
			var self = this;

			$(nhBlockConfig.managerItem.idWrap).find(nhBlockConfig.managerItem.classItem).each(function(index) {
				if($(this).find('#editor-data-extend-sub-menu').length > 0){
					var elementEditor = $(this).find('#editor-data-extend-sub-menu')[0];
					var editorDataExtendSubMenu = ace.edit(elementEditor, {
						mode: 'ace/mode/json'
					});

					var dataExtendValue = $(this).find('input#input-data-extend-sub-menu').val();
					var dataExtend = dataExtendValue.length > 0 ? JSON.stringify(JSON.parse(dataExtendValue), null, '\t') : '';
					editorDataExtendSubMenu.setValue(dataExtend);
				}
			});

			$(document).on('change', nhBlockConfig.managerItem.idWrap + ' select#menu-type', function(e) {
				var type = $(this).val();
				if(type == _CUSTOM){
					$(nhBlockConfig.managerItem.classItem + ' ' + self.idWrapListUrl).removeClass('d-none');
				}else{
					$(this).closest(nhBlockConfig.managerItem.classItem).find(self.idWrapListUrl).addClass('d-none');
					$(this).closest(nhBlockConfig.managerItem.classItem).find(self.idWrapListUrl).find('input').val('');
				}

				var itemMenu = $(this).closest(nhBlockConfig.managerItem.classItem);

				// load dropdown category
				if(itemMenu != null && itemMenu.length){
					itemMenu.find(self.idWrapCategories).html('');
				}
				
				if(type.indexOf('category') > -1){
					self.loadDropdownCategories(type, itemMenu);
				}
			});

			$(document).on('change', nhBlockConfig.managerItem.idWrap + ' input#has-sub-menu', function(e) {
				var has_sub = $(this).is(':checked') ? true : false;

				var itemMenu = $(this).closest(nhBlockConfig.managerItem.classItem);
				var wrapSubMenu = itemMenu.find(self.idWrapConfigSubMenu).toggleClass('d-none', !has_sub);
							

				if(!has_sub){
					wrapSubMenu.find('input[type="checkbox"]').prop('checked', false);
					wrapSubMenu.find('input#input-data-extend-sub-menu').val('');

					if(itemMenu.find('#editor-data-extend-sub-menu').length > 0){
						ace.edit('editor-data-extend-sub-menu', {
							mode: 'ace/mode/json'
						}).setValue('');
					}					
				}			
			});

			$(document).on('change', nhBlockConfig.managerItem.idWrap + ' select#sub-menu-type', function(e) {
				var type = $(this).val();
				var itemMenu = $(this).closest(nhBlockConfig.managerItem.classItem);
				if(type.indexOf('category') > -1){
					self.loadListCheckBoxCategories(type, itemMenu);
				}

				if(type == _CUSTOM){
					self.loadEditorDataExtendSubMenu(itemMenu);
				}
			});
		},
		loadDropdownCategories: function(type = null, itemMenu = null){
			var self = this;
			nhMain.callAjax({
	    		async: true,
	    		data:{
	    			type: type
	    		},
	    		dataType: 'html',
				url: adminPath + '/template/block/load-dropdown-categories'
			}).done(function(response) {
				if(itemMenu != null && itemMenu.length){
					itemMenu.find(self.idWrapCategories).html(response);

					nhBlockConfig.managerItem.replaceNameInput(itemMenu);
					itemMenu.find('.kt-selectpicker').each(function(index) {
					  	$(this).selectpicker();
					});
				}
			});
		},
		loadListCheckBoxCategories: function(type = null, itemMenu){
			var self = this;
			nhMain.callAjax({
	    		async: true,
	    		data:{
	    			type: type
	    		},
	    		dataType: 'html',
				url: adminPath + '/template/block/load-checkbox-categories'
			}).done(function(response) {
				if(itemMenu != null && itemMenu.length){
					itemMenu.find(self.idWrapDataSubMenu).html(response);

					nhBlockConfig.managerItem.replaceNameInput(itemMenu);
					itemMenu.find('.kt-selectpicker').each(function(index) {
					  	$(this).selectpicker();
					});
				}
			});
		},
		loadEditorDataExtendSubMenu: function(itemMenu){
			var self = this;
			nhMain.callAjax({
	    		async: true,
	    		dataType: 'html',
				url: adminPath + '/template/block/load-editor-data-extend-sub-menu'
			}).done(function(response) {
				if(itemMenu != null && itemMenu.length){
					itemMenu.find(self.idWrapDataSubMenu).html(response);
					if(itemMenu.find('#editor-data-extend-sub-menu').length > 0){
						var elementEditor = itemMenu.find('#editor-data-extend-sub-menu')[0];
						ace.edit(elementEditor, {
							mode: 'ace/mode/json'
						});
					}
					nhBlockConfig.managerItem.replaceNameInput(itemMenu);
				}
			});
		},
		validateDataBeforeSubmit: function(){
			var self = this;

			nhBlockConfig.managerItem.clearAllErrorItem();

			var validateData = true;
			$(nhBlockConfig.managerItem.idWrap).find(nhBlockConfig.managerItem.classItem).each(function(index) {
				var _menuItem = $(this);

				var typeMenu = _menuItem.find('select#menu-type').val();
				if(typeMenu.length == 0){
					toastr.error(nhMain.getLabel('vui_long_chon_loai_menu'));
					nhBlockConfig.managerItem.showErrorItem(_menuItem);
					validateData = false;
					return false;
				}

				var hasSubMenu = _menuItem.find('input#has-sub-menu').is(':checked') ? true : false;
				var viewItem = _menuItem.find('select[data-name="view_item"]').val();
				var typeSubMenu = _menuItem.find('select[data-name="type_sub_menu"]').val();

				if(hasSubMenu && viewItem.length == 0){
					toastr.error(nhMain.getLabel('vui_long_chon_giao_dien_menu_phu'));
					nhBlockConfig.managerItem.showErrorItem(_menuItem);
					validateData = false;
					return false;
				}

				if(hasSubMenu && typeSubMenu.length == 0){
					toastr.error(nhMain.getLabel('vui_long_chon_du_lieu_menu_phu'));
					nhBlockConfig.managerItem.showErrorItem(_menuItem);
					validateData = false;
					return false;
				}

				_menuItem.find('input.required').each(function(index) {
					if($(this).val().length == 0){
						toastr.error(nhMain.getLabel('vui_long_chon_nhap_thong_tin_menu'));					
						nhBlockConfig.managerItem.showErrorItem(_menuItem);
						validateData = false;
						return false;
					}
				});

			  	if(_menuItem.find('#editor-data-extend-sub-menu').length > 0){
			  		var elementEditor = _menuItem.find('#editor-data-extend-sub-menu')[0];
					var editorDataExtendSubMenu = ace.edit(elementEditor, {
						mode: 'ace/mode/json',
						minLines: 40,
	        			maxLines: 40
					});

					var checkSyntax = editorDataExtendSubMenu.getSession().getAnnotations();
					if(!$.isEmptyObject(checkSyntax)){
						toastr.error(nhMain.getLabel('loi_cu_phap_du_lieu_tuy_bien_sub_menu'));
						nhBlockConfig.managerItem.showErrorItem(_menuItem);
						validateData = false;
						return false;
					}

					var dataValue = $.trim(editorDataExtendSubMenu.getValue());
					var dataExtend = dataValue.length > 0 ? JSON.stringify(JSON.parse(editorDataExtendSubMenu.getValue())) : null;
					_menuItem.find('input#input-data-extend-sub-menu').val(dataExtend);
				}
			});

			return validateData;
		}		
	},
	blockSlider: {
		init: function(){
			if($('#editor-refer-code').length == 0) return;
			var editorRefer = ace.edit('editor-refer-code', {
				mode: 'ace/mode/json'
			});

			$(document).on('click input propertychange paste', 'textarea', function(e) {
  				var height = this.scrollHeight;
  				if(height > 200) height = 200;
  				height -= 16;
  				$(this).height(0).height(height);
			});
		},
		validateDataBeforeSubmit: function(){
			var self = this;

			nhBlockConfig.managerItem.clearAllErrorItem();

			var validateData = true;
			$(nhBlockConfig.managerItem.idWrap).find(nhBlockConfig.managerItem.classItem).each(function(index) {
				var _sliderItem = $(this);

				_sliderItem.find('input.required').each(function(index) {
					if($(this).val().length == 0){
						toastr.error(nhMain.getLabel('vui_long_chon_nhap_thong_tin_menu'));
						nhBlockConfig.managerItem.showErrorItem(_sliderItem);
						validateData = false;
						return false;
					}
				});
			});

			return validateData;
		}	
	},
	blockHtml: {
		init: function(){
			nhBlockConfig.editorHtmlBlock = ace.edit('editor-html', {
				mode: 'ace/mode/smarty',
				theme: 'ace/theme/monokai',
				enableBasicAutocompletion: true,
		        enableSnippets: true,
		        enableLiveAutocompletion: true,
		        showPrintMargin: false,
		        minLines: 40,
	        	maxLines: 40
			});

			var htmlContent = $('#html-content').val();
			nhBlockConfig.editorHtmlBlock.setValue(htmlContent);

			// show full screen
			$(document).on('click', '[nh-btn="full-screen-html-editor"]', function(e) {
				if(!$('#editor-html').hasClass('ace_editor')) return;
				$('#editor-html').addClass('full-screen-editor');
			});

			$(document).on('keydown', function(e) {
				if($('#editor-html').hasClass('full-screen-editor') && e.key == 'Escape'){
					$('#editor-html').removeClass('full-screen-editor');
				};
			});
		}
	},
	selectMedia:{
		type : null,
		copy: false,
		preview: false,
		init: function(params = {}, wrapElement = null){
			var self = this;

			if($('[btn-select-media-block]').length == 0) return false;

			if(typeof(params.copy) != _UNDEFINED && params.copy){
				self.copy = true;
			}

			if(typeof(params.preview) != _UNDEFINED && params.preview){
				self.preview = true;
			}

			$('[btn-select-media-block]').fancybox({
			   	closeExisting: true,
			   	iframe : {
			   		preload : false
			   	}
			});


			if(wrapElement == null || typeof(wrapElement) == _UNDEFINED || wrapElement.length == 0)  wrapElement = $(document);
			wrapElement.on('click', '[btn-select-media-block]', function(e) {
				e.preventDefault();
				self.resetOption();

				self.type = $(this).attr('btn-select-media-block');
				if($(this).attr('action') != _UNDEFINED && $(this).attr('action') == 'copy'){
					self.copy = true;
					copyMedia = true;
				}

				if($(this).attr('action') != _UNDEFINED && $(this).attr('action') == 'preview'){
					self.preview = true;
					previewMedia = true;
				}

				$(window).on('message', self.onSelectImage);
		    });
		},
		resetOption: function(){
			var self = this;

			self.copy = false;
			self.preview = false;

			copyMedia = false;
			previewMedia = false;
		},
		onSelectImage: function(e){
			var self = nhBlockConfig.selectMedia;

			var event = e.originalEvent;
		   	if(event.data.sender === 'myfilemanager'){
		      	if(event.data.field_id){
		      		var field_id = event.data.field_id;
		      		var inputImage = $('#' + field_id);
		      		var inputSource = $('[block-image-source="' + field_id + '"]');

		      		var imageUrl = typeof(event.data.url) != _UNDEFINED ? event.data.url : null;			      		   	

					if(self.preview){
						self.previewImage(imageUrl, field_id);
					}
					
					// replace url image before set value for input
					if(isArray(imageUrl)) imageUrl = imageUrl[0];
					imageUrl = imageUrl.replace(cdnUrl, '');


					// set value for input					
		      		if(inputImage.length > 0) inputImage.val(imageUrl);

		      		if(inputSource.length > 0) inputSource.val(self.type);

		      		if(self.copy){
		      			if(self.type == 'cdn') {
		      				imageUrl = '{CDN_URL}' + imageUrl;
		      			}

		      			if(self.type == 'template'){
		      				imageUrl = '{URL_TEMPLATE}' + imageUrl.replace(templatePath, '');
		      			}

						self.copyImage(imageUrl);
					}
				
					$.fancybox.close();
					$(window).off('message', self.onSelectImage);
		      	}
		   	}
		},
		copyImage: function(imageUrl = null, type = null){
			var self = this;

			var inputTmp = $('<input>');
			$('body').append(inputTmp);
			inputTmp.val(imageUrl).select();
			document.execCommand('copy');
			inputTmp.remove();

			toastr.success(nhMain.getLabel('da_copy_duong_dan_anh'));
		},
		previewImage: function(imageUrl = null, field_id = null){
			$('[block-preview-image="'+ field_id +'"]').find('.kt-avatar__holder').css('background-image', 'url("' + imageUrl + '")');
			$('[block-preview-image="'+ field_id +'"]').addClass('kt-avatar--changed');
		}
	},
	blockTab: {
		idWrap: '#wrap-item-config',
		classItem: '.wrap-item',
		wrapDataTab: '#wrap-data-tab',
		init: function(){
			var self = this;

			$(document).on('keyup keypress paste focus', self.idWrap + ' [input-suggest="product"]', function(e) {
				var itemMenu = $(this).closest(nhBlockConfig.blockTab.classItem);
				var inputElement = $(this);
				var nameInput = $(this).attr('name');

				var markItems = [];
				$(`input[name="config[item][${itemMenu.index()}][data_ids][]"]`).each(function( index ) {
				  	markItems.push(nhMain.utilities.parseInt($(this).val()));
				});

				nhMain.autoSuggest.basic({
					inputSuggest: '[name="'+ nameInput +'"]',
					url: adminPath + '/product/auto-suggest-normal',
					markItems: markItems,
				}, function(response){
					if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED && typeof(response.name) != _UNDEFINED){
						nhBlockConfig.managerItem.addDataSelectedAfterSuggest({
							id: response.id,
							name: response.name
						},{
							data_name: `config[item][${itemMenu.index()}][data_ids][]`
						},
						inputElement);
					}
				});
				
				if(e.type == 'focusin'){
					$(this).autocomplete('search', $(this).val());
				}
			});

			$(document).on('keyup keypress paste focus', self.idWrap + ' [input-suggest="article"]', function(e) {
				var itemMenu = $(this).closest(nhBlockConfig.blockTab.classItem);
				var inputElement = $(this);
				var nameInput = $(this).attr('name');

				var markItems = [];
				$(`input[name="config[item][${itemMenu.index()}][data_ids][]"]`).each(function( index ) {
				  	markItems.push(nhMain.utilities.parseInt($(this).val()));
				});

				nhMain.autoSuggest.basic({
					inputSuggest: '[name="'+ nameInput +'"]',
					url: adminPath + '/article/auto-suggest',
					markItems: markItems,
				}, function(response){
					if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED && typeof(response.name) != _UNDEFINED){
						nhBlockConfig.managerItem.addDataSelectedAfterSuggest({
							id: response.id,
							name: response.name
						},{
							data_name: `config[item][${itemMenu.index()}][data_ids][]`
						},
						inputElement);
					}
				});
				
				if(e.type == 'focusin'){
					$(this).autocomplete('search', $(this).val());
				}
			});

			$(document).on('click', `${self.idWrap} ${self.idWrapDataSelected} .tagify__tag__removeBtn`, function(e) {
				$(this).closest('.tagify__tag').remove();
			});

			$(document).on('change', `${nhBlockConfig.blockTab.idWrap} select#type_tag`, function(e) {
				var type = $(this).val();
				var itemMenu = $(this).closest(nhBlockConfig.blockTab.classItem);
				self.loadViewTag(type, itemMenu);
			});
		},
		loadViewTag: function(type = null, itemMenu){
			var self = this;
			nhMain.callAjax({
	    		async: true,
	    		data:{
	    			type: type
	    		},
	    		dataType: 'html',
				url: adminPath + '/template/block/load-view-data-for-tab'
			}).done(function(response) {
				if(itemMenu != null && itemMenu.length){
					itemMenu.find(self.wrapDataTab).html(response);

					nhBlockConfig.managerItem.replaceNameInput(itemMenu);
					itemMenu.find('.kt-selectpicker').each(function(index) {
					  	$(this).selectpicker();
					});
				}
			});
		},
		validateDataBeforeSubmit: function(){
			var self = this;

			nhBlockConfig.managerItem.clearAllErrorItem();

			var validateData = true;
			$(nhBlockConfig.managerItem.idWrap).find(nhBlockConfig.managerItem.classItem).each(function(index) {
				var _tabItem = $(this);

				_tabItem.find('input.required').each(function(index) {
					if($(this).val().length == 0){
						toastr.error(nhMain.getLabel('vui_long_chon_nhap_thong_tin'));					
						nhBlockConfig.managerItem.showErrorItem(_tabItem);
						validateData = false;
						return false;
					}
				});
			});

			return validateData;
		},
		replaceNameInput: function(item){
			var self = this;
			var indexItem = item.index();
			$('input, select, textarea', item).each(function () {
				if (typeof($(this).attr('data-name')) == _UNDEFINED) return;

				var name = 'config[item]['+ indexItem +'][' + $(this).attr('data-name') + ']';
				if(typeof($(this).attr('data-mutiple')) != _UNDEFINED){
					name += '[]';
				}

				$(this).attr('name', name);
				
			});
		}
	},
	logUpdate: {
		tabElement: $('#tab-logs'),
		wrapElement: $('[nh-wrap="logs"]'),
		initLoad: true,
		init: function(){
			var self = this;

			if(self.tabElement.length == 0) return;

			self.events();
		},
		events: function(){
			var self = this;

			$('a[data-toggle="tab"][href="#tab-logs"]').on('shown.bs.tab', function (e) {
				if(self.initLoad) self.loadLog();				
			});

			self.tabElement.on('click', '.kt-pagination .pages-link:not(.disabled)', function(e) {
                var page = $(this).data('page');
                self.loadLog(page);
            });


			self.tabElement.on('click', '[nh-log="rollback"]', function(e) {
				var log_id = $(this).data('id') || '';
				if(log_id == '') {
					toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
					return;
				}

				swal.fire({
			        title: nhMain.getLabel('phuc_hoi_cau_hinh'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_phuc_hoi_lai_cau_hinh_ban_ghi_nay'),
			        type: 'warning',
			        
			        confirmButtonText: '<i class="fa fa-history"></i>' + nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-warning',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		KTApp.blockPage(blockOptions);
						nhMain.callAjax({
							url: adminPath + '/template/block/rollback-log/' + nhBlockConfig.block_code,
							data: {
								log_id: log_id
							},
						}).done(function(response) {
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
			    	}
			    });
			});
		},
		loadLog: function(page = 1){
			var self = this;

			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
				url: adminPath + '/template/block/logs/' + nhBlockConfig.block_code,
				data: {
					page: page
				},
				dataType: 'html'
			}).done(function(response) {
				KTApp.unblockPage();

			   	self.tabElement.attr('nh-init', 'loaded');
			   	self.wrapElement.html(response);

			   	self.initLoad = false;
			});
		}
	}
}