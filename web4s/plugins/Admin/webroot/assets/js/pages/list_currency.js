"use strict";

var nhListCurrency = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/currency/list/json',
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
				title: nhMain.getLabel('ten_tien_te'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var urlEdit = adminPath + '/currency/update/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<span class="kt-user-card-v2__name">'+ row.name +'</span>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'code',
				title: nhMain.getLabel('ma'),
			},
			{
				field: 'is_default',
				title: nhMain.getLabel('mac_dinh'),
				textAlign: 'center',
				template: function(row) {
					var isStatus = {
						0: {'check': 'disabled="disabled"'},
						1: {'check': ''}
					};
					var isDefault = {
						0: {'check': ''},
						1: {'check': 'checked="checked"'}
					};
					return '\
						<div>\
							<label class="kt-radio kt-radio--bold" style="top:-4px;">\
								<input type="radio" class="nh-is-default" data-status="'+ row.status +'" data-id="'+ row.id +'" name="is_default" ' + isDefault[row.is_default].check + isStatus[row.status].check + ' >\
								<span></span>\
							</label>\
						</div>';
				},
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 110,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.status(row.status);
					}
					return status;					
				},
			}
		]		
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh-status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/currency/delete',
			    	status: adminPath + '/currency/change-status',
			    }
		    });

		    $(document).on('click', '.nh-is-default', function() {
				var _id = $(this).data('id');
				if(_id.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi_da_chon'));
			    	return false;
			    }
				swal.fire({
			        title: nhMain.getLabel('chon_tien_te_mac_dinh'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_chon_loai_tien_te_nay_lam_mac_dinh'),
			        type: 'warning',
			        
			        confirmButtonText: nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		nhMain.callAjax({
							url: adminPath + '/currency/is-default',
							data:{
								id: _id
							}
						}).done(function(response) {
							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
						    	toastr.info(message);
				            	$('.kt-datatable').KTDatatable('reload');
				            } else {
				            	toastr.error(message);
				            }            
						})
			    	}    	
			    });
				return false;
			});

		    $('.kt-selectpicker').selectpicker();    
		}
	};
}();

jQuery(document).ready(function() {
	nhListCurrency.listData();
});

