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

var nhListAttribute = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/setting/attribute/list/json',
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
				title: nhMain.getLabel('ten_thuoc_tinh'),
				autoHide: false,
				width: 250,
				template: function(row) {
					var content = KTUtil.isset(row, 'AttributesContent') && row.AttributesContent != null ? row.AttributesContent : {};
					var name = KTUtil.isset(content, 'name') && content.name != null ? content.name : '';
					var urlEdit = adminPath + '/setting/attribute/update/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<span class="kt-user-card-v2__name">'+ name +'</span>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'lang',
				width: 120,
				title: htmlListLang,
				class: useMultipleLanguage ? '' : 'd-none',
				sortable: false,
				textAlign: 'center',
				template: function(row) {
					var mutiple_language = nhMain.utilities.notEmpty(row.mutiple_language) ? row.mutiple_language : [];
					var templateLanguage = '';
					var urlTranslate = adminPath + '/setting/attribute/update/' + row.id;
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
				},
			},
			{
				field: 'number_options',
				title: nhMain.getLabel('tuy_chon'),
				sortable: false,
				template: function(row) {
					var has_option = KTUtil.isset(row, 'has_option') && row.has_option != null ? row.has_option : 0;
					var urlManagerOption = adminPath + '/setting/attribute/option/' + row.id;
					var result = '';
					if (has_option > 0){
						var number_options = KTUtil.isset(row, 'number_options') && row.number_options != null ? row.number_options : 0;
						var label = number_options + ' ' + nhMain.getLabel('tuy_chon');
						if(number_options == 0){
							label = nhMain.getLabel('chua_co_tuy_chon');
						}
						result = '<a href="' + urlManagerOption + '" target="_blank" class="kt-font-bold">' + label + '</a>';						
					}else{
						result = '<i class="fs-12">N/A</i>';
					}
					return result;
				}
			},
			{
				field: 'attribute_type',
				title: nhMain.getLabel('loai_thuoc_tinh'),
				sortable: true,
				template: function(row) {
					var attribute_type_name = KTUtil.isset(row, 'attribute_type_name') && row.attribute_type_name != null ? row.attribute_type_name : '';
					return '<span class="kt-font-bold">' + attribute_type_name + '</span>';
				}
			},
			{
				field: 'input_type',
				title: nhMain.getLabel('loai_input'),
				sortable: true,
				template: function(row) {
					var input_type_name = KTUtil.isset(row, 'input_type_name') && row.input_type_name != null ? row.input_type_name : '';
					return '<span class="kt-font-bold">' + input_type_name + '</span>';
				}
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
			}]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#attribute_type').on('change', function() {
		      	datatable.search($(this).val(), 'attribute_type');
		    });

		    $('#input_type').on('change', function() {
		      	datatable.search($(this).val(), 'input_type');
		    });

		    $('#has_image').on('change', function() {
		      	datatable.search($(this).val(), 'has_image');
		    });

		    $('#required').on('change', function() {
		      	datatable.search($(this).val(), 'required');
		    });
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/setting/attribute/delete',
			    	quickChange: adminPath + '/setting/attribute/change-position'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListAttribute.listData();
});

