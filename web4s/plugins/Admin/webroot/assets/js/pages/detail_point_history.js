"use strict";

var customer_id = $('[name*=customer_id]').val();

var nhListDetailPointHistory = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/customer/point/detail/history-json/' + customer_id,
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

		sortable: true,

		pagination: true,
		extensions: {
			checkbox: true
		},
		search: {
			input: $('#nh-code-point'),
		},

		translate: {
            records: {
                processing: nhMain.getLabel('vui_long_cho') +  ' ...',
                noRecords: nhMain.getLabel('khong_co_ban_ghi_nao'),
            }
        },

		columns: [
			{
				field: 'code',
				title: nhMain.getLabel('ma_giao_dich'),
				sortable: false,
				width: 150,
				template: function(row){
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? row.created : '';

					return '\
					<div class="code-time nh-weight">\
						<div> <i class="fas fa-qrcode"></i> <span class="text-primary">' + code +'</span></div>\
						<div> <i class="far fa-clock"></i> '+ nhMain.utilities.parseIntToDateTimeString(row.created) +'</div>\
					</div>';
				}
			},
			{
				field: 'description_action',
				title: nhMain.getLabel('giao_dich'),
				autoHide: false,
				width: 200,
				template: function(row) {
					var description_name = '';
					var customer_related_info = '';
					var action = KTUtil.isset(row, 'action') && row.action != null ? parseInt(row.action) : 1;
					var customer_related_name = typeof(row.customer_related_name) != _UNDEFINED && row.customer_related_name != null ? row.customer_related_name : '';
					var customer_related_code = typeof(row.customer_related_code) != _UNDEFINED && row.customer_related_code != null ? row.customer_related_code : '';

					if (typeof(row.description) != _UNDEFINED && row.description != null) {
						description_name = nhMain.getLabel(row.description);
					}

					if (typeof(row.action_type) != _UNDEFINED && row.action_type != null && row.action_type == 'give_point') {
						if (action == 0) {
							customer_related_info = '<span data-toggle="popover" data-id="'+ row.id +'" data-name="'+ customer_related_name +'" data-code="'+ customer_related_code +'" data-label="' + nhMain.getLabel('thong_tin_nguoi_nhan') + '" class="cursor-p label-value text-primary nh-view-give-point">\
														<i class="fas fa-gift"></i>\
														' + nhMain.getLabel('thong_tin_nguoi_nhan') + '\
													</span>';
						} else {
							customer_related_info = '<span data-toggle="popover" data-id="'+ row.id +'" data-name="'+ customer_related_name +'" data-code="'+ customer_related_code +'" data-label="' + nhMain.getLabel('thong_tin_nguoi_tang') + '" class="cursor-p label-value text-primary nh-view-give-point">\
														<i class="fas fa-gift"></i>\
														' + nhMain.getLabel('thong_tin_nguoi_tang') + '\
													</span>';
						}
					}

					return '<p class="kt-font-bolder mb-0">\
								' + description_name + '\
							</p>' + customer_related_info;
				}
			},
			{
				field: 'point',
				title: nhMain.getLabel('diem_thay_doi'),
				sortable: false,
				width: 120,
				class: 'text-center',
				template: function(row) {
					var action = KTUtil.isset(row, 'action') && row.action != null ? parseInt(row.action) : 1;
					var description_action = '+';

					if (action == 0) {
						var description_action = '-';						
					}

					var point = nhMain.utilities.notEmpty(row.point) ? nhMain.utilities.parseNumberToTextMoney(row.point) : 0;

					return '<span class="text-primary kt-font-bolder">' + description_action + point + '</span>';
				}
			},
			{
				field: 'point_type',
				title: nhMain.getLabel('loai_diem'),
				autoHide: false,
				width: 120,
				template: function(row) {
					var point_type = KTUtil.isset(row, 'point_type') && row.point_type != null ? parseInt(row.point_type) : 1;
					var description_point_type = nhMain.getLabel('diem_vi');

					if (point_type == 0) {
						var description_point_type = nhMain.getLabel('diem_khuyen_mai');						
					}

					return description_point_type;
				}
			},
			{
				field: 'note',
				title: nhMain.getLabel('ghi_chu'),
				autoHide: false,
				template: function(row) {
					var note = typeof(row.note) != _UNDEFINED && row.note != null ? row.note : '.....';
					return note;
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 110,
				autoHide: false,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null && row.draft != 1){
						status = nhList.template.statusCustomerPointHistory(row.status);
					}
					
					return status;

				},
			}
		]
	}

	return {
		listData: function() {
			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
	            endDate: '0d'
  			});

		    $('.kt-selectpicker').selectpicker();

		    var datatable = $('.kt-datatable').KTDatatable(options);

		    $('#action_type').on('change', function() {
		      	datatable.search($(this).val(), 'action_type');
		    });

		    $('#point_create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#point_create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	

		    $(document).on('click', '.nh-view-give-point', function(e) {
        		var _this = $(this);
        		$('.kt-datatable .nh-view-give-point').not(this).popover('hide');

        		var name = $(this).attr('data-name');
				var code = $(this).attr('data-code');
				var label = $(this).attr('data-label');

				_this.popover({
					title: label,
	    			placement: 'bottom',
	    			html: true,
	    			sanitize: false,
	    			trigger: 'manual',
		            content: $('#popover-view-give-point').html(),
		           	template: '\
			            <div class="popover lg-popover" role="tooltip">\
			                <div class="arrow"></div>\
			                <h3 class="popover-header"></h3>\
			                <div class="popover-body"></div>\
			            </div>'
		        });

		        _this.on('shown.bs.popover', function (e) {		        	
		        	var idPopover = _this.attr('aria-describedby');
		        	var _popover = $('#' + idPopover);

		        	_popover.find('.value_name').text(name);
		        	_popover.find('.value_code').text(code);
				})
		        _this.popover('show');
        	}); 
		}
	};
}();

jQuery(document).ready(function() {
	nhListDetailPointHistory.listData();
});

