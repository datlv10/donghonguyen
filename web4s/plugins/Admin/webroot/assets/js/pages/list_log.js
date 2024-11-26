"use strict";

var nhListLog = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/log/list/json',
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
				field: 'action',
				title: nhMain.getLabel('hanh_dong'),
				autoHide: false,
				sortable: false,
				width: 250,
				template: function(row) {
					var action = row.action || '';
					var type = row.type || '';
					var sub_type = row.sub_type || '';

					var htmlAction = ``;
					var htmlType = ``;

					var badgeUpdateClass = 'kt-badge--primary';
					var labelSubType = nhMain.getLabel('ban_ghi').toLowerCase();
					switch(sub_type){
						case 'article':
							labelSubType = nhMain.getLabel('bai_viet').toLowerCase();
						break;

						case 'product':
							labelSubType = nhMain.getLabel('san_pham').toLowerCase();
						break;

						case 'brand':
							labelSubType = nhMain.getLabel('thuong_hieu').toLowerCase();
						break;

						case 'block':
							labelSubType = 'BLOCK';
						break;

						case 'template':
							labelSubType = nhMain.getLabel('giao_dien').toLowerCase();
						break;
					}

					if(type == 'template'){
						labelSubType = nhMain.getLabel('tep_giao_dien').toLowerCase();
						badgeUpdateClass = 'kt-badge--warning';
					}

					if(action == 'add'){
						htmlAction = `
							<span class="kt-badge kt-badge--success kt-badge--inline">
								<i class="fa fa-plus mr-5"></i>
		                        ${nhMain.getLabel('them_moi')}
		                        ${labelSubType}
		                    </span>`;
					}

					if(action == 'update'){
						htmlAction = `
							<span class="kt-badge ${badgeUpdateClass} kt-badge--inline">
								<i class="fa fa-edit mr-5"></i>
		                        ${nhMain.getLabel('cap_nhat')}
		                        ${labelSubType}
		                    </span>`;
					}

					if(action == 'update_status'){
						htmlAction = `
							<span class="kt-badge ${badgeUpdateClass} kt-badge--inline">
								<i class="fa fa-edit mr-5"></i>
		                        ${nhMain.getLabel('doi_trang_thai')}
		                        ${labelSubType}
		                    </span>`;
					}

					if(action == 'delete'){
						htmlAction = `
							<span class="kt-badge kt-badge--danger kt-badge--inline">
								<i class="fa fa-trash-alt  mr-5"></i>
		                        ${nhMain.getLabel('xoa')}
		                        ${labelSubType}
		                    </span>`;
					}
									
					return `${htmlAction}`;
				}
			},			
			{
				field: 'description',
				title: nhMain.getLabel('mo_ta'),
				width: 400,
				sortable: false,
				autoHide: false,
				template: function(row) {
					var description = row.description || '';
					return description;
				}
			},
			{
				field: 'full_name',
				title: nhMain.getLabel('nguoi_thuc_hien'),
				width: 150,
				sortable: false,
				template: function(row) {
					var user_id = row.user_id || '';
					var fullName = row.full_name || '';

					if(user_id == 10000 && fullName.length == 0) fullName = 'Super Admin';
					return fullName;
				}
			},
			{
				field: 'created',
				title: nhMain.getLabel('thoi_gian'),
				sortable: false,
				textAlign: 'right',
				width: 110,
				template: function(row) {
					var createdLabel = row.created_label || '';

					return `<i class="fs-11">${createdLabel}</i>`;
				}
			}
		]
	};

	return {
		listData: function() {			     
			var datatable = $('.kt-datatable').KTDatatable(options);

		    $('#action').on('change', function() {
		      	datatable.search($(this).val(), 'action');
		    });

		    $('#type').on('change', function() {
		      	datatable.search($(this).val(), 'type');
		    });

		    $('#user_id').on('change', function() {
		      	datatable.search($(this).val(), 'user_id');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });			   

		    $('.kt-selectpicker').selectpicker();

            $('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
  			});

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
				var wrapElement = $('.collapse-search-advanced-content');
				if(!nhMain.utilities.notEmpty(wrapElement)) return false;

				var arrowIcon = $(this).find('i');

				wrapElement.on('shown.bs.collapse', function () {
				   	arrowIcon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
				});

				wrapElement.on('hidden.bs.collapse', function () {
					arrowIcon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
				   
				});
			});
		}
	};
}();

jQuery(document).ready(function() {
	nhListLog.listData();
});

