"use strict";

var htmlListLang = '';

if(!$.isEmptyObject(listLanguage)){
	htmlListLang += '<div class="list-flags text-center">';
	$.each(listLanguage, function(code, name) {
	  	htmlListLang += '<a href="?lang='+ code +'"><img src="'+ _FLAGS + code + '.svg" alt="'+ name +'" class="flag"></a>'
	});
	htmlListLang += '</div>';
}

var nhListAttributeOption = function() {

	var attributeId = $('.kt-datatable').data('attribute-id');
	if(!attributeId > 0){		
		return false;
	}

	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/setting/attribute/option/list/json/' + attributeId,
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
			keyword: $('#nh-keyword').val()
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
				title: nhMain.getLabel('ten_tuy_chon'),
				autoHide: false,
				width: 350,
				template: function(row) {
					var content = KTUtil.isset(row, 'AttributesOptionsContent') && row.AttributesOptionsContent != null ? row.AttributesOptionsContent : {};
					var name = KTUtil.isset(content, 'name') && content.name != null ? content.name : '';
					var urlEdit = adminPath + '/setting/attribute/option/update/' + attributeId + '/' + row.id;
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
			// {
			// 	field: 'code',
			// 	title: nhMain.getLabel('ma'),
			// 	sortable: true,
			// 	template: function(row) {
			// 		var code = KTUtil.isset(row, 'code') && row.code != null ? row.code : '';
			// 		return '<span class="kt-font-bold">' + code + '</span>';
			// 	}
			// },			
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
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/setting/attribute/option/delete',
			    	quickChange: adminPath + '/setting/attribute/option/change-position'
			    }
		    });


		    $(document).on('change', 'select#attribute_id', function(e) {
		    	var url = adminPath + '/setting/attribute/option/' + $(this).val();
		    	window.location.href = url;
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListAttributeOption.listData();
});

