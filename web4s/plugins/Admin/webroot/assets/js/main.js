"use strict";

String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

var nhMain = {
	lang: $('html').attr('lang'),
	init: function(){
		var self = this;
    	
	 	self.nhEvents.init();	 	
	},
	callAjax: function(params = {}){
		var self = this;

		var options = {
			headers: {
		        'X-CSRF-Token': csrfToken
		    },
	        async: typeof(params.async) != _UNDEFINED ? params.async : true,
	        url: typeof(params.url) != _UNDEFINED ? params.url : '',
	        type: typeof(params.type) != _UNDEFINED ? params.type : 'POST',
	        dataType: typeof(params.dataType) != _UNDEFINED ? params.dataType : 'json',
	        data: typeof(params.data) != _UNDEFINED ? params.data : {},    
	        cache: typeof(params.cache) != _UNDEFINED ? params.cache : false
	    };

	    if(typeof(params.processData) != _UNDEFINED){
	    	options.processData = params.processData;
	    }

	    if(typeof(params.contentType) != _UNDEFINED){
	    	options.contentType = params.contentType;
	    }

		var ajax = $.ajax(options).fail(function(jqXHR, textStatus, errorThrown){
			if(jqXHR.status == 403){
				toastr.error(nhMain.getLabel('het_phien_lam_viec_vui_long_dang_nhap_lai'));
				location.reload();
				return false;
			}

	    	if(typeof(params.not_show_error) == _UNDEFINED){
	    		toastr.error(textStatus + ': ' + errorThrown);
	    	}
		});
	    return ajax;
	},
	initSubmitForm: function(formEl = null, btn_save = null){
		var self = this;
		// show loading
		KTApp.progress(btn_save);
		KTApp.blockPage(blockOptions);
		
		// params call ajax
		var isUpdate = typeof(btn_save.data('update')) != _UNDEFINED ? btn_save.data('update') : 0;
		var urlRedirect = typeof(btn_save.data('link')) != _UNDEFINED ? btn_save.data('link') : '';

		self.attributeInput.setValueBeforeSubmit(formEl);
		var formData = formEl.serialize();
		
		self.callAjax({
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
            	if(typeof(data.id) != _UNDEFINED && isUpdate == 1 && urlRedirect.length > 0){
            		urlRedirect = urlRedirect + '/' +  data.id
            	}            	

            	if(urlRedirect.length > 0){
            		window.location.href = urlRedirect;
            	}else{
            		location.reload();
            	}
            } else {
            	toastr.error(message);
            }
		});
	},
	nhEvents: {
		init: function(){
			var self = this;			

			$('body').on('click', function (e) {
			    $('[data-toggle="popover"]').each(function () {
			        //the 'is' for buttons that trigger popups
			        //the 'has' for icons within a button that triggers a popup
			        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
			            $(this).popover('hide');
			        }
			    });
			});

		    $('input[type=text][maxlength]').maxlength({
	    		alwaysShow: true,
	    		placement: 'top-right',
	    		validate: true,
	            warningClass: 'kt-badge kt-badge--success kt-badge--rounded kt-badge--inline',
	            limitReachedClass: 'kt-badge kt-badge--warning kt-badge--rounded kt-badge--inline'
	        });

			self.activeMenu();
			self.stickyMenu();
			self.shortcut();
		},
		copy: function(text = null, callback){
			var self = this;
			
			var inputTmp = $('<input>');
			$('body').append(inputTmp);
			inputTmp.val(text).select();
			document.execCommand('copy');
			inputTmp.remove();

			if (typeof(callback) != 'function') {
		        callback = function () {};
		    }

		    callback();
		},
		activeMenu: function(){
			var self = this;
			
			var pathMenu = $('body').attr('path-menu');
		 	if(!nhMain.utilities.notEmpty(pathMenu)) return false;

		 	var activeMenu = $('.kt-menu__item a[path-menu="' + pathMenu + '"]');
		 	
		 	// menu mobile active
		 	var liElement = activeMenu.closest('li.kt-menu__item');
	 		liElement.addClass('kt-menu__item--active');
	 		activeMenu.parents('li.kt-menu__item--submenu').addClass('kt-menu__item--open');

	 		// menu desktop active level 1
	 		if(liElement.closest('.kt-menu__submenu').length > 0){
	 			liElement = liElement.closest('.kt-menu__submenu').closest('li.kt-menu__item');
	 			liElement.addClass('kt-menu__item--active');
	 		}

	 		// level 2
	 		if(liElement.closest('.kt-menu__submenu').length > 0){
	 			liElement = liElement.closest('.kt-menu__submenu').closest('li.kt-menu__item');
	 			liElement.addClass('kt-menu__item--active');
	 		}
		},
		stickyMenu: function() {
			var self = this;

			var sticky = $('.sticky-menu');
			if(!nhMain.utilities.notEmpty(sticky)) return false;

			var startScroll = $(window).scrollTop();
			$(window).scroll(function(){
			    var scroll = $(window).scrollTop();

			    scroll > startScroll ? sticky.addClass('scroll-down') : startScroll = scroll;
			    if (scroll == 0) {
			    	sticky.removeClass('scroll-down');
			    }
			})
		},
		shortcut: function(){
			var self = this;

			$(document).on('keydown', function (e) {
			    var shortcut = e.keyCode;
			    var disabled = $('[shortcut="' + shortcut + '"]').attr('disabled');
			    if ($('[shortcut="' + shortcut + '"]').length > 0 && typeof disabled == "undefined") {
			        if ($('[shortcut="' + shortcut + '"]').is('[type=text]')) {
			            $('[shortcut="' + shortcut + '"]').focus();
			        } else {
			            $('[shortcut="' + shortcut + '"]').trigger('click');
			        }
			        e.preventDefault();
			    }
			});
		},

	},
	attributeInput: {
		init: function(params = {}){
			var self = this;

			var wrap = typeof(params.wrap) != _UNDEFINED && params.wrap.length > 0 ? params.wrap : $('body');
			var acceptInit = typeof(params.accept_init) != _UNDEFINED ? Boolean(params.accept_init) : false;

			var selectorInput = '[input-attribute]:not(.no-init)';
			if(acceptInit){
				selectorInput = '[input-attribute]';
			}

			wrap.find(selectorInput).each(function(index) {
				var typeAttribute = $(this).attr('input-attribute');
				if(typeAttribute.length == 0) return;

			  	switch(typeAttribute){
			  		case 'date-picker':
			  			$(this).datepicker({
			  				language: 'vi',
				            format: 'dd/mm/yyyy',
				            todayHighlight: true,
				            autoclose: true,
				        });
			  		break;

			  		case 'datetime-picker':
				  		$(this).datetimepicker({
				            format: 'dd/mm/yyyy - hh:ii',
				            showMeridian: true,
				            todayHighlight: true,
				            autoclose: true,
				        });
			  		break;

			  		case 'numeric':
			  			nhMain.input.inputMask.init($(this), 'number');
			  		break;

			  		case 'multiple-select':
			  			$(this).select2();
			  		break;

			  		case 'single-select':
			  			$(this).selectpicker();
			  		break;

			  		case 'city':
			  		case 'city_district':
			  		case 'city_district_ward':
			  			var wrapElement = $(this).closest('.wrap-location');
			  			if(wrapElement.length == 0) return;

			  			var cityDropdown = wrapElement.find('select#attribute_city_id');
			  			var districtDropdown = wrapElement.find('select#attribute_district_id');
			  			var wardDropdown = wrapElement.find('select#attribute_ward_id');

			  			cityDropdown.on('change', function( event ) {
			  				var cityId = $(this).val();
		  					if(wardDropdown.length > 0){
		  						wardDropdown.find('option:not([value=""])').remove();
								wardDropdown.selectpicker('refresh');
		  					}

		  					if(districtDropdown.length > 0){
		  						districtDropdown.find('option:not([value=""])').remove();
								districtDropdown.selectpicker('refresh');
		  					}

		  					if(districtDropdown.length > 0 && cityId > 0){
								nhMain.callAjax({
						    		async: false,
									url: adminPath + '/district/list/json/' + cityId
								}).done(function(response) {
									var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
						        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
						        	if (code == _SUCCESS && !$.isEmptyObject(data)) {
					                    var listOption = '';
								        $.each(data, function (key, item) {
								            listOption += '<option value="' + item.id + '">' + item.name + '</option>';
								        });
								        districtDropdown.append(listOption);
								        districtDropdown.selectpicker('refresh');
						            }
								});
		  					}
			  			});

			  			districtDropdown.on('change', function( event ) {
			  				var districtId = $(this).val();
		  					if(wardDropdown.length > 0){
		  						wardDropdown.find('option:not([value=""])').remove();
								wardDropdown.selectpicker('refresh');
		  					}

		  					if(wardDropdown.length > 0 && districtId > 0){
								nhMain.callAjax({
						    		async: false,
									url: adminPath + '/ward/list/json/' + districtId
								}).done(function(response) {
									var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
						        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
						        	if (code == _SUCCESS && !$.isEmptyObject(data)) {
					                    var listOption = '';
								        $.each(data, function (key, item) {
								            listOption += '<option value="' + item.id + '">' + item.name + '</option>';
								        });
								        wardDropdown.append(listOption);
								        wardDropdown.selectpicker('refresh');
						            }
								});
		  					}
			  			});
			  			
			  			wrapElement.find('select').selectpicker();

			  		break;

			  		case 'rich-text':
				  		tinymce.init({
						  	selector: '[input-attribute="rich-text"]',
						  	content_css: 'https://cdn0014.cdn4s.com/media/bootstrap.min.css',
						  	extended_valid_elements : 'div[nh-light-gallery|class|id|style]',
						  	height: 500,
						  	toolbar_sticky: true,
						  	image_caption: true,
						    image_advtab: true,
						    toolbar: false,
							quickbars_insert_toolbar: 'quicktable image',
							quickbars_selection_toolbar: 'bold italic underline | formatselect | bullist numlist | blockquote quicklink',
							contextmenu: 'undo redo | inserttable | cell row column deletetable | help',			  
							plugins: 'preview importcss searchreplace autolink code directionality visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking advlist lists wordcount help charmap quickbars emoticons',
							toolbar: 'bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | image media | numlist bullist | forecolor backcolor removeformat | fullscreen  preview | template codesample | ltr rtl | pagebreak | charmap emoticons | undo redo | link',
							font_size_formats: '8px 10px 12px 14px 16px 18px 20px 22px 24px 36px',
							file_picker_types: 'file image media',
							link_rel_list: [
						        {title: 'No Follow', value: 'nofollow'},
						        {title: 'Dofollow', value: 'dofollow'},
						        {title: 'Disable', value: ''},
						    ],
							templates: adminPath + '/system/get-tinymce-templates',							
						  	file_picker_callback: (callback, value, meta) => {

						  		$.fancybox.open({
						  			src: `${cdnUrl}/myfilemanager/?cross_domain=1&token=${accessKeyUpload}&field_id=tinymce_file&lang=${languageAdmin}`,
								    type: 'iframe'
								});

						    	$(window).on('message', function(e){
						    		var event = e.originalEvent;

						    		if(event.data.sender === 'myfilemanager' && event.data.field_id == 'tinymce_file'){
								      	var url = event.data.url;
								      	if($.isArray(url)) url = typeof(url[0]) ? url[0] : '';

										$.fancybox.close();
										$(window).off('message', this);

										callback(url);
								   	}
						    	});
							},
				   			init_instance_callback: function (editor) {
					            var events =['keyup', 'MouseLeave'];
					            if(editor.id == 'content'){
					                editor.on('keyup', function (e) {
				                        var result = {
				                            content: e.target.innerText,
				                            contentHtml:e.target.outerHTML,
				                            contentChars:e.target.outerHTML.length,
				                            countWords: typeof(editor.plugins.wordcount) != 'undefined' ? editor.plugins.wordcount.getCount(): 0
				                        }
				                        list_callback.keyup(result);
				                    });
					            }
					        }
						});
			  		break;

			  		case 'image':
			  			nhMain.selectMedia.single.init();
			  		break;

			  		case 'images':
						nhMain.selectMedia.album.init();
			  		break;

			  		case 'video':
						nhMain.selectMedia.video.init({
							input: $(this)
						});
			  		break;

			  		case 'files':
						nhMain.selectMedia.file.init();
			  		break;

			  		case 'product_select':
			  		case 'article_select':
			  			var wrapElement = $(this).closest('.wrap-auto-suggest');
			  			var code = $(this).attr('input-attribute-code');
			  			var url = null;

			  			if(typeAttribute == 'product_select'){
			  				url = adminPath + '/product/auto-suggest-normal';
			  			}

			  			if(typeAttribute == 'article_select'){
			  				url = adminPath + '/article/auto-suggest';
			  			}

			  			if(wrapElement.length == 0 || typeof(code) == _UNDEFINED || code.length == 0 || url.length == 0) return;
			  			var selectorSuggestInput = '#' + code + '-suggest';
			  			var inputName = code + '[]';			  			

			  			wrapElement.on('keyup keypress paste focus', selectorSuggestInput, function(e) {
			  				var inputElement = $(this);
							nhMain.autoSuggest.basic({
								inputSuggest: selectorSuggestInput,
								url: url,
							}, function(response){
								if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED && typeof(response.name) != _UNDEFINED){
									self.autoSuggestAddDataSelected({
										id: response.id,
										name: response.name
									}, wrapElement, inputName);
								}
							});
							
							if(e.type == 'focusin'){
								inputElement.autocomplete('search', inputElement.val());
							}
						});

						wrapElement.on('click', '.tagify__tag__removeBtn', function(e) {
							$(this).closest('.tagify__tag').remove();
						});

			  		break;

			  		case 'album_image':
			  			nhMain.selectMedia.album.init();
			  			self.managerItem.init($(this).closest('.wrap-manager'));
			  		break;

			  		case 'album_video':
			  			nhMain.selectMedia.video.init({
							input: $(this)
						});
			  			self.managerItem.init($(this).closest('.wrap-manager'));
			  		break;
			  	}
			});
		},
		setValueBeforeSubmit: function(formEl = null){
			if(formEl == null || typeof(formEl) == _UNDEFINED || formEl.length == 0) return false;

			formEl.find('textarea[input-attribute="rich-text"]').each(function( index ) {
			  	var id = $(this).attr('id');
			  	$(this).val(tinymce.get(id).getContent());
			});
		},
		autoSuggestAddDataSelected: function(item = {}, wrapElement = null, inputName = null){
			var self = this;

			if($.isEmptyObject(item)) return;
			if(wrapElement == null || wrapElement.length == 0) return;			
			if(inputName == null || inputName.length == 0) return;
			if(typeof(item.id) == _UNDEFINED ||  self.autoSuggestCheckDataExist(item.id, wrapElement)) return;

			var tagHtml = 
			'<span class="tagify__tag">\
	            <x class="tagify__tag__removeBtn" role="button"></x>\
	            <div><span class="tagify__tag-text">' + item.name + '</span></div>\
	            <input name="'+ inputName +'" value="' + item.id + '" type="hidden">\
	        </span>';
			wrapElement.find('#wrap-data-selected').append(tagHtml);
		},
		autoSuggestCheckDataExist: function(id = null, wrapElement = null){
			var self = this;
			if(wrapElement == null || wrapElement.length == 0) return false;

			if(wrapElement.find('#wrap-data-selected input[value="'+ id +'"]').length > 0) return true;
			return false;
		},
		managerItem: {
			wrapElement: null,
			listElement: null,			
			itemHtml: null,
			code: null,
			init: function(wrapElement = null){
				var self = this;

				var define = self.defineObject(wrapElement);
				if(!define) return;

				self.itemHtml = self.listElement.find('.wrap-item:first-child').length ? self.listElement.find('.wrap-item:first-child')[0].outerHTML : '';
				if(self.itemHtml == null || typeof(self.itemHtml) == _UNDEFINED || self.itemHtml.length == 0) return false;

				self.wrapElement.find('.wrap-item').each(function(index) {
				  	self.initInputItem($(this));
				});
				
				self.event();
			},
			event: function(){
				var self = this;


				$(document).on('click', '.wrap-manager #add-item', function(e) {
				    e.stopImmediatePropagation();

					var define = self.defineObject($(this).closest('.wrap-manager'));
					if(!define) return;

					self.addNewItem();
				});

				$(document).on('click', '.wrap-manager .btn-delete-item', function(e) {
					e.stopImmediatePropagation();

					var define = self.defineObject($(this).closest('.wrap-manager'));
					if(!define) return;

					var itemElement = $(this).closest('.wrap-item');
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

				    		if(self.listElement.find('.wrap-item').length > 1){
				    			itemElement.remove();
				    		}else{
				    			self.clearDataItem(itemElement);
				    		}

				    	}
				    });
				});

				$(document).on('click', '.wrap-manager .btn-toggle-item', function(e) {
					e.stopImmediatePropagation();

					var define = self.defineObject($(this).closest('.wrap-manager'));
					if(!define) return;

					var itemElement = $(this).closest('.wrap-item');
					var hidden = itemElement.hasClass('kt-portlet--collapse');

					itemElement.toggleClass('kt-portlet--collapse', !hidden);
					if(hidden){
						itemElement.find('.kt-portlet__body').slideDown();
					}else{
						itemElement.find('.kt-portlet__body').slideUp();
					}
				});

				$(document).on('keyup', '.wrap-manager input.item-name', function(e) {
					e.stopImmediatePropagation();

					var define = self.defineObject($(this).closest('.wrap-manager'));
					if(!define) return;

					var name = $(this).val();
					if(name.length == 0){
						name = 'New Item';
					}

					var itemElement = $(this).closest('.wrap-item');
					itemElement.find('.kt-portlet__head-title').text(name);
				});
			},
			defineObject: function (wrapElement = null) {
				var self = this;

				if(wrapElement == null || wrapElement.length == 0) return false;
				self.wrapElement = wrapElement;

				self.code = self.wrapElement.attr('attribute-code');
				if(self.code == null || typeof(self.code) == _UNDEFINED || self.code.length == 0) return false;

				self.listElement = self.wrapElement.find('.list-item');
				if(self.listElement == null || typeof(self.listElement) == _UNDEFINED || self.listElement.length == 0) return false;

				self.itemHtml = self.listElement.find('.wrap-item:first-child').length ? self.listElement.find('.wrap-item:first-child')[0].outerHTML : '';
				if(self.itemHtml == null || typeof(self.itemHtml) == _UNDEFINED || self.itemHtml.length == 0) return false;
				
				return true;
			},
			initInputItem: function(itemElement = null){
				var self = this;
				if(itemElement == null || itemElement.length == 0) return;
				var indexItem = itemElement.index();

				// replace name input
				self.replaceNameInput(itemElement);

				if(itemElement.find('[input-attribute="album_video"]').length > 0){
					itemElement.find('[input-attribute="album_video"]').each(function(index) {
						nhMain.selectMedia.video.init({
							input: $(this)
						});
					});
					
				}

				if(itemElement.find('select#type_video').length > 0){
					var selectElement = itemElement.find('select#type_video');
					selectElement.val(itemElement.find('select#type_video option:first').val());

					// selectElement.selectpicker();
					
				}

				if(itemElement.find('.input-select-image').length > 0){
					var inputImage = itemElement.find('.input-select-image');
					var _id = inputImage.attr('id');

					var preIndex = indexItem - 1;

					var newId = _id.replace('_' + preIndex + '_', '_' + indexItem + '_');
					inputImage.attr('id', newId);

					var btnSelectImage = itemElement.find('.btn-select-image');
					if(btnSelectImage.length > 0){
						var dataSource = btnSelectImage.data('src');
						dataSource = dataSource.replace(_id, newId);
						btnSelectImage.attr('data-src', dataSource)
					}
				}
				
			},
			replaceNameInput: function(itemElement = null){
				var self = this;
				var indexItem = itemElement.index();
				$('input, select, textarea', itemElement).each(function () {
					if (typeof($(this).attr('data-name')) == _UNDEFINED) return;

					var name = self.code + '['+ indexItem +'][' + $(this).attr('data-name') + ']';
					if(typeof($(this).attr('data-mutiple')) != _UNDEFINED){
						name += '[]';
					}

					// thay đổi đường dẫn chọn album ảnh và id của input nhận callback ảnh
					if($(this).attr('input-attribute') == 'album_image'){
						var idReplace = self.code + '_' + indexItem + '_' + $(this).attr('data-name');
						$(this).attr('id', idReplace);

						// thay đổi đường dẫn button chọn album images
						if(itemElement.find('.btn-select-image-album').length > 0){
							var btn = itemElement.find('.btn-select-image-album');
							var src = btn.data('src');
							var srcSplit = src.split('field_id=');
							var srcNew = srcSplit[0] + 'field_id=' + idReplace;
							btn.attr('data-src', srcNew);
						}
					}

					// thay đổi đường dẫn chọn album video và id của input nhận callback ảnh
					if($(this).attr('input-attribute') == 'album_video'){
						var idReplace = self.code + '_' + indexItem + '_' + $(this).attr('data-name');
						$(this).attr('id', idReplace);

						// thay đổi đường dẫn button chọn album images
						if(itemElement.find('.btn-select-video').length > 0){
							var btn = itemElement.find('.btn-select-video');
							var src = btn.data('src');
							var srcSplit = src.split('field_id=');
							var srcNew = srcSplit[0] + 'field_id=' + idReplace;
							btn.attr('data-src', srcNew);
						}
					}

					$(this).attr('name', name);					
				});


				

			},
			addNewItem: function(){
				var self = this;
				self.listElement.append(self.itemHtml);
				var itemElement = self.listElement.find('.wrap-item:last-child');

				self.clearDataItem(itemElement);
				self.initInputItem(itemElement);
			},
			clearDataItem: function(itemElement = null){
				if(itemElement == null || itemElement.length == 0) return;
				var self = this;

				itemElement.find('.kt-portlet__head-title').text('New Item');

				$('input, select, textarea', itemElement).each(function () {
					var typeInput = $(this).attr('type');
					if(typeInput == 'checkbox'){
						$(this).prop('checked', false);
					}else{
						$(this).val('');
					}
				});

				if(itemElement.find('.kt-avatar').length > 0){
					var wrapImage = itemElement.find('.kt-avatar');
					wrapImage.removeClass('kt-avatar--changed');
					wrapImage.find('.kt-avatar__holder').css('background-image', '');
				}

				if(itemElement.find('.list-image-album').length > 0){
					itemElement.find('.list-image-album').html('');
				}
			}
		}
	},
	input: {
		inputMask:{
			init: function(el, type = null){
				var self = this;
				var options = {};
				switch(type){
					case 'email':
						options = self.options.email;
						el.inputmask(options);
					break;

					case 'number':
						options = self.options.number;
						el.inputmask('decimal', options);

						el.focus(function() {
						 	$(this).select(); 
						});
					break;

					default:				
					break;
				}				
			},
			options: {
				number: {
					integerDigits: 13,
					autoGroup: true,
					groupSeparator: ',',
					groupSize: 3,
					rightAlign: false,
					allowPlus: false,
    				allowMinus: false
		        },
		        email: {
		            mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
		            greedy: false,
		            onBeforePaste: function (pastedValue, opts) {
		                pastedValue = pastedValue.toLowerCase();
		                return pastedValue.replace("mailto:", "");
		            },
		            definitions: {
		                '*': {
		                    validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
		                    cardinality: 1,
		                    casing: "lower"
		                }
		            }
		        }
			}
		},
		dateRangerPicker: function(el, options = {}){
			var self = this;

			var timePicker = typeof(options.timePicker) != _UNDEFINED && options.timePicker != false ? true : false;
			var formatTime = 'DD/MM/YYYY';

			if(timePicker){
				formatTime = 'HH:mm - DD/MM/YYYY';
			}
			var default_options = {
				singleDatePicker: false,
	            buttonClasses: 'btn',
	            applyClass: 'btn-primary',
	            cancelClass: 'btn-secondary',
	            timePicker: timePicker,
	            timePickerIncrement: 30,
	            timePicker24Hour: true,
	            autoUpdateInput: false,
	            showCustomRangeLabel: false,
	            ranges: {
			        [nhMain.getLabel('hom_nay')]: [moment(), moment()],
			        [nhMain.getLabel('7_ngay_truoc')]: [moment().subtract(6, 'days'), moment()],
			        [nhMain.getLabel('30_ngay_truoc')]: [moment().subtract(29, 'days'), moment()],
			        [nhMain.getLabel('thang_nay')]: [moment().startOf('month'), moment().endOf('month')],
			        [nhMain.getLabel('thang_truoc')]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			    },
			    linkedCalendars: false,
			    alwaysShowCalendars: true,
	            locale: {
			        format: formatTime,
			        separator: ' - ',
			        applyLabel: nhMain.getLabel('ap_dung'),
			        cancelLabel: nhMain.getLabel('huy'),
			        fromLabel: nhMain.getLabel('tu'),
			        customRangeLabel: nhMain.getLabel('pham_vi_tuy_chinh'),
			        toLabel: nhMain.getLabel('den'),
			        weekLabel: nhMain.getLabel('tuan'),
			        daysOfWeek: [
			            nhMain.getLabel('cn'),
			            nhMain.getLabel('t2'),
			            nhMain.getLabel('t3'),
			            nhMain.getLabel('t4'),
			           	nhMain.getLabel('t5'),
			            nhMain.getLabel('t6'),
			            nhMain.getLabel('t7')
			        ],
			        monthNames: [
			            nhMain.getLabel('thang_1'),
			            nhMain.getLabel('thang_2'),
			            nhMain.getLabel('thang_3'),
			            nhMain.getLabel('thang_4'),
			            nhMain.getLabel('thang_5'),
			            nhMain.getLabel('thang_6'),
			            nhMain.getLabel('thang_7'),
			            nhMain.getLabel('thang_8'),
			            nhMain.getLabel('thang_9'),
			            nhMain.getLabel('thang_10'),
			            nhMain.getLabel('thang_11'),
			            nhMain.getLabel('thang_12')
			        ]
			    }
	        }

	        el.daterangepicker(default_options, function(start, end, label) {
	            el.val( start.format(formatTime) + ' → ' + end.format(formatTime));
	        });
		},		
		touchSpin: {
			init: function(el, options = {}){
				el.TouchSpin({
		            buttondown_class: 'btn btn-secondary',
		            buttonup_class: 'btn btn-secondary',
		            verticalbuttons: true,
		            prefix: typeof(options.prefix) != _UNDEFINED ? options.prefix : '',
		            min: typeof(options.min) != _UNDEFINED ? options.min : 0,
            		max: typeof(options.max) != _UNDEFINED ? options.max : 9999,
            		step: typeof(options.step) != _UNDEFINED ? options.step : 1,
		            verticalup: '<i class="la la-plus"></i>',
		            verticaldown: '<i class="la la-minus"></i>'
		        });
			}
		}
	},
	tinyMce: {
		simple: function(params = {}){
			var self = this;
			tinymce.init({
			  	selector: '.mce-editor-simple',
			  	height: 300,
			  	menubar: '',
				plugins: 'preview searchreplace autolink code directionality visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking advlist lists wordcount help charmap quickbars emoticons',
				toolbar: 'bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | charmap emoticons | undo redo | link',
				font_size_formats: '8px 10px 12px 14px 16px 18px 20px 22px 24px 36px',
				link_rel_list: [
			        {title: 'No Follow', value: 'nofollow'},
			        {title: 'Dofollow', value: 'dofollow'},
			        {title: 'Disable', value: ''},
			    ],
			});
		},
		full: function(list_callback = {}){
			var self = this;
			if (typeof(list_callback.keyup) == _UNDEFINED) {
		        list_callback.keyup = function () {};
		    }
			var self = this;
			tinymce.init({
			  	selector: '.mce-editor',
			  	content_css: 'https://cdn0014.cdn4s.com/media/bootstrap.min.css',
			  	extended_valid_elements : 'div[nh-light-gallery|class|id|style]',
			  	height: 500,
			  	toolbar_sticky: true,
			  	image_caption: true,
			    image_advtab: true,
			    toolbar: false,
				quickbars_insert_toolbar: 'quicktable image',
				quickbars_selection_toolbar: 'bold italic underline | formatselect | bullist numlist | blockquote quicklink',
				contextmenu: 'undo redo | inserttable | cell row column deletetable | help',			  
				plugins: 'preview importcss searchreplace autolink code directionality visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking advlist lists wordcount help charmap quickbars emoticons',
				toolbar: 'bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | image media | numlist bullist | forecolor backcolor removeformat | fullscreen  preview | template codesample | ltr rtl | pagebreak | charmap emoticons | unlink | undo redo | link',
				font_size_formats: '8px 10px 12px 14px 16px 18px 20px 22px 24px 36px',
				file_picker_types: 'file image media',
				link_rel_list: [
			        {title: 'No Follow', value: 'nofollow'},
			        {title: 'Dofollow', value: 'dofollow'},
			        {title: 'Disable', value: ''},
			    ],
				templates: adminPath + '/system/get-tinymce-templates',
			  	file_picker_callback: (callback, value, meta) => {
			  		$.fancybox.open({
			    		src: `${cdnUrl}/myfilemanager/?cross_domain=1&token=${accessKeyUpload}&field_id=tinymce_file&lang=${languageAdmin}`,
					    type: 'iframe'
					});

			    	$(window).on('message', function(e){
			    		var event = e.originalEvent;

			    		if(event.data.sender === 'myfilemanager' && event.data.field_id == 'tinymce_file'){
					      	var url = event.data.url;
					      	if($.isArray(url)) url = typeof(url[0]) ? url[0] : '';

							$.fancybox.close();
							$(window).off('message', this);

							callback(url);
					   	}
			    	});
				},
	   			init_instance_callback: function (editor) {
		            var events =['keyup', 'MouseLeave'];
		            if(editor.id == 'content'){
		                editor.on('keyup', function (e) {
	                        var result = {
	                            content: e.target.innerText,
	                            contentHtml:e.target.outerHTML,
	                            contentChars:e.target.outerHTML.length,
	                            countWords: typeof(editor.plugins.wordcount) != 'undefined' ? editor.plugins.wordcount.getCount(): 0
	                        }
	                        list_callback.keyup(result);
	                    });           
		            }
		        },
			});
		}
	},
	autoSuggest: {
		inputSuggest: null,
		inputValue: null,
		url: '',
		fieldLabel: 'name',
		fieldValue: 'id',
		filter: {},
		getParams: {},
		basic: function(params = {}, callback){
			var self = this;
			if (typeof(callback) != 'function') {
		        callback = function () {};
		    }

		    self.inputSuggest = typeof(params.inputSuggest) != _UNDEFINED ? params.inputSuggest : '';
		    self.inputValue = typeof(params.inputValue) != _UNDEFINED ? params.inputValue : '';
		    self.url = typeof(params.url) != _UNDEFINED ? params.url : '';
		    self.fieldLabel = typeof(params.fieldLabel) != _UNDEFINED ? params.fieldLabel : '';
		    self.fieldValue = typeof(params.fieldValue) != _UNDEFINED ? params.fieldValue : 'id';
		    self.filter = typeof(params.filter) != _UNDEFINED ? params.filter : {};
		    self.getParams = typeof(params.getParams) != _UNDEFINED ? params.getParams : {};
		    
		    var markItems = typeof(params.markItems) != _UNDEFINED ? params.markItems : [];
		    var itemMore = typeof(params.itemMore) != _UNDEFINED ? params.itemMore : {};

		    if(self.inputSuggest.length > 0 && self.url.length > 0){

		    	$(document).on('focus', self.inputSuggest, function(e) {
					$(this).select();
				});
		    	
				$(self.inputSuggest).autocomplete({
					delay: 500,			        
			        minLength: 0,
			        autoFocus: false,
			        selectFirst: false,
			        classes: typeof(params.classes) != _UNDEFINED ? params.classes : null,
			        markItems: markItems,
				    source: function(request, suggest){
				    	self.filter['keyword'] = typeof(request.term) != _UNDEFINED ? request.term : null;
				    	var result = [];

				    	// format default response is {id: value, name: value}
				    	nhMain.callAjax({
				    		async: false,
							url: self.url,
							data: {
								filter: self.filter,
								get_params: self.getParams
							}
						}).done(function(response) {							
							if(response.code = _SUCCESS){
								result = response.data;
							}						    
						});

						if(!$.isEmptyObject(itemMore)){

							var keyName = 'name';
							if(self.fieldLabel.length > 0 && self.fieldLabel != 'name'){
					        	keyName = self.fieldLabel;
					        }
					        var itemMoreObject = {
					        	id: typeof(itemMore.value) != _UNDEFINED ? itemMore.value : _ADD
					        }

					        itemMoreObject[keyName] = typeof(itemMore.label) != _UNDEFINED ? itemMore.label : nhMain.getLabel('them_ban_ghi_moi')

							result.unshift(itemMoreObject);
				    	}

						suggest(result);
				    },
				    select: function (e, ui) {
			            $(self.inputSuggest).val('');

			            if (!$.isEmptyObject(ui.item)) {
			            	var _id = typeof(ui.item[self.fieldValue]) != _UNDEFINED ? ui.item[self.fieldValue] : null;
			            	var _moreValue = typeof(itemMore.value) != _UNDEFINED ? itemMore.value : null;
			            	if(_id != null && _id != _moreValue){
			            		$(self.inputValue).val(_id);
			            	}
			                callback(ui.item);
			            }
			            e.preventDefault();
			        },
				}).autocomplete('instance')._renderItem = function (ul, item) {
					var classItem = '';
					if(typeof(itemMore.value) != _UNDEFINED && typeof(item.id) != _UNDEFINED && itemMore.value == item.id){
						classItem = 'item-add-more';
					}

					if($.inArray(item.id, this.options.markItems) != -1) classItem += ' mark'

					var name = typeof(item.name) != _UNDEFINED ? item.name : '';
				    if(self.fieldLabel.length > 0 && self.fieldLabel != 'name'){
			        	name = typeof(item[self.fieldLabel]) != _UNDEFINED ? item[self.fieldLabel] : '';
			        }

			        return $(`<li class="${classItem}">`).append(`<span>${name}</span>`).appendTo(ul);
				};

		    }
		} 
	},
	getLabel: function(key = null){
		if(typeof(locales[key]) == _UNDEFINED){
			return key;
		}
		return locales[key];
	},
	validation: {
		offsetScroll: -145,
		error: {
			show: function(input = null, message = null, callback){
				if(input.length > 0 && message.length > 0){
					input.next('div.error').remove();					
					if (typeof(callback) != 'function') {
				        callback = function () {};
				    }

				    input.closest('.form-group').addClass('is-invalid');
					var name = typeof(input.attr('name')) != _UNDEFINED ? input.attr('name') + '-error' : '';
					var error = '<div id="' + name + '" class="error invalid-feedback">' + message + '</label>';
					input.after(error).focus();
					callback();
				}		
			},
			clear: function(wrapForm = null){
				if(wrapForm.length > 0){
					wrapForm.find('.form-group').removeClass('is-invalid');					
					wrapForm.find('div.error').remove();
				}
			}
		},
		isEmail: function(email = null){
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  		return regex.test(email);
		},
		isPhone: function(phone = null){
			var regex = /[0-9]{10,11}/;
	  		return regex.test(phone);
		},
		phoneVn: function(){
			$.validator.addMethod('phoneVN', function(phone_number, element) {
				phone_number = phone_number.replace( /\(|\)|\s+|-/g, '');
				return this.optional(element) || phone_number.length > 9 && phone_number.match( /^(01|02|03|04|05|06|07|08|09)+([0-9]{8,9})\b$/ );
			}, nhMain.getLabel('so_dien_thoai_chua_dung_dinh_dang'));
		},
		phoneInput: function(){
			$(document).on('keypress', '.phone-input', function(e) {
				if(!$.isNumeric(e.key)){
					return false;
				}

				if($(this).val().length > 10){
					return false;
				}
			});

			$(document).on('focus', '.phone-input', function(e) {
	    		$(this).select();
	    	});
		},
		url: {
			init: function(){
				var self = this;

				$.validator.addMethod('url', function (url, element) {
			            if (url != nhMain.utilities.parseToUrl(url)) {
			                return false;
			            }
		                return true;
		            },
		            nhMain.getLabel('duong_dan_chua_dung_dinh_dang')
		        );
			},
			checkExist: function(_url, _id, callback){
				if (typeof(callback) != 'function'){
					callback = function() {};
				}
		        
		        nhMain.callAjax({
					url: adminPath + '/link/check-exist',
					async: false,
					data:{
						url: _url,
						id: _id
					}
				}).done(function(response) {
					callback(response);
				})
			}
		}
	},
	utilities: {
		notEmpty: function(value = null){
			if(typeof(value) == _UNDEFINED){
				return false;
			}

			if(value == null){
				return false;
			}

			if(value.length == 0){
				return false;
			}

			return true;
		},
		parseNumberToTextMoney: function(number = null){
			if (typeof(number) != 'number' || isNaN(number) || typeof(number) == _UNDEFINED) {
		        return 0;
		    }	    
	    	return number.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
		},
		parseTextMoneyToNumber: function(text_number = null){
			if (typeof(text_number) == _UNDEFINED) {
		        return 0;
		    }

			var number = parseFloat(text_number.toString().replace(/,/g, ''));
			if(isNaN(number)) number = 0;
			
			return number;
		},
		parseFloat: function(number = null){
			if (isNaN(number) || typeof(number) == _UNDEFINED || number == null) {
		        return 0;
		    }	

			number = parseFloat(number);
			if (isNaN(number)) {
		        return 0;
		    }
		    return number;
		},
		parseInt: function(number = null){
			if (isNaN(number) || typeof(number) == _UNDEFINED || number == null) {
		        return 0;
		    }	

			number = parseInt(number);
			if (isNaN(number)) {
		        return 0;
		    }
		    return number;
		},
		parseIntToDateString: function(number = null){
			var self = this;
			var date_string = '';
			var int_number = nhMain.utilities.parseInt(number);
			if(int_number > 0){
				var date = new Date(int_number * 1000);	
				date_string = date.getDate() + '/' + (date.getMonth()+1) + '/' + date.getFullYear();
			}
			return date_string;
		},
		parseIntToDateTimeString: function(number = null){
			var self = this;
			var date_string = '';
			var int_number = nhMain.utilities.parseInt(number);
			if(int_number > 0){
				var date = new Date(int_number * 1000);
				var minutes = date.getMinutes();
				if(minutes < 10){
					minutes = '0' + minutes;
				}				

				var hours = date.getHours();
				if(hours < 10){
					hours = '0' + hours;
				}

				date_string = hours + ':' + minutes + ' - ' +  date.getDate() + '/' + (date.getMonth()+1) + '/' + date.getFullYear();
			}
			return date_string;
		},
		parseJsonToObject: function(json_string = null){
			var result = {};
			try {
		        result = JSON.parse(json_string);
		    } catch (e) {
		        return {};
		    }
		    return result;
		},
		parseToUrl: function(str = null){
			str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ẫ|ậ|ẩ|ẩ|ă|ằ|ắ|ẳ|ặ|ẵ/g, 'a');
		    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ẫ|Ậ|Ẩ|Ă|Ằ|Ắ|Ặ|Ẵ|ẵ/g, 'a');
		    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ+/g, 'e');
		    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ+/g, 'e');
		    str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
		    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, 'i');
		    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ+/g, 'o');
		    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ+/g, 'o');
		    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
		    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, 'u');
		    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
		    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, 'y');
		    str = str.replace(/đ/g, 'd');
		    str = str.replace(/Đ/g, 'd');
		    // return str.toLowerCase().trim().replace(/[^\w ]+/g, '').replace(/ +/g, '-').replace(/&/g, '-and-');

		    
		    str = str.toUpperCase();
		    var char_map = {
		            // Latin
		            'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE', 'Ç': 'C', 
		            'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I', 'Î': 'I', 'Ï': 'I', 
		            'Ð': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö': 'O', 'Ő': 'O', 
		            'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U', 'Ű': 'U', 'Ý': 'Y', 'Þ': 'TH', 
		            'ß': 'ss', 
		            'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'æ': 'ae', 'ç': 'c', 
		            'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i', 
		            'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ő': 'o', 
		            'ø': 'o', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ű': 'u', 'ý': 'y', 'þ': 'th', 
		            'ÿ': 'y',
		            // Latin symbols
		            '©': '(c)',
		            // Greek
		            'Α': 'A', 'Β': 'B', 'Γ': 'G', 'Δ': 'D', 'Ε': 'E', 'Ζ': 'Z', 'Η': 'H', 'Θ': '8',
		            'Ι': 'I', 'Κ': 'K', 'Λ': 'L', 'Μ': 'M', 'Ν': 'N', 'Ξ': '3', 'Ο': 'O', 'Π': 'P',
		            'Ρ': 'R', 'Σ': 'S', 'Τ': 'T', 'Υ': 'Y', 'Φ': 'F', 'Χ': 'X', 'Ψ': 'PS', 'Ω': 'W',
		            'Ά': 'A', 'Έ': 'E', 'Ί': 'I', 'Ό': 'O', 'Ύ': 'Y', 'Ή': 'H', 'Ώ': 'W', 'Ϊ': 'I',
		            'Ϋ': 'Y',
		            'α': 'a', 'β': 'b', 'γ': 'g', 'δ': 'd', 'ε': 'e', 'ζ': 'z', 'η': 'h', 'θ': '8',
		            'ι': 'i', 'κ': 'k', 'λ': 'l', 'μ': 'm', 'ν': 'n', 'ξ': '3', 'ο': 'o', 'π': 'p',
		            'ρ': 'r', 'σ': 's', 'τ': 't', 'υ': 'y', 'φ': 'f', 'χ': 'x', 'ψ': 'ps', 'ω': 'w',
		            'ά': 'a', 'έ': 'e', 'ί': 'i', 'ό': 'o', 'ύ': 'y', 'ή': 'h', 'ώ': 'w', 'ς': 's',
		            'ϊ': 'i', 'ΰ': 'y', 'ϋ': 'y', 'ΐ': 'i',
		            // Turkish
		            'Ş': 'S', 'İ': 'I', 'Ç': 'C', 'Ü': 'U', 'Ö': 'O', 'Ğ': 'G',
		            'ş': 's', 'ı': 'i', 'ç': 'c', 'ü': 'u', 'ö': 'o', 'ğ': 'g', 
		            // Russian
		            'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo', 'Ж': 'Zh',
		            'З': 'Z', 'И': 'I', 'Й': 'J', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O',
		            'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'C',
		            'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sh', 'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu',
		            'Я': 'Ya',
		            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
		            'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
		            'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'c',
		            'ч': 'ch', 'ш': 'sh', 'щ': 'sh', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu',
		            'я': 'ya',
		            // Ukrainian
		            'Є': 'Ye', 'І': 'I', 'Ї': 'Yi', 'Ґ': 'G',
		            'є': 'ye', 'і': 'i', 'ї': 'yi', 'ґ': 'g',
		            // Czech
		            'Č': 'C', 'Ď': 'D', 'Ě': 'E', 'Ň': 'N', 'Ř': 'R', 'Š': 'S', 'Ť': 'T', 'Ů': 'U', 
		            'Ž': 'Z', 
		            'č': 'c', 'ď': 'd', 'ě': 'e', 'ň': 'n', 'ř': 'r', 'š': 's', 'ť': 't', 'ů': 'u',
		            'ž': 'z', 
		            // Polish
		            'Ą': 'A', 'Ć': 'C', 'Ę': 'e', 'Ł': 'L', 'Ń': 'N', 'Ó': 'o', 'Ś': 'S', 'Ź': 'Z', 
		            'Ż': 'Z', 
		            'ą': 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n', 'ó': 'o', 'ś': 's', 'ź': 'z',
		            'ż': 'z',
		            // Latvian
		            'Ā': 'A', 'Č': 'C', 'Ē': 'E', 'Ģ': 'G', 'Ī': 'i', 'Ķ': 'k', 'Ļ': 'L', 'Ņ': 'N', 
		            'Š': 'S', 'Ū': 'u', 'Ž': 'Z',
		            'ā': 'a', 'č': 'c', 'ē': 'e', 'ģ': 'g', 'ī': 'i', 'ķ': 'k', 'ļ': 'l', 'ņ': 'n',
		            'š': 's', 'ū': 'u', 'ž': 'z'
		        };

		    $.each(char_map, function( key, value ) {
		        str = str.replaceAll(key, value);
		    });

		    str = str.toLowerCase().trim().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '').replace(/ +/g, '-').replace(/&/g, '-and-');
		    str = str.replace(RegExp('[-]{2,}', 'g'), '-');
		    return str;
		},
		getThumbs: function(url = null, size = null){
			if(!nhMain.utilities.notEmpty(url) || !nhMain.utilities.notEmpty(size) || $.inArray(size, [50, 150, 250, 350]) == -1) return false;
			
			var path = url.split('/');

			path[1] = 'thumbs';
			var extension = url.replace(/^.*\./, '');
			var fileName = path[path.length - 1];
			var name = fileName.substr(0,fileName.lastIndexOf('.'));

			if(!nhMain.utilities.notEmpty(name) || !nhMain.utilities.notEmpty(extension)) return false;

			var fileName = name + '_thumb_' + size + '.' + extension;
			path[path.length - 1] = fileName;
			return path.join('/');
		},
		isJson: function(str = null){
			try {
		        JSON.parse(str);
		    } catch (e) {
		        return false;
		    }
		    return true;
		}
	},
	location: {
		idWrap: null,
		init: function(params = {}){
			var self = this;

			self.idWrap = typeof(params.idWrap) != _UNDEFINED ? params.idWrap : [];	

			$.each(self.idWrap, function(index, idWrap) {
				$(document).on('change', idWrap + ' #city_id', function(e) {
					//clear ward select
					var wardSelect = $(idWrap + ' #ward_id');
					wardSelect.find('option:not([value=""])').remove();
					wardSelect.selectpicker('refresh');

					// clear district select
					var districtSelect = $(idWrap + ' #district_id');
					districtSelect.find('option:not([value=""])').remove();
					districtSelect.selectpicker('refresh');

					// load option district select
					var city_id = $(this).val();
					if(city_id > 0){
						var _data = {};
						_data[_PAGINATION] = {};
						_data[_PAGINATION][_PERPAGE] = 200;

						nhMain.callAjax({
				    		async: false,
							url: adminPath + '/district/list/json/' + city_id,
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
							        districtSelect.append(listOption);
							        districtSelect.selectpicker('refresh');
			                    }		                    
				            } else {
				            	toastr.error(message);
				            }
						});
					}
				});

				$(document).on('change', idWrap + ' #district_id', function(e) {
					//clear ward select
					var wardSelect = $(idWrap + ' #ward_id');
					wardSelect.find('option:not([value=""])').remove();
					wardSelect.selectpicker('refresh');

					// load option ward select
					var district_id = $(this).val();				
					if(district_id > 0){
						var _data = {};
						_data[_PAGINATION] = {};
						_data[_PAGINATION][_PERPAGE] = 200;

						nhMain.callAjax({
				    		async: false,
							url: adminPath + '/ward/list/json/' + district_id,
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
							        wardSelect.append(listOption);
							        wardSelect.selectpicker('refresh');
			                    }		                    
				            } else {
				            	toastr.error(message);
				            }
						});
					}
				});		  
			});
		}
	},
	mainCategory: {
		wrapCategory: null,
		init: function(params = {}) {
			var self = this;
			self.wrapCategory = typeof(params.wrapCategory) != _UNDEFINED ? params.wrapCategory : [];

			var idCategorySelected = $(self.wrapCategory + ' #main_category_id').val();

			$(document).on('change', self.wrapCategory + ' #categories', function(e) {
				var listCategory = [];
				$.each($(this).val(), function(index, category_id) {
					var textName = $(self.wrapCategory + ` #categories option[value="${category_id}"]`).text();

					listCategory.push({id: category_id, text: textName});
				});
				
				var categorySelect = $(self.wrapCategory + ' #main_category_id');
				categorySelect.find('option:not([value=""])').remove();
				categorySelect.selectpicker('render');

				if(listCategory) {
					var listOption = '';
					var firstOption = null;
					var existIdSelected = false;

					$.each(listCategory, function(key, item){
						if(key === 0 && !firstOption){
							firstOption = item.id;
						}
						listOption += '<option value="' + item.id + '">' + item.text + '</option>';

						if(item.id == idCategorySelected) existIdSelected = true;
					});
					categorySelect.append(listOption);
					categorySelect.val(firstOption);

					if(idCategorySelected > 0 && existIdSelected){
						categorySelect.val(idCategorySelected);
					}
					
					categorySelect.selectpicker('refresh');
				}
			});
		}
	},
	selectMedia: {
		single:{
			init: function(){
				var self = this;

				$(document).on('click', '.btn-select-image', function(e) {
					e.stopImmediatePropagation();

					$.fancybox.open({
			    		src: $(this).attr('data-src'),
					    type: 'iframe'
					});
			    	$(window).on('message', self.onSelectImage);
			    });

			    $(document).on('click', '.btn-clear-image', function(e) {
			    	e.stopImmediatePropagation();

			    	var wrap = $(this).closest('.kt-avatar');
			    	wrap.find('.kt-avatar__holder').css('background-image', '').removeAttr('href');
			    	wrap.removeClass('kt-avatar--changed');
			    	wrap.find('input[type="hidden"]').val('');
			    });   
			},
			onSelectImage: function(e){
				var self = this;
				var event = e.originalEvent;

			   	if(event.data.sender === 'myfilemanager'){
			      	if(event.data.field_id){
			      		var field_id = event.data.field_id;
			      		var input = $('#' + field_id);
			      		var wrap = input.closest('.kt-avatar');
			      		var url = event.data.url;

			      		var imageUrl = typeof(url[0]) != _UNDEFINED ? url[0] : null;
			      		if(input.length && imageUrl != null){
			      			$('#' + field_id).val(imageUrl.replace(cdnUrl, ''));
			      		}
						
			      		if(wrap.length && imageUrl != null){
			      			wrap.addClass('kt-avatar--changed')
		        			wrap.find('.kt-avatar__holder').css('background-image', 'url("' + imageUrl + '")').attr('href', imageUrl);
			      		}					

						$.fancybox.close();
						$(window).off('message', self.onSelectImage);
			      	}
			   	}
			},
		},
		album:{
			init: function(){
				var self = this;

			    $(document).on('click', '.btn-select-image-album', function(e) {
			    	e.stopImmediatePropagation();

			    	$.fancybox.open({
			    		src: $(this).attr('data-src'),
					    type: 'iframe'
					});
			    	$(window).on('message', self.onSelectAlbum);
			    });

			    $(document).on('click', '.btn-clear-image-album', function(e) {
			    	e.preventDefault();
			    	e.stopImmediatePropagation();

			    	
			    	var wrap = $(this).closest('.wrap-album');
			    	var input = wrap.find('input');

			    	$(this).closest('.item-image-album').remove();

			    	var list_images = [];
			    	wrap.find('.item-image-album').each(function(index) {
			    		var imageUrl = $(this).data('image');
						list_images.push(imageUrl.replace(cdnUrl, ''));
					});

					var json_value = !$.isEmptyObject(list_images) ? JSON.stringify(list_images) : '';
			      	input.val(json_value);
			    });
			    
			    self.sortItem();
			},
			template:{
				itemImage: function(url_image = null){
					return '\
					<a href="'+ url_image +'" target="_blank" class="kt-media kt-media--lg mr-10 position-relative item-image-album" data-image="'+ url_image +'">\
                        <img src="'+ url_image +'">\
                        <span class="btn-clear-image-album" title="'+ nhMain.getLabel('xoa_anh') +'">\
                            <i class="fa fa-times"></i>\
                        </span>\
                    </a>';
				}
			},
			onSelectAlbum: function(e){
				var self = this;
				var event = e.originalEvent;
			   	if(event.data.sender === 'myfilemanager'){
			      	if(event.data.field_id){
			      		var field_id = event.data.field_id;
			      		var input = $('#' + field_id);
			      		var wrap = input.closest('.wrap-album');
			      		var listAlbum = wrap.find('.list-image-album');
			      		var url = event.data.url;
			      		if(listAlbum.length && input.length && url.length){
			      			$.each(url, function(index, image){
			      				var imageHtml = nhMain.selectMedia.album.template.itemImage(image);
			      				
			      				listAlbum.append(imageHtml);
							});

			      			var list_images = [];
			      			listAlbum.find('.item-image-album').each(function(index) {
			      				var imageUrl = $(this).data('image');
								list_images.push(imageUrl.replace(cdnUrl, ''));
							});

			      			var json_value = !$.isEmptyObject(list_images) ? JSON.stringify(list_images) : '';
			      			input.val(json_value);

			      			listAlbum.removeClass('d-none');
			      		}

						$.fancybox.close();
						$(window).off('message', self.onSelectAlbum);
			      	}
			   	}
			},
			sortItem: function() {
				var self = this;

				$('.wrap-album').each(function(index) {
					var wrapElement = $(this);
				  	var albumElement = $(this).find('.list-image-album');
				  	if(albumElement.length == 0) return;

				  	albumElement.sortable({
						containment: wrapElement,
						stop: function( event, ui ) {
							var images = [];
					    	albumElement.find('.item-image-album').each(function(index) {
					    		var imageUrl = $(this).data('image');
								images.push(imageUrl.replace(cdnUrl, ''));
							});

							var jsonValue = !$.isEmptyObject(images) ? JSON.stringify(images) : '';
					      	wrapElement.find('input[type="hidden"]').val(jsonValue);
						}
					});
					albumElement.disableSelection();
				});
			}
		},
		video:{
			init: function(options){
				var self = this;
				var input = null;
				if(options.input != _UNDEFINED) input = options.input;
				if(input == null || input.length == 0) return;

				var wrapElement = input.closest('.wrap-video');
				if(wrapElement.length == 0) return;

				wrapElement.find('.btn-select-video').fancybox({
				   	closeExisting: true,
				   	iframe : {
				   		preload : false
				   	},
				   	afterClose: function(e){
				   		$(window).off('message', self.onSelectVideo);
				   	}
				});

				wrapElement.on('click', '.btn-select-video', function(e) {
					e.stopImmediatePropagation();

			    	$(window).on('message', self.onSelectVideo);
			    });
			    
			    var show = $('#type_video').val() == _VIDEO_SYSTEM ? true : false;
			    wrapElement.find('.btn-select-video').toggleClass('d-none', !show);

			    wrapElement.on('change', '#type_video', function(e) {
			    	e.stopImmediatePropagation();

			    	var show = $(this).val() == _VIDEO_SYSTEM ? true : false;
			    	wrapElement.find('.btn-select-video').toggleClass('d-none', !show);
			    	input.val('');		    	
			    });
			},
			onSelectVideo: function(e){
				var self = this;
				var event = e.originalEvent;
			   	if(event.data.sender === 'myfilemanager'){

			      	if(event.data.field_id){		      		
			      		var field_id = event.data.field_id;
			      		
			      		var input = $('#' + field_id);
			      		var url = event.data.url;
			      		if($.isArray(url)) url = typeof(url[0]) ? url[0] : '';

			      		if(input.length){
			      			$('#' + field_id).val(url.replace(cdnUrl, ''));
			      		}

						$.fancybox.close();
						$(window).off('message', self.onSelectVideo);
			      	}
			   	}
			}
		},
		file:{
			init: function(){
				var self = this;
				$(document).on('click', '.btn-select-file', function(e) {
					e.stopImmediatePropagation();

			    	$.fancybox.open({
			    		src: $(this).attr('data-src'),
					    type: 'iframe'
					});
			    	$(window).on('message', self.onSelectFile);
			    });

			    $(document).on('click', '.btn-clear-file', function(e) {
			    	e.stopImmediatePropagation();
			    	e.preventDefault();

			    	var wrap = $(this).closest('.wrap-files');
			    	var input = wrap.find('input');

			    	$(this).closest('.item-file').remove();

			    	var list_files = [];
			    	wrap.find('.item-file').each(function(index) {
						var fileUrl = $(this).data('file');
						list_files.push(fileUrl.replace(cdnUrl, ''));
					});

					var json_value = !$.isEmptyObject(list_files) ? JSON.stringify(list_files) : '';
			      	input.val(json_value);
			    });

			    self.sortItem();
			},
			template:{
				itemFile: function(url_file = null) {
					var icon_class = 'fa-file';
					var ext = url_file.substr((url_file.lastIndexOf('.') + 1));	      			
				    switch(ext) {
				        case 'jpg':
				        case 'png':
				        case 'gif':
				        case 'jpeg':
				        case 'svg':
				        case 'bmp':
				        case 'ico':
				        case 'webp':
				            icon_class = 'fa-file-image';
				        break;

				        case 'xlsx':
				        case 'xlsm':
				        case 'xls':
				            icon_class = 'fa-file-excel';
				        break;

				        case 'doc':
				        case 'docx':
				            icon_class = 'fa-file-word';
				        break;

				        case 'pdf':
				            icon_class = 'fa-file-pdf';
				        break;

				        case 'mp3':
				        case 'flac':
				        case 'm4a':
				            icon_class = 'fa-file-audio';
				        break;

				        case 'mp4':
				        case 'swf':
				        case 'avi':
				        case '3gp':
				        case 'mov':
				        case 'wmv':
				        case 'webm':
				            icon_class = 'fa-file-video';
				        break;
			     	}

					return '\
					<a href="'+ url_file +'" class="kt-media kt-media--lg mr-20 position-relative item-file" data-file="'+ url_file +'" target="_blank" >\
                        <i class="fa '+ icon_class +'"></i>\
                        <span class="btn-clear-file" title="'+ nhMain.getLabel('xoa_tep') +'">\
                            <i class="fa fa-times"></i>\
                        </span>\
                    </a>';
				}
			},
			onSelectFile: function(e){
				var self = this;
				var event = e.originalEvent;
			   	if(event.data.sender === 'myfilemanager'){

			   		if(event.data.field_id){		      		
			      		var field_id = event.data.field_id;
			      		var input = $('#' + field_id);
			      		var wrap = input.closest('.wrap-files');
			      		var listFile = wrap.find('.list-files');
			      		var url = event.data.url;
			      		
			      		if(nhMain.utilities.isJson(url)){
			      			url = nhMain.utilities.parseJsonToObject(url);
			      		}

			      		if(listFile.length && input.length && url.length) {			      			
			      			if($.isArray(url)) {
				      			$.each(url, function( index, file) {
					      			listFile.append(nhMain.selectMedia.file.template.itemFile(file));
								});
			      			} else {
			      				listFile.append(nhMain.selectMedia.file.template.itemFile(url));
			      			}
			      			
			      			var list_files = [];
			      			listFile.find('.item-file').each(function(index) {
								var fileUrl = $(this).data('file');
								list_files.push(fileUrl.replace(cdnUrl, ''));		
							});	

			      			var json_value = !$.isEmptyObject(list_files) ? JSON.stringify(list_files) : '';
			      			input.val(json_value);

			      			listFile.removeClass('d-none');			      	
			      		}

						$.fancybox.close();
						$(window).off('message', self.onSelectFile);
			      	}
			   	}
			},
			sortItem: function() {
				var self = this;

				$('.wrap-files').each(function(index) {
					var wrapElement = $(this);
				  	var viewFilesElement = $(this).find('.list-files');
				  	if(viewFilesElement.length == 0) return;

					viewFilesElement.sortable({
						containment: viewFilesElement,
						stop: function( event, ui ) {
							var files = [];
					    	viewFilesElement.find('.item-file').each(function(index) {
					    		var fileUrl = $(this).data('file');
								files.push(fileUrl.replace(cdnUrl, ''));
							});

							var jsonValue = !$.isEmptyObject(files) ? JSON.stringify(files) : '';
					      	wrapElement.find('input[type="hidden"]').val(jsonValue);
						}
					});
					viewFilesElement.disableSelection();	
				});
			}
		},
		dropzoneUpload: function(params = {}, callbackSuccess, callbackComplete) {

			if (typeof(callbackSuccess) != 'function') {
		        callbackSuccess = function () {};
		    }

		    if (typeof(callbackComplete) != 'function') {
		        callbackComplete = function () {};
		    }
	        var _id = '#' + params.id;
	        var _ext = params.ext;
	        var _maxFile = params.maxFile;

	        // set the preview element template
	        var previewNode = $(_id + " .dropzone-item-upload");
	        previewNode.id = "";
	        var previewTemplate = previewNode.parent('.dropzone-items').html();
	        previewNode.remove();

	        var dropzoneUpload = new Dropzone(_id, { // Make the whole body a dropzone
	            url: params.url, // Set the url for your upload script location
	            headers: {
	                'X-CSRF-Token': csrfToken
	            },
	            paramName: "file",
	            maxFiles: _maxFile,
	            parallelUploads: 5,
	            acceptedFiles: _ext,
	            previewTemplate: previewTemplate,
	            maxFilesize: 5, // Max filesize in MB
	            autoQueue: false, // Make sure the files aren't queued until manually added
	            previewsContainer: _id + " .dropzone-items", // Define the container to display the previews
	            clickable: _id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
	            dictInvalidFileType: nhMain.getLabel('sai_dinh_dang_tep_tin'),
	            dictFileTooBig: nhMain.getLabel('kich_thuoc_tep_tin_qua_lon'),
	            dictMaxFilesExceeded: nhMain.getLabel('vuot_qua_so_luong_file_tai_len'),
	        });

	        dropzoneUpload.on("sending", function(file, xhr, formData) {
			  	// Will send the filesize along with the file as POST data.
			  	formData.append("path", $('#path').val());
			});

			dropzoneUpload.on('success', function(response) {
	            var result = jQuery.parseJSON(response.xhr.response);
	            if(result.code === _SUCCESS) {
	            	callbackSuccess();            	
	            } else if(result.code === _ERROR){
	            	toastr.error(result.message);
	            }
	        });

	        // Hide the total progress bar when nothing's uploading anymore
	        dropzoneUpload.on("complete", function(progress) {
	            var thisProgressBar = _id + " .dz-complete";
	            setTimeout(function(){
	                $( thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress, " + thisProgressBar + " .dropzone-start").css('opacity', '0');
	            }, 300);
	            if(progress.status === _SUCCESS){
	            	var rs = jQuery.parseJSON(progress.xhr.response);
		            if(rs.code === _SUCCESS) {
		            	var thisComplete = "#uploadFile .dz-processing.dz-success.dz-complete";
	            		$( thisComplete + " .dropzone-complete").text(nhMain.getLabel('tai_len_thanh_cong'));
		            	callbackComplete();
		            }
	            } 
	        });

	        dropzoneUpload.on("addedfile", function(file) {
	            var htmlTemplate = $(".template-container");
	            htmlTemplate.remove();
	            // Hookup the start button
	            file.previewElement.querySelector(_id + " .dropzone-start").onclick = function() { dropzoneUpload.enqueueFile(file); };
	            $(document).find( _id + ' .dropzone-item').css('display', '');
	            $( _id + " .dropzone-upload, " + _id + " .dropzone-remove-all").css('display', 'inline-block');
	        });

	        // Update the total progress bar
	        dropzoneUpload.on("totaluploadprogress", function(progress) {
	            $(this).find( _id + " .progress-bar").css('width', progress + "%");
	        });

	        dropzoneUpload.on("sending", function(file) {
	            // Show the total progress bar when upload starts
	            $( _id + " .progress-bar").css('opacity', '1');
	            // And disable the start button
	            file.previewElement.querySelector(_id + " .dropzone-start").setAttribute("disabled", "disabled");
	        });

	        // Setup the buttons for all transfers
	        document.querySelector( _id + " .dropzone-upload").onclick = function() {
	            dropzoneUpload.enqueueFiles(dropzoneUpload.getFilesWithStatus(Dropzone.ADDED));
	        };

	        // Setup the button for remove all files
	        document.querySelector(_id + " .dropzone-remove-all").onclick = function() {
	            $( _id + " .dropzone-upload, " + _id + " .dropzone-remove-all").css('display', 'none');
	            dropzoneUpload.removeAllFiles(true);
	        };

	        // On all files completed upload
	        dropzoneUpload.on("queuecomplete", function(progress){
	            $( _id + " .dropzone-upload").css('display', 'none');
	        });

	        // On all files removed
	        dropzoneUpload.on("removedfile", function(file){
	            if(dropzoneUpload.files.length < 1){
	                $( _id + " .dropzone-upload, " + _id + " .dropzone-remove-all").css('display', 'none');
	            }
	        });
	    },
	    forBlockConfig: {
	    	type: null,
	    	init: function(e){
	    		var self = this;
				$('[btn-select-media-block]').fancybox({
				   	closeExisting: true,
				   	iframe : {
				   		preload : false
				   	},
				   	afterClose: function(e){
				   		$(window).off('message', self.onSelectImage);
				   	}
				});

				$(document).on('click', '[btn-select-media-block]', function(e) {
					self.type = $(this).attr('btn-select-media-block');
					
					$(window).on('message', self.onSelectImage, );			    	
			    });
	    	},
	    	onSelectImage: function(e){
				var self = this;
				var event = e.originalEvent;
			   	if(event.data.sender === 'myfilemanager'){
			      	if(event.data.field_id){
			      		var field_id = event.data.field_id;
			      		var url = event.data.url;
			      		
			      		var imageUrl = typeof(url[0]) != _UNDEFINED ? url[0] : null;
			      		if(self.type == 'cdn') imageUrl = imageUrl.replace(cdnUrl, '{CDN_URL}');

			      		if(imageUrl != null){
			      			$('#' + field_id).val(imageUrl.replace(cdnUrl, ''));
			      		}
										
						$.fancybox.close();
						$(window).off('message', self.onSelectImage);

						// copy url image
						var input_tmp = $('<input>');
						$('body').append(input_tmp);
						input_tmp.val(imageUrl).select();
						document.execCommand('copy');
						input_tmp.remove();

						toastr.success(nhMain.getLabel('da_copy_duong_dan_anh'));
			      	}
			   	}
			}
	    }
	},
	quickAdd: {
		idModal: '',
		idForm: '',
		idButtonSubmit: '',		
		init: function(params = {}, callback, validateForm){
			var self = this;

			self.idModal = typeof(params.idModal) != _UNDEFINED ? params.idModal : '';
			self.idForm = typeof(params.idForm) != _UNDEFINED ? params.idForm : '';
			self.idButtonSubmit = typeof(params.idButtonSubmit) != _UNDEFINED ? params.idButtonSubmit : '';

			var formEl = $(self.idForm);

		    if (typeof(callback) != 'function') {
		        callback = function () {};
		    }

		    if (typeof(validateForm) != 'function') {
		        validateForm = function (formEl) {
		        	return null;
		        };
		    }

			$(self.idModal).on('hidden.bs.modal', function () {
			  	self.clearModal();
			});

			$(document).on('click', self.idModal + ' ' + self.idButtonSubmit, function(e) {
				var validator = validateForm(formEl);
				if (validator != false && (validator == null || validator.form())) {
					KTApp.blockPage(blockOptions);

					var formData = formEl.serialize();
					nhMain.callAjax({
						url: formEl.attr('action'),
						data: formData
					}).done(function(response) {
						$(self.idModal).modal('hide');
						KTApp.unblockPage();
						
						callback(response);
					});
				}
				
			});

			$(self.idModal + ' input.number-input').each(function() {
				nhMain.input.inputMask.init($(this));
			});
		},
		clearModal: function(){
			var self = this;
			$(self.idModal).find('input').val('');
		},
	},
	tagSuggest: {
		tagify: null,
		init: function(){
			var self = this;
			var inputTagify = document.getElementById('tags');
			if(!nhMain.utilities.notEmpty(inputTagify)) return false;

	        self.tagify = new Tagify(inputTagify, {
	            whitelist: [],
	            templates : {
					dropdownItem( item ){
					    return `<div ${this.getAttributes(item)}
				                class='tagify__dropdown__item ${item.class ? item.class : ""}'
				                tabindex="0"
				                role="option">
								${item.value}								
							</div>`
					}
			  	}
	        });
	        self.tagify.on('input', self.onInput);
		},
		onInput: function(e){
			var tagify = nhMain.tagSuggest.tagify;
			var value = e.detail.value;
			tagify.settings.whitelist.length = 0; // reset the whitelist

			tagify.loading(true).dropdown.hide.call(tagify)

			nhMain.callAjax({
				async: true,
				url: adminPath + '/tag/auto-suggest',
				data:{
					keyword: value
				}
			}).done(function(whitelist) {
				tagify.settings.whitelist.splice(0, whitelist.length, ...whitelist)
  				tagify.loading(false).dropdown.show.call(tagify, value); // render the suggestions dropdown
			})
		}
	},
	scrollToAnchor: {
		listPosition: {},
		lastPosition: 0,
		heightOfHeader: 170,

		init: function(){
			var self = this;

			if($("[nh-anchor]").length == 0 || $('.kt-sticky-toolbar a[nh-to-anchor]').length == 0) return false;

			// init nh anchor
			$('[nh-anchor]').each(function(index){
				self.listPosition[$(this).attr("nh-anchor")] = $(this).first().offset().top;
			});
			self.lastPosition = self.listPosition[Object.keys(self.listPosition)[Object.keys(self.listPosition).length - 1]];

			// show anchor actived when start
			var startScrollTop = $(window).scrollTop();
			var startScrollBottom = $(window).scrollTop() + $(window).height();
			self.checkAnchorActived(startScrollTop, startScrollBottom);

			// event
			$(document).on('click', '.kt-sticky-toolbar a[nh-to-anchor]', function(e) {
				e.preventDefault();	
				self.scrollToAnchor($(this).attr('nh-to-anchor'));
			});			

			$(window).scroll(function(event){
				var scrollTop = $(this).scrollTop();
				var scrollBottom = $(this).scrollTop() + $(this).height();
				self.checkAnchorActived(scrollTop, scrollBottom);
			});
		},
		scrollToAnchor: function(object_name, params = {}){
			var anchor = $("[nh-anchor='"+ object_name +"']");
			if(anchor.length) {
			    $('html,body').animate({scrollTop: anchor.offset().top - 138}, 'slow');
			}
		},
		checkAnchorActived: function(scrollTop = 0, scrollBottom = 0){
			var self = this;
			$.each(self.listPosition, function(name, position) {
			    if (scrollTop >= (position - self.heightOfHeader)){
			    	self.toggleClassActive(name);
				}

				if(scrollBottom >= (self.lastPosition + 400)) {
					self.toggleClassActive(Object.keys(self.listPosition)[Object.keys(self.listPosition).length - 1]);
				}
			});
		},
		toggleClassActive: function(name){
			$(".kt-sticky-toolbar__item a[nh-to-anchor]").removeClass("active");
			$(".kt-sticky-toolbar__item a[nh-to-anchor=" + name + "]").addClass("active");
		}
	}	
}

