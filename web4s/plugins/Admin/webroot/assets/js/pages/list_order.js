"use strict";

var nhListOrder = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/order/list/json',
					params: {
						query: {
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
				title: nhMain.getLabel('ma_don_hang'),
				sortable: false,
				width: 170,
				autoHide: false,
				template: function(row){
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';
					var source = typeof(row.source) != _UNDEFINED && row.source != null ? row.source : '';
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? row.created : '';
					var detailUrl = adminPath + '/order/detail/' + row.id;
					var print = adminPath + '/print?code=ORDER&id_record=' + row.id;

					return `
					<div class="code-time nh-weight">\
						<div> <i class="fas fa-qrcode"></i> <a href="${detailUrl}">${code}</a></div>\
						<div> <i class="far fa-clock"></i> ${nhMain.utilities.parseIntToDateTimeString(row.created)}</div>\
						<div> <i class="fa fa-print"></i> <a target="_blank" href="${print}">${nhMain.getLabel('in_don_hang')}</a></div>\
					</div>`;
				}
			},
			{
				field: 'contact',
				title: nhMain.getLabel('khach_hang'),
				sortable: false,
				width: 250,
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
				field: 'count_items',
				title: nhMain.getLabel('SL'),
				width: 50,
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
			},
			{
				field: 'total',
				title: nhMain.getLabel('tong_tien'),
				width: 80,
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
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
				sortable: false,
				width: 200,
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
				template: function(row){
					var paid = typeof(row.paid) != _UNDEFINED && row.paid != null ? row.paid : '';
					var debt = typeof(row.debt) != _UNDEFINED && row.debt != null ? row.debt : '';

					var cod_paid = typeof(row.cod_paid) != _UNDEFINED && row.cod_paid != null ? row.cod_paid : '';
					var cash_paid = typeof(row.cash_paid) != _UNDEFINED && row.cash_paid != null ? row.cash_paid : '';
					var bank_paid = typeof(row.bank_paid) != _UNDEFINED && row.bank_paid != null ? row.bank_paid : '';
					var credit_paid = typeof(row.credit_paid) != _UNDEFINED && row.credit_paid != null ? row.credit_paid : '';
					var gateway_paid = typeof(row.gateway_paid) != _UNDEFINED && row.gateway_paid != null ? row.gateway_paid : '';
					var voucher_paid = typeof(row.voucher_paid) != _UNDEFINED && row.voucher_paid != null ? row.voucher_paid : '';

					var html_payment_method ='';
					if(cod_paid > 0) {
						html_payment_method = '<p>' + nhMain.getLabel('thu_ho') + '(COD): ' + nhMain.utilities.parseNumberToTextMoney(cod_paid) +'</p>';
					}

					if(cash_paid > 0) {
						html_payment_method += '<p>' + nhMain.getLabel('tien_mat') + ': ' + nhMain.utilities.parseNumberToTextMoney(cash_paid) +'</p>';
					}

					if(bank_paid > 0) {
						html_payment_method += '<p>' + nhMain.getLabel('chuyen_khoan') + ': ' + nhMain.utilities.parseNumberToTextMoney(bank_paid) +'</p>';
					}

					if(credit_paid > 0) {
						html_payment_method += '<p>' + nhMain.getLabel('quet_the') + ': ' + nhMain.utilities.parseNumberToTextMoney(credit_paid) +'</p>';
					}

					if(gateway_paid > 0) {
						html_payment_method += '<p>' + nhMain.getLabel('cong_thanh_toan') + ': ' + nhMain.utilities.parseNumberToTextMoney(gateway_paid) +'</p>';
					}

					if(voucher_paid > 0) {
						html_payment_method += '<p>' + nhMain.getLabel('thanh_toan_bang_voucher') + ': ' + nhMain.utilities.parseNumberToTextMoney(voucher_paid) +'</p>';
					}

					var return_paid = '';
					if ( paid > 0 ) {
						return_paid = '\
						<div style="margin-bottom: 5px">\
							<span class="text-primary" >\
							 ' + nhMain.getLabel('da_thanh_toan') + '</span>: \
							 <span class=" kt-link popover-payment-method"\
							 data-toggle="kt-popover" data-html="true" \
							 data-original-title="' + nhMain.getLabel('thanh_toan') + '" \
							 data-content="' + html_payment_method +'">\
							 ' + nhMain.utilities.parseNumberToTextMoney(paid) +'</span>\
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
							<div class="text-success kt-link popover-payment-method"\
								 data-toggle="kt-popover" data-html="true" \
								 data-original-title="' + nhMain.getLabel('thanh_toan') + '" \
								 data-content="' + html_payment_method +'">\
								 <span>' + nhMain.getLabel('thanh_toan_hoan_tat') + '</span>\
							</div>\
						</div>';
					}
				}
			},
			{
				field: 'note',
				title: nhMain.getLabel('ghi_chu'),
				sortable: false,
				width: 200,
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
				template: function(row){
					var staff_note = typeof(row.staff_note) != _UNDEFINED && row.staff_note != null ? row.staff_note : '';

					var _htmlNoteCustomer = '';
					var _htmlNoteStaff = '<div class="nh-note-staff nh-weight">\
												' + nhList.template.changeNote(row.id, 'staff_note', staff_note, nhMain.getLabel('ghi_chu_nhan_vien')) +'\
											</div>';

					if (typeof(row.note) != _UNDEFINED && row.note != null) {
						var note = typeof(row.note) != _UNDEFINED && row.note != null ? row.note : '';

						_htmlNoteCustomer = '<div class="nh-note-customer nh-weight">\
												<span>'+ nhMain.getLabel('khach_hang') + ':</span> ' + note +'\
											</div>';
					}

					if (typeof(row.staff_note) != _UNDEFINED && row.staff_note != null) {
						_htmlNoteStaff = '<div class="nh-note-staff nh-weight">\
												<span>'+ nhMain.getLabel('nhan_vien') + ':</span> ' + nhList.template.changeNote(row.id, 'staff_note', staff_note, nhMain.getLabel('ghi_chu_nhan_vien')) +'\
											</div>';
					}

					return _htmlNoteCustomer + _htmlNoteStaff;
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				sortable: false,
				width: 120,
				autoHide: false,
				responsive: {
				  	visible: 'md',
				  	hidden: 'xs'
				},
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.statusOrders(row.status);
					}
					if(KTUtil.isset(row, 'customer_cancel') && row.customer_cancel == 1){
						status = nhList.template.statusOrders(_CUSTOMER_CANCEL);
					}
					return status;
				},
			}
			// {
   //              field: 'action',
   //              title: '',
   //              sortable: false,
   //              width: 50,
   //              autoHide: false,
   //              template: function(row) {
   //              	var detailUrl = adminPath + '/order/detail/' + row.id;
   //              	var printUrl = adminPath + '/print/ORDER/' + row.id;

   //                  return '\
			// 			<div class="dropdown">\
			// 				<a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
   //                              <i class="la la-cog"></i>\
   //                          </a>\
			// 			  	<div class="dropdown-menu dropdown-menu-right">\
			// 			    	<a class="dropdown-item" href="'+ detailUrl +'" target="_blank"><i class="la la-edit"></i> ' + nhMain.getLabel('chi_tiet_don_hang') + '</a>\
			// 			    	<a class="dropdown-item" href="'+ printUrl +'" target="_blank"><i class="la la-print"></i> ' + nhMain.getLabel('in_don_hang') + '</a>\
			// 			  	</div>\
			// 			</div>\
			// 		';
   //              },
			// }
		]
	};

	return {
		listData: function() {
			nhMain.location.init({
				idWrap: ['.nh-search-advanced']
			});

  			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
	            endDate: '0d'
  			});

			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });		

		    $('#source').on('change', function() {
		      	datatable.search($(this).val(), 'source');
		    });

		    $('#branch_id').on('change', function() {
		      	datatable.search($(this).val(), 'branch_id');
		    });  

		    $('#city_id').on('change', function() {
		      	datatable.search($(this).val(), 'city_id');
		    }); 

		    $('#district_id').on('change', function() {
		      	datatable.search($(this).val(), 'district_id');
		    });  	

		    $('#ward_id').on('change', function() {
		      	datatable.search($(this).val(), 'ward_id');
		    }); 

		    $('#price_from').on('change', function() {
		      	datatable.search($(this).val(), 'price_from');
		    });

		    $('#price_to').on('change', function() {
		      	datatable.search($(this).val(), 'price_to');
		    });

		    $('#staff_id').on('change', function() {
		      	datatable.search($(this).val(), 'staff_id');
		    });

		    $('#created_by').on('change', function() {
		      	datatable.search($(this).val(), 'created_by');
		    }); 

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	

		    $('#note').on('change', function() {
		      	datatable.search($(this).val(), 'note');
		    });  

		    $('#pay_status').on('change', function() {
		      	datatable.search($(this).val(), 'pay_status');
		    }); 
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	note: adminPath + '/order/change-note'
			    }
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
					pay_status: $('[name=pay_status]').val(),
					source: $('[name=source]').val(),
					city_id: $('[name=city_id]').val(),
					district_id: $('[name=district_id]').val(),
					ward_id: $('[name=ward_id]').val(),
					price_from: $('[name=price_from]').val(),
					price_to: $('[name=price_to]').val(),
					staff_id: $('[name=staff_id]').val(),
					create_from: $('[name=create_from]').val(),
					create_to: $('[name=create_to]').val(),
				}

                nhMain.callAjax({
                    url: adminPath + '/order/list/json',
					data: {
						'data_filter': data_filter,
						'pagination': {page: page},
						'get_staff': true,
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

		    $('.kt-selectpicker').selectpicker();
		    
		    $(document).on('mouseenter', '.kt-datatable .popover-payment-method', function() {
				$(this).popover({
	    			html: true,
	    			trigger: 'hover',
	    			placement: 'top'
		        });
		        $(this).popover('show');
		    });
		}
	};
}();

jQuery(document).ready(function() {
	nhListOrder.listData();
});

