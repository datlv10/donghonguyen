"use strict";

var htmlListLang = '';
if(!$.isEmptyObject(listLanguage)){
	htmlListLang += '<div class="list-flags head-flags text-center" data-toggle="kt-tooltip" title="'+ nhMain.getLabel('danh_sach_theo_ngon_ngu') +'">';
	$.each(listLanguage, function(code, name) {
		var flagDefault = '';
		if(nhMain.lang == code){
			flagDefault = 'flag-default';
		}
	  	htmlListLang += '<a href="?lang='+ code +'"><img src="'+ _FLAGS + code + '.svg" alt="'+ name +'" class="flag ' + flagDefault + '"></a>'
	});
	htmlListLang += '</div>';
}

var quickUpload = {
	idModal: '#quick-upload',
	inputUpload: 'input#album',
	init: function(){
		var self = this;

		nhMain.selectMedia.album.init();

		$(document).on('click', '[upload-id]', function() {
			$(self.idModal).modal('show');

			var id = $(this).attr('upload-id');

			if(!nhMain.utilities.notEmpty(id)) return false;

			self.loadContentModalUpload(id);

		});

		$(document).on('click', '.btn-quick-upload', function() {
			var id = $(self.idModal).find('input[name="album"]').attr('upload-id');
			var listImages = $(self.idModal).find('input[name="album"]').val();
			
			KTApp.blockPage(blockOptions);
			if(id != _UNDEFINED && id.length > 0){
				nhMain.callAjax({
					url: adminPath + '/product/quick-upload',
					data: {
						id: id,
						images: listImages
					}
				}).done(function(response) {
					KTApp.unblockPage();					
					
					$(self.idModal).modal('hide');
					self.loadTable(id, images);

					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	if (code == _SUCCESS) {
		        		var images = typeof(data.images) != _UNDEFINED ? data.images : {};

						self.loadTable(id, images);
						$(self.idModal).modal('hide');
		        	}else{
		        		toastr.error(message);
		        	}
				});
			}
			return false;
		});

		$(self.idModal).on('hidden.bs.modal', function () {
  		  	self.clearModal();
  		});
	},
	loadTable: function(id = null, images = null){
		var self = this;

		var templateImage =  nhList.template.changeImage(id, images);
		$('.kt-datatable').find('[upload-id="' + id + '"]').closest('.symbol-group.symbol-hover').html(templateImage);
	},

	loadContentModalUpload: function(id = null) {
 		var self = this;

 		var modalBody = '.modal-body';
 		var listAlbum = '.list-image-album';
 		var wrapAlbum = '.wrap-album';

		if(id != _UNDEFINED && id.length > 0){
			nhMain.callAjax({
				url: adminPath + '/product/upload-modal/' + id,
				dataType: 'html'
			}).done(function(response) {
				$(self.idModal).find(modalBody).html(response);  
				$(document).ready(function() { 
					$(listAlbum).sortable({
						stop: function( event, ui ) {
							var list_images = [];
					    	$(listAlbum).find('.item-image-album').each(function(index) {
					    		var imageUrl = $(this).data('image');
								list_images.push(imageUrl.replace(cdnUrl, ''));
							});

							var json_value = !$.isEmptyObject(list_images) ? JSON.stringify(list_images) : '';
					      	$(wrapAlbum).find('input').val(json_value);
						}
					});
					$(listAlbum).disableSelection();
				});
			});
		}
	},

	clearModal: function() {
		var self = this;

		$(self.idModal).find('input').val('');
		$(self.idModal).find('.item-image-album').remove();
	}
}