var nhList = {
	init: function() {
		var self = this;
	},
	eventDefault: function(datatable = {}, params = {}, callback = null) {
		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

		datatable.on('kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated', function(e) {
            var ids = [];
            $(datatable[0]).find('tbody tr').each(function( index, element ) {
            	var id = $(this).find('input[type="checkbox"]:checked').val();
            	if(id > 0){
            		ids.push(id);
            	}            	
            })

            var count = ids.length;
            $('#nh-selected-number').html(count);
            if (count > 0) {
                $('#nh-group-action').collapse('show');
            } else {
                $('#nh-group-action').collapse('hide');
            }
        });

        var url = typeof(params.url) != _UNDEFINED ? params.url : {};

		if(typeof(url.delete) != _UNDEFINED && url.delete.length){
			$(document).on('click', '.nh-delete', function() {
			  	var _id = $(this).data('id');
				if(typeof(_id) == _UNDEFINED || _id.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
			    	return false;
			    }

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

			    		KTApp.blockPage(blockOptions);
			    		nhMain.callAjax({
							url: url.delete,
							data:{
								ids: [_id]
							}
						}).done(function(response) {
							KTApp.unblockPage();

							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
						    if (code == _SUCCESS) {
				            	$('.kt-datatable').KTDatatable('reload');
				            	toastr.info(message);
				            	callback(response);
				            } else {
				            	toastr.error(message);
				            }
						})
			    	}    	
			    });
				return false;
			});

			$(document).on('click', '#nh-group-action .nh-delete-all', function() {
				var _ids = [];
				$('.kt-datatable__table .select-record input:checked').each(function (i, checkbox) {
					var _id = nhMain.utilities.parseInt($(this).val());
					if(_id > 0){
						_ids.push(_id);
					}
			    });
					
				if(_ids.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi_da_chon'));
			    	return false;
			    }

			    swal.fire({
			        title: nhMain.getLabel('xoa_ban_ghi'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_nhung_ban_ghi_da_chon'),
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
							url: url.delete,
							data:{
								ids: _ids
							}
						}).done(function(response) {
							KTApp.unblockPage();

							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	$('.kt-datatable').KTDatatable('reload');
				            	toastr.info(message);
				            	callback(response);
				            } else {
				            	toastr.error(message);
				            }            
						})
			    	}    	
			    });
				return false;
			});
		}

        if(typeof(url.status) != _UNDEFINED && url.status.length){
			$(document).on('click', '.nh-change-status', function() {
			  	var _id = $(this).data('id');
			  	var _status = $(this).data('status');
				if(typeof(_id) == _UNDEFINED || _id.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
			    	return false;
			    }

			    swal.fire({
			        title: nhMain.getLabel('thay_doi_trang_thai'),
			        text: nhMain.getLabel('ban_chac_chan_muon_thay_doi_trang_thai_ban_ghi_nay'),
			        type: 'warning',
			        confirmButtonText: nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){

			    		KTApp.blockPage(blockOptions);
			    		nhMain.callAjax({
							url: url.status,
							data:{
								ids: [_id],
								status: _status
							}
						}).done(function(response) {
							KTApp.unblockPage();

							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	$('.kt-datatable').KTDatatable('reload');
				            	toastr.info(message);
				            	callback(response);
				            } else {
				            	toastr.error(message);
				            }            
						})
			    	}    	
			    });
				return false;
			});

			$(document).on('click', '#nh-group-action .nh-change-status-all', function() {
				var _ids = [];
				$('.kt-datatable__table .select-record input:checked').each(function (i, checkbox) {
					var _id = nhMain.utilities.parseInt($(this).val());
					if(_id > 0){
						_ids.push(_id);
					}
			    });
			  	var _status = $(this).data('status');

				if(_ids.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
			    	return false;
			    }

			    swal.fire({
			        title: nhMain.getLabel('thay_doi_trang_thai'),
			        text: nhMain.getLabel('ban_chac_chan_muon_thay_doi_trang_thai_ban_ghi_nay'),
			        type: 'warning',
			        confirmButtonText: nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		KTApp.blockPage(blockOptions);

			    		nhMain.callAjax({
							url: url.status,
							data:{
								ids: _ids,
								status: _status
							}
						}).done(function(response) {
							KTApp.unblockPage();

							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	$('.kt-datatable').KTDatatable('reload');
				            	toastr.info(message);
				            	callback(response);
				            } else {
				            	toastr.error(message);
				            }            
						})
			    	}    	
			    });
				return false;
			});
		}

		if(typeof(url.duplicate) != _UNDEFINED && url.duplicate.length){
			$(document).on('click', '.nh-duplicate', function() {
				var _id = $(this).data('id');
				if(typeof(_id) == _UNDEFINED || _id.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
			    	return false;
			    }				

			    swal.fire({
			        title: nhMain.getLabel('nhan_doi_ban_ghi'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_nhan_doi_ban_ghi_da_chon'),
			        type: 'warning',
			        
			        confirmButtonText: nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		KTApp.blockPage(blockOptions);

			    		nhMain.callAjax({
							url: url.duplicate,
							data:{
								ids: [_id]
							}
						}).done(function(response) {
							KTApp.unblockPage();

							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	$('.kt-datatable').KTDatatable('reload');
				            	toastr.info(message);
				            } else {
				            	toastr.error(message);
				            }            
						})
			    	}    	
			    });
				return false;
			});
		}

		if(typeof(url.position) != _UNDEFINED && url.position.length){
			$(document).on('click', '.kt-datatable .nh-change-position', function() {	
		    	var _id = $(this).data('id');
		    	var _position = $(this).data('position');
				var modalDetail = $('#change-position-modal');
				modalDetail.find('input#id_record').val(_id);
				typeof(_position) == 'number' ? modalDetail.find('input#position').val(_position) : modalDetail.find('input#position').val(null);
				modalDetail.modal('show');
			});

			$(document).on('click', '#btn-change-position', function() {
				KTApp.blockPage(blockOptions);

				var modalDetail = $('#change-position-modal');
				var _id = modalDetail.find('input#id_record').val();
		    	var _position = modalDetail.find('input#position').val();

			  	nhMain.callAjax({
					url: url.position,
					data:{
						id: _id,
						position: _position
					}
				}).done(function(response) {
					KTApp.unblockPage();

					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
				    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

				    modalDetail.modal('hide');
				    if (code == _SUCCESS) {
		            	$('.kt-datatable').KTDatatable('reload');
		            	toastr.info(message);
		            } else {
		            	toastr.error(message);
		            }
				});
			});
		}

		if(typeof(url.quickChange) != _UNDEFINED && url.quickChange.length){
			$(document).on('click', '.kt-datatable .nh-quick-change', function(e) {
        		var _this = $(this);
        		$('.kt-datatable .nh-quick-change').not(this).popover('hide');
				_this.popover({
	        		title: nhMain.getLabel('thay_doi_thong_tin'),
	    			placement: 'bottom',
	    			html: true,
	    			sanitize: false,
	    			trigger: 'manual',
		            content: $('#popover-quick-change').html(),
		           	template: '\
			            <div class="popover lg-popover" role="tooltip">\
			                <div class="arrow"></div>\
			                <h3 class="popover-header"></h3>\
			                <div class="popover-body"></div>\
			            </div>'
		        });
				var name = $(this).attr('data-change');
				var changeValue = $(this).attr('data-change-value').replace('...','');
				var label = $(this).attr('data-label');

		        _this.popover('show');
		        _this.on('shown.bs.popover', function (e) {		        	
		        	var idPopover = _this.attr('aria-describedby');
		        	var _popover = $('#' + idPopover);

		        	_popover.find('label').text(label);
		        	_popover.find('#value-change').val(changeValue);
		        	nhMain.input.inputMask.init(_popover.find('.number-input'), 'number');
				})
        	}); 

        	$(document).on('click', '#cancel-quick-change', function(e) {
				var idPopover = $(this).closest('.popover.lg-popover').attr('id');
				var btnPopover = $('.nh-quick-change[aria-describedby="'+ idPopover +'"]');
				btnPopover.popover('dispose');
			});

			$(document).on('click', '#confirm-quick-change', function(e) {
				KTApp.blockPage(blockOptions);
				var _popover = $(this).closest('.popover.lg-popover');
				var idPopover = _popover.attr('id');
				var btnPopover = $('.nh-quick-change[aria-describedby="'+ idPopover +'"]');

				var valueChange = _popover.find('#value-change').val();
				var nameChange = btnPopover.attr('data-change');
				var idChange = btnPopover.attr('data-id');

				nhMain.callAjax({
	                url: url.quickChange,
	                data: {
	                	id: idChange,
	                	value: valueChange,
	                	name: nameChange
	                }
	            }).done(function(response) {
	            	KTApp.unblockPage();  
	                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	                var data = typeof(response.data) != _UNDEFINED ? response.data : '';
	                toastr.clear();
	                if (code == _SUCCESS) {
	                    toastr.info(message);

	                    btnPopover.text(valueChange);
						btnPopover.attr('data-change-value', valueChange);
	                } else {
	                    toastr.error(message);
	                }            
	            });

				btnPopover.popover('dispose');
			});
		}
		
		if(typeof(url.note) != _UNDEFINED && url.note.length){
			$(document).on('click', '.kt-datatable .quick-change-note', function(e) {
        		var _this = $(this);
        		$('.kt-datatable .quick-change-note').not(this).popover('hide');
				_this.popover({
	        		title: nhMain.getLabel('thay_doi_thong_tin'),
	    			placement: 'bottom',
	    			html: true,
	    			sanitize: false,
	    			trigger: 'manual',
		            content: $('#popover-quick-change').html(),
		           	template: '\
			            <div class="popover lg-popover" role="tooltip">\
			                <div class="arrow"></div>\
			                <h3 class="popover-header"></h3>\
			                <div class="popover-body"></div>\
			            </div>'
		        });
				var name = $(this).attr('data-change');
				var changeValue = $(this).attr('data-change-value');
				var label = $(this).attr('data-label');

		        _this.popover('show');
		        _this.on('shown.bs.popover', function (e) {		        	
		        	var idPopover = _this.attr('aria-describedby');
		        	var _popover = $('#' + idPopover);

		        	_popover.find('label').text(label);
		        	_popover.find('#value-change').val(changeValue);
				})
        	}); 

        	$(document).on('click', '#cancel-quick-change', function(e) {
				var idPopover = $(this).closest('.popover.lg-popover').attr('id');
				var btnPopover = $('.quick-change-note[aria-describedby="'+ idPopover +'"]');
				btnPopover.popover('dispose');
			});

			$(document).on('click', '#confirm-quick-change', function(e) {
				KTApp.blockPage(blockOptions);
				var _popover = $(this).closest('.popover.lg-popover');
				var idPopover = _popover.attr('id');
				var btnPopover = $('.quick-change-note[aria-describedby="'+ idPopover +'"]');

				var valueChange = _popover.find('#value-change').val();
				var typeChange = btnPopover.attr('data-change');
				var idChange = btnPopover.attr('data-id');

				nhMain.callAjax({
	                url: url.note,
	                data: {
	                	id: idChange,
	                	value: valueChange,
	                	type: typeChange
	                }
	            }).done(function(response) {
	            	KTApp.unblockPage();  
	                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	                var data = typeof(response.data) != _UNDEFINED ? response.data : '';
	                toastr.clear();
	                if (code == _SUCCESS) {
	                    toastr.info(message);
	                    if ( typeChange == 'note') {
	                    	btnPopover.text(valueChange);
							btnPopover.attr('data-change-value', valueChange);
	                    }
	                    if ( typeChange == 'staff_note') {
	                    	btnPopover.text(valueChange);
							btnPopover.attr('data-change-value', valueChange);
	                    }
	                } else {
	                    toastr.error(message);
	                }            
	            });

				btnPopover.popover('dispose');
			});
		}

		$(document).on('click', '.kt-datatable .view-detail', function() {
			var modalDetail = $('#view-detail-modal');
			var url = $(this).attr('href');

			modalDetail.modal('show');
			KTApp.blockPage(blockOptions);

			if(url != _UNDEFINED && url.length > 0){
				nhMain.callAjax({
					url: url,
					data:{
						lang: nhMain.lang
					},
					dataType: 'html'
				}).done(function(response) {
					modalDetail.find('.modal-body').html(response);
					KTApp.unblockPage();
				});
			}
			return false;
		});

		$('#view-detail-modal').on('hidden.bs.modal', function (e) {
		  	$(this).find('.modal-body').html('');
		})

		$(document).on('click', '#btn-refresh-search', function() {
			KTApp.blockPage(blockOptions);
	    	$('.nh-search-advanced input').val('');
	    	$('.nh-search-advanced .kt-selectpicker').val('');
	    	$('.nh-search-advanced .kt-selectpicker').selectpicker('refresh');
			datatable.setDataSourceParam('query','');
	    	$('.kt-datatable').KTDatatable('load');
	    	KTApp.unblockPage();
		});

		$(document).on('click', '.collapse-search-advanced', function() {
			var wrapSearchAdvanced = $('.collapse-search-advanced-content');
			if(!nhMain.utilities.notEmpty(wrapSearchAdvanced)) return false;

			var arrowIcon = $(this).find('i');

			wrapSearchAdvanced.on('shown.bs.collapse', function () {
			   	arrowIcon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
			});

			wrapSearchAdvanced.on('hidden.bs.collapse', function () {
				arrowIcon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
			   
			});
		});
	},
	template: {
		groupAction: function(row = {}, urlEdit, urlView){
			return '\
				<div class="dropdown">\
					<a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
	                    <i class="la la-ellipsis-h"></i>\
	                </a>\
				  	<div class="dropdown-menu dropdown-menu-right">\
				    	<a class="dropdown-item" href="' + urlEdit + '"><i class="la la-edit w-20"></i>' + nhMain.getLabel('sua_thong_tin') + '</a>\
				    	<div class="dropdown-divider"></div>\
				    	<a class="dropdown-item nh-change-status" data-id="'+ row.id +'" data-status="1" href="javascript:;"><i class="la la-check text-success w-20 fs-15"></i>' + nhMain.getLabel('hoat_dong') + '</a>\
				    	<a class="dropdown-item nh-change-status" data-id="'+ row.id +'" data-status="0" href="javascript:;"><i class="la la-ban w-20 fs-15"></i>' + nhMain.getLabel('ngung_hoat_dong') + '</a>\
				    	<div class="dropdown-divider"></div>\
				    	<a class="dropdown-item nh-delete" data-id="'+ row.id +'" href="#"><i class="la la-trash-o text-danger w-20 fs-15"></i>' + nhMain.getLabel('xoa') + '</a>\
				  	</div>\
				</div>';
			},
		status: function(status){
			return '<span class="kt-badge ' + statusOptions[status].class + ' kt-badge--inline kt-badge--pill">' + statusOptions[status].title + '</span>';
		},
		statusProduct: function(status_product){
			return '<span class="kt-badge ' + statusProductOptions[status_product].class + ' kt-badge--inline kt-badge--pill">' + statusProductOptions[status_product].title + '</span>';
		},
		statusPromotionCoupon: function(status_promotion){
			return '<span class="kt-badge ' + statusPromotionCouponOptions[status_promotion].class + ' kt-badge--inline kt-badge--pill">' + statusPromotionCouponOptions[status_promotion].title + '</span>';
		},
		statusCustomerPointHistory: function(status_customer_point){
			return '<span class="kt-badge ' + statusCustomerPointHistoryOptions[status_customer_point].class + ' kt-badge--inline kt-badge--pill">' + statusCustomerPointHistoryOptions[status_customer_point].title + '</span>';
		},
		statusOrders: function(status){
			return '<span class="kt-badge ' + statusOrdersOptions[status].class + ' kt-badge--inline kt-badge--pill">' + statusOrdersOptions[status].title + '</span>';
		},
		draftProduct: function(draft) {
			return '<span class="kt-badge ' + draftProductOptions[draft].class + ' kt-badge--inline kt-badge--pill">' + draftProductOptions[draft].title + '</span>';
		},
		typePromotion: function(type){
			return  typePromotionOptions[type].title;
		},
		typeDiscountPromotion: function(type){
			return  typeDiscountPromotionOptions[type].title;
		},
		isPartner: function(is_partner){
			return '<span class="kt-font-bold text-' + isPartnerOptions[is_partner].class + '"><span class="kt-badge kt-badge--' + isPartnerOptions[is_partner].class + ' kt-badge--dot mr-5"></span>' + isPartnerOptions[is_partner].title + '</span>';
		},
		statusAffiliate: function(status_affiliate){
			return '<span class="kt-badge ' + statusAffiliateOptions[status_affiliate].class + ' kt-badge--inline kt-badge--pill">' + statusAffiliateOptions[status_affiliate].title + '</span>';
		},
		image: function(image = null){
			if(image != null && image.length > 0 && image != _NO_IMAGE){
				image = cdnUrl + image;
			}else{
				image = _NO_IMAGE;
			}
			return '\
                <div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
                    <div class="kt-user-card-v2__pic">\
                        <img src="' + image + '">\
                    </div>\
                </div>';
		},
		createdBy: function(row = {}, urlUserDetail){
			var created = '';
			if(KTUtil.isset(row, 'created') && row.created != null){
				created = nhMain.utilities.parseIntToDateTimeString(row.created);
			}

			var urlUserDetail = '#';
			var fullName = '';
			var user = KTUtil.isset(row, 'User') && row.User != null ? row.User : {};

			if(KTUtil.isset(user, 'full_name') && user.full_name != null){
				fullName = user.full_name;
			}

			if(KTUtil.isset(user, 'id') && user.id != null){
				urlUserDetail = adminPath + '/user/detail/' + user.id;
			}

			return '\
				<div class="kt-user-card-v2">\
					<div class="kt-user-card-v2__details">\
						<a href="'+ urlUserDetail +'" class="kt-user-card-v2__name">'+ fullName +'</a>\
						<span class="kt-user-card-v2__desc">'+ created +'</span>\
					</div>\
				</div>';
		},
		changePosition: function(row = {}) {
			var position = KTUtil.isset(row, 'position') && row.position != null ? row.position : '-';
			return '\
				<button data-id="' + row.id + '" data-position="' + position + '" type="button" class="border-0 btn btn-outline-hover-brand btn-sm btn-icon nh-change-position"><i class="la la-edit"></i></button>\
					 ' + position + '';
		},
		changeQuick: function(id = null, name = null, value = null, label = null) {
			return '\
				<div data-toggle="popover" data-id="' + id + '" data-change="' + name + '" data-change-value="' + value + '" data-label="' + label + '" class="cursor-p label-value nh-quick-change ">' + value + '</div>';
		},
		changeKiotviet: function(id = null, name = null, value = null, label = null) {
			return '\
				<div data-toggle="popover" data-id="' + id + '" data-change="' + name + '" data-change-value="' + value + '" data-label="' + label + '" class="cursor-p label-value nh-kiotviet-change">' + value + '</div>';
		},
		changeNote: function(id = null, name = null, value = null, label = null) {
			return '\
				<div data-toggle="popover" data-id="' + id + '" data-change="' + name + '" data-change-value="' + value + '" data-label="' + label + '" class="cursor-p label-value quick-change-note ">' + value + '</div>';
		},
		changeImage: function(id = null, images = {}, imageAvatar = null) {
			var templateImage = '<div class="symbol-group symbol-hover">';

			if(nhMain.utilities.notEmpty(imageAvatar)) {
				var imageAvatarThumb = nhMain.utilities.notEmpty(imageAvatar) ? nhMain.utilities.getThumbs(imageAvatar, 150) : '';

				templateImage += '\
				<a class="symbol symbol-circle" href="'+ cdnUrl + imageAvatar +'" data-lightbox="'+ id +'">\
					<img src="'+ cdnUrl + imageAvatarThumb +'">\
				</a>';
			}

			if(nhMain.utilities.notEmpty(images)) {
				var classDisplay = 'd-none';
				var content = '';

				$.each(nhMain.utilities.parseJsonToObject(images), function(index, urlImage) {
					if(index <= 1){
						classDisplay = '';
						content = '<img src="'+ cdnUrl + nhMain.utilities.getThumbs(urlImage, 150) +'">';
					}

					if(index == 2){
						classDisplay = '';
						content = '<span class="symbol-label font-weight-bold">+'+ (nhMain.utilities.parseJsonToObject(images).length - 2).valueOf() +'</span>';
					}

					if(index > 2){
						classDisplay = 'd-none';
						content = '';
					}

					templateImage += '\
						<a class="'+ classDisplay +' symbol symbol-circle" href="'+ cdnUrl + urlImage +'" data-lightbox="'+ id +'">\
					  		'+ content +'\
					  	</a>';

				});
			}

			templateImage += '\
				<div upload-id="' + id+ '" class="symbol symbol-circle" data-toggle="kt-tooltip" title="'+ nhMain.getLabel('tai_anh') +'"></i>\
					<span class="symbol-label font-weight-bold"><i class="fa fa-cloud-upload-alt"></i></span>\
				</div>\
			</div>';
			return templateImage;
		}
	}
}

