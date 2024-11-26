"use strict";

var htmlListLang = '';
if(!$.isEmptyObject(listLanguage)){
	htmlListLang += '<div class="list-flags head-flags text-center">';
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
		nhMain.selectMedia.single.init();

		$(document).on('click', '[upload-id]', function() {
			$(self.idModal).modal('show');

			var id = $(this).attr('upload-id');

			if(!nhMain.utilities.notEmpty(id)) return false;

			self.loadContentModalUpload(id);

		});

		$(document).on('click', '.btn-quick-upload', function() {
			var id = $(self.idModal).find('input[name="album"]').attr('upload-id');
			var listImages = $(self.idModal).find('input[name="album"]').val();
			var avatarImage = $(self.idModal).find('input[name="image_avatar"]').val();
			
			KTApp.blockPage(blockOptions);
			if(id != _UNDEFINED && id.length > 0){
				nhMain.callAjax({
					url: adminPath + '/article/quick-upload',
					data: {
						id: id,
						images: listImages,
						image_avatar: avatarImage
					}
				}).done(function(response) {
					KTApp.unblockPage();

					var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	if (code == _SUCCESS) {
		        		var imageAvatar = typeof(data.image_avatar) != _UNDEFINED ? data.image_avatar : '';
						var listImage = typeof(data.images) != _UNDEFINED ? data.images : {};

						$(self.idModal).modal('hide');
						self.loadTable(id, listImage, imageAvatar);
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


	loadTable: function(id = null, images = null, image_avatar = null){
		var self = this;

		var templateImage =  nhList.template.changeImage(id, images, image_avatar);
		$('.kt-datatable').find('[upload-id="' + id + '"]').closest('.symbol-group.symbol-hover').html(templateImage);
	},

	loadContentModalUpload: function(id = null) {
 		var self = this;

 		var modalBody = '.modal-body';
 		var listAlbum = '.list-image-album';
 		var wrapAlbum = '.wrap-album';

		if(id != _UNDEFINED && id.length > 0){
			nhMain.callAjax({
				url: adminPath + '/article/upload-modal/' + id,
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

var nhListArticle = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/article/list/json',
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
				field: 'name',
				title: nhMain.getLabel('tieu_de'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var url = typeof(row.url) != _UNDEFINED && row.url != null ? row.url : '';
					var urlEdit = adminPath + '/article/update/' + row.id;
					var urlDetail = adminPath + '/article/detail/' + row.id;

					var viewTemplate = ''
					if(url.length > 0){
						viewTemplate = '<span class="view-template kt-margin-l-5"><a target="_blank" href="/'+ url +'"><i class="fa fa-eye"></i></a></span>';
					}
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="d-inline kt-user-card-v2__name">'+ name +'</a>' + viewTemplate + '\
								<span class="d-block kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="text-info action-item nh-duplicate" data-id="'+ row.id +'">'+ nhMain.getLabel('nhan_ban') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'lang',
				title: htmlListLang,
				class: useMultipleLanguage ? '' : 'd-none',
				sortable: false,
				textAlign: 'center',
				template: function(row) {
					var mutiple_language = nhMain.utilities.notEmpty(row.mutiple_language) ? row.mutiple_language : [];
					var templateLanguage = '';
					var urlTranslate = adminPath + '/article/update/' + row.id;
					var templateLanguage = '<div class="list-flags">';
					$.each(listLanguage, function(code, name) {
						var flag_class = '';
						if(typeof(mutiple_language[code]) != _UNDEFINED && mutiple_language[code]){
							flag_class = 'text-primary';
						}
					  	templateLanguage += '<a href="'+ urlTranslate + '?lang=' + code +'" class="fa fa-pencil-alt flag ' + flag_class + '" title="'+ nhMain.getLabel('dich_sang') + ' ' + name +'">'
					});
					templateLanguage += '</div>';
					return templateLanguage;
				}
			},
			{
				field: 'image_avatar',
				title: '<div><i class="fa fa-image fa-lg"></i></div>',
				sortable: false,
				width: 155,
				template: function(row) {
					var imageAvatar = nhMain.utilities.notEmpty(row.image_avatar) ? row.image_avatar : '';
					var images = [];

					if(KTUtil.isset(row, 'has_album') && row.has_album != null){
						images = nhMain.utilities.notEmpty(row.images) ? JSON.stringify(row.images) : [];
					}

					return nhList.template.changeImage(row.id, images, imageAvatar);
				}
			},
			{
				field: 'seo/keyword_seo',
				title: nhMain.getLabel('seo') +'/'+ nhMain.getLabel('tu_khoa'),				
				width: 110,
				sortable: false,
				template: function(row) {
					var seo_score = nhMain.utilities.notEmpty(row.seo_score) ? row.seo_score : 'default';
					var keyword_score = nhMain.utilities.notEmpty(row.keyword_score) ? row.keyword_score : 'default';
					var seo = {
						default: {'title': nhMain.getLabel('chua_co'), 'class': 'kt-badge kt-badge--dark'},
						success: {'title': nhMain.getLabel('tot'), 'class': 'kt-badge kt-badge--success'},						
						warning: {'title': nhMain.getLabel('binh_thuong'), 'class': 'kt-badge kt-badge--warning'},						
						danger: {'title': nhMain.getLabel('chua_dat'), 'class': 'kt-badge kt-badge--danger'},						
					};
					return '\
						<span class="kt-section__content kt-section__content--solid mr-10">\
							<span title="'+ seo[seo_score].title +'" class="'+ seo[seo_score].class +'"></span>\
						</span>\
						<span class="kt-section__content kt-section__content--solid">\
							<span title="'+ seo[keyword_score].title +'" class="'+ seo[keyword_score].class +'"></span>\
						</span>';
				}
			},
			{
				field: 'view',
				title: nhMain.getLabel('luot_xem'),
				sortable: true,
				textAlign: 'center',
				width: 90,
			},
			{
				field: 'position',
				title: nhMain.getLabel('vi_tri'),
				width: 60,
				sortable: true,
				textAlign: 'center',
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
				title: nhMain.getLabel('trang_thai'),
				width: 110,
				autoHide: false,
				template: function(row) {
					var status = '';
					var draftArticle = '';
					if(KTUtil.isset(row, 'status') && row.status != null && row.draft != 1){
						status = nhList.template.statusProduct(row.status);
					}
					
					if((KTUtil.isset(row, 'draft') && row.draft == 1)){
						draftArticle = nhList.template.draftProduct(row.draft);
					}
					return status + draftArticle;

				},
			}]
	};

	return {
		listData: function() {			     
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#id_categories').on('change', function() {
		      	datatable.search($(this).val().length > 0 ? [$(this).val()] : [], 'id_categories');
		    });

		    $('#featured').on('change', function() {
		      	datatable.search($(this).val(), 'featured');
		    });	

		    $('#has_album').on('change', function() {
		      	datatable.search($(this).val(), 'has_album');
		    });

		    $('#has_video').on('change', function() {
		      	datatable.search($(this).val(), 'has_video');
		    });

		    $('#has_file').on('change', function() {
		      	datatable.search($(this).val(), 'has_file');
		    });

		    $('#seo_score').on('change', function() {
		      	datatable.search($(this).val(), 'seo_score');
		    });

		    $('#keyword_score').on('change', function() {
		      	datatable.search($(this).val(), 'keyword_score');
		    });

		    $('#catalogue').on('change', function() {
		      	datatable.search($(this).val(), 'catalogue');
		    });

		    $('#seo_score').on('change', function() {
		      	datatable.search($(this).val(), 'seo_score');
		    });

		    $('#keyword_score').on('change', function() {
		      	datatable.search($(this).val(), 'keyword_score');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/article/delete',
			    	status: adminPath + '/article/change-status',
			    	duplicate: adminPath + '/article/duplicate',
			    	quickChange: adminPath + '/article/change-position'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();

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
					featured: $('#featured').val(),
					has_album: $('#has_album').val(),
					has_video: $('#has_video').val(),
					has_file: $('#has_file').val(),
					catalogue: $('#catalogue').val(),
					seo_score: $('#seo_score').val(),
					keyword_score: $('#keyword_score').val(),
					create_from: $('#create_from').val(),
					create_to: $('#create_to').val(),
				}

                nhMain.callAjax({
                    url: adminPath + '/article/list/json',
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

            quickUpload.init(); 
		}
	};
}();

jQuery(document).ready(function() {
	nhListArticle.listData();
});