var importExcel = {
	idModal: '#import-excel-modal',
	init:function() {
		var self = this;

		$(document).on('click', '#btn-import-excel:not(.disabled)', function(e) {
			var btnImport = $(this);

			// show loading
			btnImport.find('.icon-spinner').removeClass('d-none');
			$(this).addClass('disabled');

			var file_input = $('#excel_file');
            var file_data = file_input[0].files;
            if(file_input.length == 0){
				nhMain.showLog(nhMain.getLabel('khong_tim_thay_du_lieu_file'));
			}

			var formData = new FormData();
            $.each(file_data, function(index, file) {
				formData.append("excel_file", file);				
			});

            nhMain.callAjax({
	    		async: true,
				url: adminPath + '/product/import-excel',
				data: formData,
				contentType: false,
				processData: false,
			}).done(function(response) {
				KTApp.unblockPage();					

				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	var folder = typeof(data.folder) != _UNDEFINED ? data.folder : '';
	        	
	        	if (code == _SUCCESS) {
	        		self.import(0, folder, function(e) {
						// remove loading
			            btnImport.find('.icon-spinner').addClass('d-none');
						$(this).removeClass('disabled');
					});
	        	}else{
	        		$(self.idModal).find('.alert.alert-danger').removeClass('d-none');
	            	$(self.idModal).find('.alert.alert-danger').text(message);
	            	toastr.error(message);
	        	}
			});

			return false;
		});
        
        $('#import-excel-modal').on('hidden.bs.modal', function () {
		  	location.reload();
		});
	},

	import: function(page = 0, folder = null, callback = null){
		var self = this;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

		var data = {
        	page: page,
        	folder: folder
        }

		nhMain.callAjax({
            url: adminPath + '/product/process-import-excel',
            data: data
        }).done(function(response) {
        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : '';
        	var folder = typeof(data.folder) != _UNDEFINED ? data.folder : '';
        	var percent = typeof(data.percent) != _UNDEFINED ? data.percent : 0;

        	if (code == _SUCCESS) {
        		if(typeof(data.product) != _UNDEFINED && data.product > 0){
        			$('#current-item-excel').html(data.product);
        		}

        		$(self.idModal).find('.progress').removeClass('d-none');
				$(self.idModal).find('.progress .progress-bar').css('width', `${percent}%`);
				$(self.idModal).find('.progress .progress-bar').text(`${percent}%`);

        		if(typeof(data.continue) != _UNDEFINED && data.continue){
        			self.import(data.page, folder, callback);
        		}else{     		
        			$(self.idModal).find('.alert.alert-success').removeClass('d-none');	
        			toastr.success(message);
        			callback(response);
        		}

            } else {
            	$(self.idModal).find('.alert.alert-danger').removeClass('d-none');
            	$(self.idModal).find('.alert.alert-danger').text(message);
            	toastr.error(message);
            }
        });
	}
}

var discountProduct = {
	validator: null,
	idModal: '#discount-product-modal',
	formElement: $("form#discount-product"),
	init:function() {
		var self = this;

		if(self.idModal.length == 0 || self.formElement.length == 0) return false;

		$('.kt-select-multiple').select2();
		self.validation();

		$(document).on('click', self.idModal + ' #btn-apply-discount-product', function(e) {
			e.preventDefault();

			if (self.validator.form()) {
				KTApp.blockPage(blockOptions);
				var formData = self.formElement.serialize();

		        KTApp.blockPage();
		        self.validation()
		        nhMain.callAjax({
					url: self.formElement.attr('action'),
					data: formData
				}).done(function(response) {
					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

		        	toastr.clear();
		            if (code == _SUCCESS) {
						$(self.idModal).modal('hide');
						location.reload();
		            } else {
		            	toastr.error(message);
		            }
					KTApp.unblockPage();
				});
			}
		});
	},
	validation: function(){
        var self = this;

        self.validator = self.formElement.validate({
            ignore: ':hidden',
            rules: {
                discount_percent: {
                    required: true,
                    number: true,
                    min: 0,
                    max: 100
                }
            },
            messages: {
                discount_percent: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    min: nhMain.getLabel('gia_khuyen_mai_lon_hon_hoac_bang_0'),
                    max: nhMain.getLabel('gia_khuyen_mai_nho_hon_hoac_bang_100'),
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
			}
        });
    },
}

var useKiotviet = $('#kiotviet').length != 0 && $('#kiotviet').val() > 0 ? 1 : 0;

var htmlKiotvietTitle = ``;
if(useKiotviet > 0){
	htmlKiotvietTitle = `
	<div class="col-12 col-md-2">
		KiotViet
	</div>
	<div class="col-12 col-md-2">
		${nhMain.getLabel('so_luong_kiotviet')}
	</div>
	`;
}else{
	htmlKiotvietTitle = `
	<div class="col-12 col-md-2 ">
		${nhMain.getLabel('so_luong')}
	</div>
	`;	
}