// options default
var blockOptions = {
    overlayColor: '#000000',
    type: 'v2',
    state: 'success',
    message: nhMain.getLabel('vui_long_cho') + '...'
}

var statusOptions = {
	'-1': {'title': nhMain.getLabel('cho_duyet'), 'class': ' kt-badge--warning kt-font-bold'},
	0: {'title': nhMain.getLabel('ngung_hoat_dong'), 'class': ' kt-badge--danger kt-font-bold'},
	1: {'title': nhMain.getLabel('hoat_dong'), 'class': ' kt-badge--success kt-font-bold'}
};

var typePromotionOptions = {
	'promotion': {'title': nhMain.getLabel('chuong_trinh_khuyen_mai')},
	'coupon': {'title': nhMain.getLabel('chuong_trinh_coupon')}
};

var typeDiscountPromotionOptions = {
	'discount_order': {'title': nhMain.getLabel('chiet_khau_don_hang')},
	'discount_product': {'title': nhMain.getLabel('chiet_khau_san_pham')},
	'free_ship': {'title': nhMain.getLabel('mien_phi_van_chuyen')},
	'give_product': {'title': nhMain.getLabel('tang_san_pham')}
};

var statusProductOptions = {
	'-1': {'title': nhMain.getLabel('cho_duyet'), 'class': ' kt-badge--warning kt-font-bold'},
	0: {'title': nhMain.getLabel('ngung_hoat_dong'), 'class': ' kt-badge--danger kt-font-bold'},
	1: {'title': nhMain.getLabel('hoat_dong'), 'class': ' kt-badge--success kt-font-bold'},
	2: {'title': nhMain.getLabel('ngung_kinh_doanh'), 'class': ' kt-badge--warning kt-font-bold'},
};

