"use strict";

var nhListOrderReturn = function() {
	var optionsMain = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/order/list/json',
					params: {
						query: {
							type: _ORDER_RETURN
						},
					},
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
				field: 'code',
				title: nhMain.getLabel('ma_don_tra'),
				width: 220,
				template: function(row){
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';
					var source = typeof(row.source) != _UNDEFINED && row.source != null ? row.source : '';
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? row.created : '';
					var detailUrl = adminPath + '/order/return/detail/' + row.id;

					return '\
					<div class="code-time nh-weight">\
						<div> <i class="fas fa-qrcode"></i> <a href="' + detailUrl + '">' + code +'</a></div>\
						<div> <i class="far fa-clock"></i> '+ nhMain.utilities.parseIntToDateTimeString(row.created) +'</div>\
					</div>';
				}
			},
			{
				field: 'contact',
				title: nhMain.getLabel('khach_hang'),
				width: 250,
				sortable: false,
				template: function(row){
					var full_name = typeof(row.contact.full_name) != _UNDEFINED && row.contact.full_name != null ? row.contact.full_name : '';
					var phone = typeof(row.contact.phone) != _UNDEFINED && row.contact.phone != null ? row.contact.phone : '';
					var full_address = typeof(row.contact.full_address) != _UNDEFINED && row.contact.full_address != null ? row.contact.full_address : '';

					return '\
					<div class="contact-order nh-weight">\
						<div> <span>'+ nhMain.getLabel('ho_ten') + '</span>: ' + full_name +'</div>\
						<div> <span>'+ nhMain.getLabel('so_dien_thoai') + '</span>: ' + phone +'</div>\
						<div> <span>'+ nhMain.getLabel('dia_chi') + '</span>: ' + full_address +'</div>\
					</div>';
				}
			},
			{
				field: 'total',
				title: nhMain.getLabel('tong_tien'),
				width: 80,
				template: function (row) {
					var total = '';
					if(KTUtil.isset(row, 'total') && row.total != null){
						total = nhMain.utilities.parseNumberToTextMoney(row.total);
					}
					return total;
				}
			},
			{
				field: 'payment',
				title: nhMain.getLabel('thanh_toan'),
				width: 200,
				sortable: false,
				template: function(row){
					var paid = typeof(row.paid) != _UNDEFINED && row.paid != null ? row.paid : '';
					var debt = typeof(row.debt) != _UNDEFINED && row.debt != null ? row.debt : '';
					var return_paid = '';
					if ( paid > 0 ) {
						return_paid = '\
						<div>\
							<span class="text-primary">'+ nhMain.getLabel('da_thanh_toan') + '</span>: ' + nhMain.utilities.parseNumberToTextMoney(paid) +'\
						</div>';
					}

					if ( debt > 0  ) {
						return '\
						<div class="nh-weight">\
							'+ return_paid +'\
							<div class="text-danger"> <span>'+ nhMain.getLabel('con_no') + '</span>: ' + nhMain.utilities.parseNumberToTextMoney(debt) +'</div>\
						</div>';
					} else {
						return '\
						<div class="nh-weight">\
							<div class="text-success"> <span>'+ nhMain.getLabel('thanh_toan_hoan_tat') + '</span></div>\
						</div>';
					}
				}
			},
			{
				field: 'note',
				title: nhMain.getLabel('ghi_chu'),
				width: 300,
				sortable: false,
				template: function(row){
					var note = typeof(row.note) != _UNDEFINED && row.note != null ? row.note : '';
					var staff_note = typeof(row.staff_note) != _UNDEFINED && row.staff_note != null ? row.staff_note : '';

					return '\
					<div class="nh-note-customer">' + nhList.template.changeNote(row.id, 'note', note, nhMain.getLabel('ghi_chu_khach_hang')) + '</div>\
					<div class="nh-note-staff">' + nhList.template.changeNote(row.id, 'staff_note', staff_note, nhMain.getLabel('ghi_chu_nhan_vien')) + '</div>\
					';
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 120,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.statusOrders(row.status);
					}
					return status;
				},
			}
		]
	};

	var optionsModal = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/order/list/json',
					params: {
						query: {
							status: _DONE,
							type: _ORDER
						},
					},
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
			scroll: true,
            height: 400,
            minHeight: 400,
            footer: false,
		},

		sortable: true,

		pagination: true,
		extensions: {
			checkbox: true
		},
		search: {
			input: $('#nh-keyword-modal'),
		},

		columns: [
			{
				field: 'code',
				title: nhMain.getLabel('ma_don_hang'),
				width: 220,
				template: function(row){
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';
					var source = typeof(row.source) != _UNDEFINED && row.source != null ? row.source : '';
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? row.created : '';
					var detailUrl = adminPath + '/order/detail/' + row.id;

					return '\
					<div class="code-time nh-weight">\
						<div> <i class="fas fa-qrcode"></i> <a href="' + detailUrl + '">' + code +'</a></div>\
						<div> <i class="far fa-clock"></i> '+ nhMain.utilities.parseIntToDateString(row.created) +'</div>\
					</div>';
				}
			},
			{
				field: 'contact',
				title: nhMain.getLabel('khach_hang'),
				width: 250,
				sortable: false,
				template: function(row){
					var full_name = typeof(row.contact.full_name) != _UNDEFINED && row.contact.full_name != null ? row.contact.full_name : '';
					var phone = typeof(row.contact.phone) != _UNDEFINED && row.contact.phone != null ? row.contact.phone : '';
					var full_address = typeof(row.contact.full_address) != _UNDEFINED && row.contact.full_address != null ? row.contact.full_address : '';

					return '\
					<div class="contact-order nh-weight">\
						<div> <span>'+ nhMain.getLabel('ho_ten') + '</span>: ' + full_name +'</div>\
						<div> <span>'+ nhMain.getLabel('so_dien_thoai') + '</span>: ' + phone +'</div>\
						<div> <span>'+ nhMain.getLabel('dia_chi') + '</span>: ' + full_address +'</div>\
					</div>';
				}
			},
			{
				field: 'total',
				title: nhMain.getLabel('tong_tien'),
				width: 80,
				template: function (row) {
					var total = '';
					if(KTUtil.isset(row, 'total') && row.total != null){
						total = nhMain.utilities.parseNumberToTextMoney(row.total);
					}
					return total;
				}
			},
			{
				field: 'payment',
				title: nhMain.getLabel('thanh_toan'),
				width: 200,
				sortable: false,
				template: function(row){
					var paid = typeof(row.paid) != _UNDEFINED && row.paid != null ? row.paid : '';
					var debt = typeof(row.debt) != _UNDEFINED && row.debt != null ? row.debt : '';
					var return_paid = '';
					if ( paid > 0 ) {
						return_paid = '\
						<div>\
							<span class="text-primary">'+ nhMain.getLabel('da_thanh_toan') + '</span>: ' + nhMain.utilities.parseNumberToTextMoney(paid) +'\
						</div>';
					}

					if ( debt > 0  ) {
						return '\
						<div class="nh-weight">\
							'+ return_paid +'\
							<div class="text-danger"> <span>'+ nhMain.getLabel('con_no') + '</span>: ' + nhMain.utilities.parseNumberToTextMoney(debt) +'</div>\
						</div>';
					} else {
						return '\
						<div class="nh-weight">\
							<div class="text-success"> <span>'+ nhMain.getLabel('thanh_toan_hoan_tat') + '</span></div>\
						</div>';
					}
				}
			},
			{
                field: 'Actions',
                title: '',
                sortable: false,
                width: 140,
                overflow: 'visible',
                autoHide: false,
                template: function(row) {
                	var linkReturn = adminPath + '/order/return/create/' + row.id;
                    return '\
						<a href="' + linkReturn + '" class="btn-sm btn btn-label-brand btn-bold">\
                           	' + nhMain.getLabel('tao_don_tra_hang') + '\
                        </a>\
					';
                },
            }
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(optionsMain);

			// event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	note: adminPath + '/order/change-note'
			    }
		    });
		    
		    $('.kt-selectpicker').selectpicker();

		    var modal = $('#list-return-modal');
			var datatableModal = $('.kt-datatable-return').KTDatatable(optionsModal);	 

		    // fix datatable layout after modal shown
	        datatableModal.hide();
	        var alreadyReloaded = false;
	        modal.on('shown.bs.modal', function() {
	            if (!alreadyReloaded) {
	                var modalContent = $(this).find('.modal-content');
	                datatableModal.spinnerCallback(true, modalContent);

	                datatableModal.reload();

	                datatableModal.on('kt-datatable--on-layout-updated', function() {
	                    datatableModal.show();
	                    datatableModal.spinnerCallback(false, modalContent);
	                    datatableModal.redraw();
	                });

	                alreadyReloaded = true;
	            }
	        });
		}
	};
}();

jQuery(document).ready(function() {
	nhListOrderReturn.listData();
});