var htmlTitle =`
<div class="row p-0 label-item-product">
	<div class="col-12 col-md-2">
		${nhMain.getLabel('thuoc_tinh')} 
	</div>
	<div class="col-12 col-md-3">
		<div class="d-inline ml-10" data-toggle="kt-tooltip" title="${nhMain.getLabel('danh_sach_anh')}">
			<i class="fa fa-image fa-lg "></i>
		</div>
	</div>
	<div class="col-12 col-md-3">
		${nhMain.getLabel('gia_gia_dac_biet')}
	</div>		
	${htmlKiotvietTitle}
	
</div>`;


var nhListProduct = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/product/list/json',
					headers: {
						'X-CSRF-Token': csrfToken
					},
					map: function(raw) {
						var dataSet = raw;
						if (typeof raw.data !== _UNDEFINED) {
							dataSet = raw.data;
						}

						if(typeof(raw.extend) != _UNDEFINED && typeof(raw.extend) != _UNDEFINED){
							extend = raw.extend;
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
			status: $('#nh-status').val(),
			id_categories: $('#id_categories').length > 0 ? [$('#id_categories').val()] : [],
			price_from: $('#price_from').val(),
			price_to: $('#price_to').val(),
			creator: $('#creator_id').val(),
			product_mark: $('#product_mark').val(),
			display_product: $('#display_product').val(),
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
				class: 'w-kt-table w-md-3 w-xs-10',
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
				autoHide: false,
				sortable: false,
			},
			{
				field: 'name',
				class: 'w-kt-table w-md-25 w-xs-90',
				title: nhMain.getLabel('ten_san_pham'),
				autoHide: false,
				template: function(row) {
					var name = typeof(row.name) != _UNDEFINED && row.name != null ? row.name : '';
					var url = typeof(row.url) != _UNDEFINED && row.url != null ? row.url : '';
					var urlEdit = adminPath + '/product/update/' + row.id;
					var urlDetail = adminPath + '/product/detail/' + row.id;

					var preView = ''
					if(url.length > 0){
						preView = '<span data-toggle="kt-tooltip" title="'+ nhMain.getLabel('xem_san_pham') +'" class="view-template kt-margin-l-5"><a target="_blank" href="/'+ url +'"><i class="fa fa-eye"></i></a></span>';
					}
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="d-inline kt-user-card-v2__name">'+ name +'</a>' + preView + '\
								<span class="d-block kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="text-info action-item nh-duplicate" data-id="'+ row.id +'">'+ nhMain.getLabel('nhan_ban') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
							</div>\
						</div>';
				}
			},
			{
				field: 'lang',
				title: htmlListLang,
				class: useMultipleLanguage ? 'w-kt-table w-md-10' : 'd-none',
				sortable: false,
				textAlign: 'center',
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
				template: function(row) {
					var mutiple_language = nhMain.utilities.notEmpty(row.mutiple_language) ? row.mutiple_language : [];
					var templateLanguage = '';
					var urlTranslate = adminPath + '/product/update/' + row.id;
					var templateLanguage = '<div class="list-flags">';
					$.each(listLanguage, function(code, name) {
						var flag_class = '';
						if(typeof(mutiple_language[code]) != _UNDEFINED && mutiple_language[code]){
							flag_class = 'text-primary';
						}
					  	templateLanguage += '<a href="'+ urlTranslate + '?lang=' + code +'" class="fa fa-pencil-alt flag ' + flag_class + '" data-toggle="kt-tooltip" title="'+ nhMain.getLabel('dich_sang') + ' ' + name +'">'
					});
					templateLanguage += '</div>';
					return templateLanguage;
				},
			},
			{
				field: 'item_product',
				class: 'w-kt-table w-md-50',
				title: `${htmlTitle}`,
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
				sortable: false,
					template: function (row) {
					var html = '';
					if(nhMain.utilities.notEmpty(row.items)){
						var hide = row.items.length > 5 ? true : false;
						var rowHtml = hide ? '<div class="wrap-items d-none">' : '<div class="wrap-items">';						
						$(row.items).each(function(i, item) {
							var product_item_id = nhMain.utilities.notEmpty(item.id) ? item.id : '';
							var code = nhMain.utilities.notEmpty(item.code) ? item.code : '';

							var htmlImages = nhList.template.changeImage(product_item_id, JSON.stringify(item.images));

							var price = nhMain.utilities.notEmpty(item.price) ? nhMain.utilities.parseNumberToTextMoney(item.price) : '';
							var htmlPrice = '\
							<span data-toggle="kt-tooltip" title="' + nhMain.getLabel('gia_san_pham') + '">' 
							+ nhList.template.changeQuick(product_item_id, 'price', price, nhMain.getLabel('gia')) 
							+ '</span>';

							var statusSpecial = {
								true: 'data-toggle="kt-tooltip" title="'+ nhMain.getLabel('gia_dac_biet_da_duoc_ap_dung') +'" class="kt-font-info"',
								false: 'data-toggle="kt-tooltip" title="'+ nhMain.getLabel('gia_dac_biet_khong_duoc_ap_dung') +'" class="kt-font-danger"'
							}

							var price_special = typeof(item.price_special) != _UNDEFINED && item.price_special > 0 ? nhMain.utilities.parseNumberToTextMoney(item.price_special) : '...';

							var htmlApplySpecial = '';
							if(typeof(item.price_special) != _UNDEFINED){
								htmlApplySpecial = statusSpecial[item.apply_special];
							}

							if(item.price_special == 0){
								htmlApplySpecial = '';
							}

							var htmlPriceSpecial = ' / <span ' + htmlApplySpecial + '>' 
							+ nhList.template.changeQuick(product_item_id, 'price_special', price_special, nhMain.getLabel('gia_dac_biet')) 
							+ '</span>';
							
							if(useKiotviet > 0) {
								if(item.total_quantity_kiotviet >= 0 && item.kiotviet_code != null){
									
									var quantity_partners = typeof(item.total_quantity_kiotviet) != _UNDEFINED && item.total_quantity_kiotviet > 0 ? nhMain.utilities.parseNumberToTextMoney(item.total_quantity_kiotviet) : '0';
									
									var htmlItemQuantity = '';
									$(item.kiotviet_store).each(function(i, item_kiotviet_store) {
										var quantity_kiotviet = nhMain.utilities.notEmpty(item_kiotviet_store.quantity) ? nhMain.utilities.parseNumberToTextMoney(item_kiotviet_store.quantity) : '';
									  	htmlItemQuantity +=`\
								  		<div class='tooltip-quantity'> 
								       		<span class='pr-2'>
								       			${item_kiotviet_store.name}:
								       		</span>

								       		<span class='kt-font-info'>
								       			${quantity_kiotviet}
								       		</span>
								       	</div>`
									});
								}else{
									var quantity_partners = typeof(item.total_quantity_kiotviet) != _UNDEFINED && item.total_quantity_kiotviet > 0 ? item.total_quantity_kiotviet : '...';
									var htmlItemQuantity = `\
									<div class='item-quantity'> 
							       		${nhMain.getLabel('so_luong_kiotviet')}
							       	</div>`
								}

								var htmlQuantity = `\
								<span class="tooltip-partner-quantity" data-toggle="kt-tooltip" data-html="true" title="${htmlItemQuantity}">
									${quantity_partners}
								</span>`;
							} else {
								var quantity_available = typeof(item.quantity_available) != _UNDEFINED && item.quantity_available > 0 ? item.quantity_available : '...';
								var htmlQuantity = '\
								<span data-toggle="kt-tooltip" title="' + nhMain.getLabel('thay_doi_so_luong') +'">' 
								+ nhList.template.changeQuick(product_item_id, 'quantity_available', quantity_available, nhMain.getLabel('so_luong')) 
								+ '</span>';
							}

							var htmlAttribute = nhMain.utilities.notEmpty(item.attribute_name) ? item.attribute_name : '';

							var statusOptions = {
								0: {'class': 'kt-badge kt-badge--danger kt-badge--dot', 'tooltipHtml': 'data-toggle="kt-tooltip" title="'+ nhMain.getLabel('ngung_hoat_dong') +'"'},
								1: {'class': 'kt-badge kt-badge--success kt-badge--dot', 'tooltipHtml': 'data-toggle="kt-tooltip" title="'+ nhMain.getLabel('hoat_dong') +'"'}
							};
							htmlCodeKiotviet= '';
							if(useKiotviet > 0) {
								var kiotviet_code = typeof(item.kiotviet_code) != _UNDEFINED && item.kiotviet_code != null ? item.kiotviet_code : '...';
								var htmlCodeKiotviet = `
								<div class="col-12 col-md-2">
									<span data-toggle="kt-tooltip" title="${nhMain.getLabel('ma_kiotviet')}">
									${nhList.template.changeKiotviet(product_item_id, 'kiotviet_code', kiotviet_code, nhMain.getLabel('ma_kiotviet'))}
									</span>
								</div>
								`;
							}
						
						  	rowHtml += `
							  	<div class="row p-0 list-item-product">
							  		<div class="col-12 col-md-2">
							  			<div class="product-attribute">${htmlAttribute}</div>\
							  		</div>
							  		<div class="col-12 col-md-3">
							  			${htmlImages}
							  		</div>
							  		<div class="col-12 col-md-3">
							  			${htmlPrice} ${htmlPriceSpecial}
							  		</div>
							  		${htmlCodeKiotviet}
							  		<div class="col-12 col-md-2">
							  			${htmlQuantity}
							  		</div>
							  		
							  	</div>`;
						});
						rowHtml += '</div>';

						if(hide){
							rowHtml += '<span class="show-list-item text-primary cursor-p">' + row.items.length + ' ' + nhMain.getLabel('phien_ban') + '</span>'
							rowHtml += '<span class="hide-list-item text-primary cursor-p mt-20 d-none">' + nhMain.getLabel('an_tat_ca_phien_ban') + '</span>'
						}
						html += rowHtml;
					}

					return html;
				}
			},
			{
				field: 'position',
				class: 'w-kt-table w-md-5',
				title: nhMain.getLabel('vi_tri'),
				sortable: true,
				textAlign: 'center',
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
				template: function (row) {
					var position = '';
					if(KTUtil.isset(row, 'position') && row.position != null){
						position = nhMain.utilities.parseNumberToTextMoney(row.position);
					}
					return nhList.template.changeQuick(row.id, 'position', position, nhMain.getLabel('vi_tri'));
				}
			},
			{
				field: 'status',
				class: 'w-kt-table w-md-10',
				title: nhMain.getLabel('trang_thai'),
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
				template: function(row) {
					var status = '';
					var draftProduct = '';
					if(KTUtil.isset(row, 'status') && row.status != null && row.draft != 1){
						status = nhList.template.statusProduct(row.status);
					}
					
					if((KTUtil.isset(row, 'draft') && row.draft == 1)){
						draftProduct = nhList.template.draftProduct(row.draft);
					}
					return status + draftProduct;
				},
			}
		]
	}

	return {
		listData: function() {
			lightbox.option({
              'resizeDuration': 200,
              'wrapAround': true,
              'albumLabel': ' %1 '+ nhMain.getLabel('tren') +' %2'
            });

			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
  			});

			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			var datatable = $('.kt-datatable').KTDatatable(options);
			
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });		   	

		    $('#id_categories').on('change', function() {
		      	datatable.search($(this).val().length > 0 ? [$(this).val()] : [], 'id_categories');
		    });	

		    $('#price_from').on('change', function() {
		      	datatable.search($(this).val(), 'price_from');
		    });	

		    $('#price_to').on('change', function() {
		      	datatable.search($(this).val(), 'price_to');
		    });	   

		    $('#id_brands').on('change', function() {
		      	datatable.search([$(this).val()], 'id_brands');
		    });

		    $('#created_by').on('change', function() {
		      	datatable.search($(this).val(), 'created_by');
		    });	

		    $('#product_mark').on('change', function() {
		      	datatable.search($(this).val(), 'product_mark');
		    });	

		    $('#display_product').on('change', function() {
		      	datatable.search($(this).val(), 'display_product');
		    });

		    $('#stocking').on('change', function() {
		      	datatable.search($(this).val(), 'stocking');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	

		    $('#has_image').on('change', function() {
		      	datatable.search($(this).val(), 'has_image');
		    });	

		    //event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/product/delete',
			    	status: adminPath + '/product/change-status',
			    	duplicate: adminPath + '/product/duplicate',
			    	quickChange: adminPath + '/product/quick-change',
			    }
		    });

		    $(document).on('click', '.kt-datatable .show-list-item', function() {
		    	var td = $(this).closest('td');
		    	$(this).addClass('d-none');

				td.find('.wrap-items').removeClass('d-none');
				td.find('.hide-list-item').removeClass('d-none').addClass('d-block');
			});

			$(document).on('click', '.kt-datatable .hide-list-item', function() {
		    	var td = $(this).closest('td');
		    	$(this).addClass('d-none').removeClass('d-block');

				td.find('.wrap-items').addClass('d-none');
				td.find('.show-list-item').removeClass('d-none')
			});

			$(document).on('click', '[nh-dowload-file-import]', function(e) {
				e.preventDefault();
				KTApp.blockPage(blockOptions);

				nhMain.callAjax({
					url: adminPath + '/product/download-file-import',
				}).done(function(response) {
					KTApp.unblockPage();
					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					var message = typeof(response.message) != _UNDEFINED ? response.message : '';
					var name = typeof(response.meta.name) != _UNDEFINED ? response.meta.name : '';

					var $tmp = $("<a>");
		            $tmp.attr("href",response.data);
		            $("body").append($tmp);
		            $tmp.attr("download", name + '.xls');
		            $tmp[0].click();
		            $tmp.remove();

					if (code == _SUCCESS) {
		            	toastr.info(message);
		            } else {
		            	toastr.error(message);
		            }
				});
		
				return false;
			});	

			$(document).on('click', '[nh-export]', function(e) {
                e.preventDefault();
                KTApp.blockPage(blockOptions);
                var nhExport = typeof($(this).attr('nh-export')) != _UNDEFINED ? $(this).attr('nh-export') : '';
                var page = typeof(datatable.getCurrentPage()) != _UNDEFINED ? datatable.getCurrentPage() : 1;

                var data_filter = {
					lang: nhMain.lang,
					keyword: $('#nh-keyword').val(),
					status: $('[name=status]').val(),
					id_categories: $('#id_categories').val().length > 0 ? [$('#id_categories').val()] : [],
					price_from: $('#price_from').val(),
					price_to: $('#price_to').val(),
					creator: $('#creator_id').val(),
					product_mark: $('#product_mark').val(),
					display_product: $('#display_product').val(),
				}

                nhMain.callAjax({
                    url: adminPath + '/product/list/json',
					data: {
						'data_filter': data_filter,
						'pagination': {page: page},
						'get_categories': true,
						'get_attributes': true,
						'export': nhExport
					}
                }).done(function(response) {
                    KTApp.unblockPage();
                    var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                    var name = typeof(response.meta.name) != _UNDEFINED ? response.meta.name : '';

                    var $tmp = $("<a>");
                    $tmp.attr("href",response.data);
                    $("body").append($tmp);
                    $tmp.attr("download", name + '.xlsx');
                    $tmp[0].click();
                    $tmp.remove();

                    if (code == _SUCCESS) {
                        toastr.info(message);
                    } else {
                        toastr.error(message);
                    }
                });
        
                return false;
            });	
			
			$(document).on('click', '.kt-datatable .nh-kiotviet-change', function(e) {
        		var _this = $(this);
        		$('.kt-datatable .nh-kiotviet-change').not(this).popover('hide');
				_this.popover({
					title: nhMain.getLabel('thay_doi_thong_tin'),
	    			placement: 'bottom',
	    			html: true,
	    			sanitize: false,
	    			trigger: 'manual',
		            content: $('#popover-kiotviet-change').html(),
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

		        _this.on('shown.bs.popover', function (e) {		        	
		        	var idPopover = _this.attr('aria-describedby');
		        	var _popover = $('#' + idPopover);

		        	_popover.find('label').text(label);
		        	_popover.find('#value-change').val(changeValue);
				})
		        _this.popover('show');
        	}); 

	    	$(document).on('click', '#cancel-kiotviet-change', function(e) {
				var idPopover = $(this).closest('.popover.lg-popover').attr('id');
				var btnPopover = $('.nh-kiotviet-change[aria-describedby="'+ idPopover +'"]');
				btnPopover.popover('dispose');
			});

			$(document).on('click', '#confirm-kiotviet-change', function(e) {
				KTApp.blockPage(blockOptions);
				var _popover = $(this).closest('.popover.lg-popover');
				var idPopover = _popover.attr('id');
				var btnPopover = $('.nh-kiotviet-change[aria-describedby="'+ idPopover +'"]');

				var valueChange = _popover.find('#value-change').val();
				var nameChange = btnPopover.attr('data-change');
				var idChange = btnPopover.attr('data-id');

				nhMain.callAjax({
					url: adminPath + '/product/kiotviet-code',
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

		    $('.kt-selectpicker').selectpicker();
			$('body').tooltip({ selector: '[data-toggle=kt-tooltip]' });

		    quickUpload.init();
		    importExcel.init();
		    discountProduct.init();
		    
		}
	};
}();

jQuery(document).ready(function() {
	nhListProduct.listData();
});