var statusAffiliateOptions = {
	0: {'title': nhMain.getLabel('khong_duyet'), 'class': ' kt-badge--danger kt-font-bold'},
	1: {'title': nhMain.getLabel('da_kich_hoat'), 'class': ' kt-badge--success kt-font-bold'},
	2: {'title': nhMain.getLabel('cho_duyet'), 'class': ' kt-badge--warning kt-font-bold'},
};

var isPartnerOptions = {
	0: {'title': nhMain.getLabel('chua_kich_hoat'), 'class': 'danger'},
	1: {'title': nhMain.getLabel('da_kich_hoat'), 'class': 'success'},
	2: {'title': nhMain.getLabel('cho_duyet'), 'class': 'warning'},
};

var statusPromotionCouponOptions = {
	0: {'title': nhMain.getLabel('ngung_hoat_dong'), 'class': ' kt-badge--danger kt-font-bold'},
	1: {'title': nhMain.getLabel('hoat_dong'), 'class': ' kt-badge--success kt-font-bold'},
	2: {'title': nhMain.getLabel('da_su_dung'), 'class': ' kt-badge--warning kt-font-bold'},
}

var statusCustomerPointHistoryOptions = {
	0: {'title': nhMain.getLabel('huy'), 'class': ' kt-badge--danger kt-font-bold'},
	1: {'title': nhMain.getLabel('thanh_cong'), 'class': ' kt-badge--success kt-font-bold'},
	2: {'title': nhMain.getLabel('cho_duyet'), 'class': ' kt-badge--warning kt-font-bold'},
}

