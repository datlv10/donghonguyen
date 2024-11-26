"use strict";

var nhListTag = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/tag/list/json',
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
				title: nhMain.getLabel('ten_the'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var urlEdit = adminPath + '/tag/update/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlEdit +'" class="kt-user-card-v2__name">'+ row.name +'</a>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'url',
				title: nhMain.getLabel('duong_dan'),
				sortable: false,
				template: function(row) {
					var url = '';
					if(nhMain.utilities.notEmpty(row.url)){
						url = '<i>{the-bai-viet}/</i><b>' + row.url + '</b>';
					}
					return url;
				},
			},
			{
				field: 'lang',
				title: nhMain.getLabel('ngon_ngu'),
				autoHide: false,
				sortable: true,
				template: function(row) {
					var language = '';
					var imgLanguage = '';

					if(!$.isEmptyObject(listLanguage) && listLanguage[row.lang]){
						language = listLanguage[row.lang];
						imgLanguage = '<div class="list-flags d-inline mr-5"><img src="'+ _FLAGS + row.lang + '.svg" alt="'+ language +'" class="flag"></div>';
					}
					return imgLanguage + language ;
				},
			}
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);

		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#nh-language').on('change', function() {
		      	datatable.search($(this).val(), 'lang');
		    });
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/tag/delete'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListTag.listData();
});

