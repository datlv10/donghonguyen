"use strict";

var typeCategory = $('#nh-type-category').val();
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

var nhListCategoryProduct = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/category/'+ typeCategory +'/list/json',
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
		
		layout: {
			scroll: false,
			footer: false,
			class: 'table-hover',
		},

		sortable: false,
		pagination: false,
		extensions: {
			checkbox: true
		},
		search: {
			input: $('#nh-keyword')
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
				class: '',
				width: 18,
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
				autoHide: false,
				sortable: false,
			},			
			{
				field: 'name',
				title: nhMain.getLabel('ten_danh_muc'),
				autoHide: false,
				sortable: false,
				width: 400,
				template: function(row) {
					var content = KTUtil.isset(row, 'CategoriesContent') && row.CategoriesContent != null ? row.CategoriesContent : {};
					var link = KTUtil.isset(row, 'Links') && row.Links != null ? row.Links : {};
					var url = KTUtil.isset(link, 'url') && link.url != null ? link.url : '';
					var name = KTUtil.isset(content, 'name') && content.name != null ? content.name : '';
					var urlEdit = adminPath + '/category/' + row.type + '/update/' + row.id;
					var urlDetail = adminPath + '/category/' + row.type + '/detail/' + row.id;

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
					var urlTranslate = adminPath + '/category/' + row.type + '/update/' + row.id;
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
				field: 'position',
				title: nhMain.getLabel('vi_tri'),
				width: 60,
				sortable: false,
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
				sortable: false,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.status(row.status);
					}
					return status;					
				},
			},  
			{
				field: 'created_by',
				title: nhMain.getLabel('nguoi_tao'),
				width: 120,
				sortable: false,
				template: function(row) {
					return nhList.template.createdBy(row);
				}
			}
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh-status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });		 

		    $('#created_by').on('change', function() {
		      	datatable.search($(this).val(), 'created_by');
		    });	    		   
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/category/'+ typeCategory +'/delete',
			    	status: adminPath + '/category/change-status',
			    	duplicate: adminPath + '/category/'+ typeCategory +'/duplicate',
			    	quickChange: adminPath + '/category/change-position'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListCategoryProduct.listData();
});