var draftProductOptions = {
	1: {'title': nhMain.getLabel('ban_luu_nhap'), 'class': ' kt-badge--dark kt-font-bold'},
};

var statusOrdersOptions = {};
statusOrdersOptions[_DRAFT] = {'title': nhMain.getLabel('chua_xac_nhan'), 'class': ' kt-badge--dark kt-font-bold'};
statusOrdersOptions[_NEW_ORDER] = {'title': nhMain.getLabel('don_moi'), 'class': ' kt-badge--danger kt-font-bold'};
statusOrdersOptions[_CONFIRM] = {'title': nhMain.getLabel('xac_nhan'), 'class': ' kt-badge--warning kt-font-bold'};
statusOrdersOptions[_PACKAGE] = {'title': nhMain.getLabel('dong_goi'), 'class': ' kt-badge--brand kt-font-bold'};
statusOrdersOptions[_EXPORT] = {'title': nhMain.getLabel('xuat_kho'), 'class': ' kt-badge--brand kt-font-bold'};
statusOrdersOptions[_DONE] = {'title': nhMain.getLabel('thanh_cong'), 'class': ' kt-badge--success kt-font-bold'};
statusOrdersOptions[_CANCEL] = {'title': nhMain.getLabel('don_huy'), 'class': ' kt-badge--dark kt-font-bold'};
statusOrdersOptions[_WAITING_RECEIVING] = {'title': nhMain.getLabel('cho_nhan_hang'), 'class': ' kt-badge--danger kt-font-bold'};
statusOrdersOptions[_RECEIVED] = {'title': nhMain.getLabel('da_nhan_hang'), 'class': ' kt-badge--brand kt-font-bold'};
statusOrdersOptions[_CUSTOMER_CANCEL] = {'title': nhMain.getLabel('khach_hang_huy'), 'class': ' kt-badge--dark kt-font-bold'};

$(document).ready(function() {
	nhMain.init();
});
