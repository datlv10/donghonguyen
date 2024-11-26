"use strict";

var nhListUser = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/language/list/json',
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
				field: 'id',
				title: 'ID',
				width: 30,
			}, 
			{
				field: 'name',
				title: nhMain.getLabel('ten_nuoc'),
				autoHide: false,
				template: function(row) {
					var imageAvatar = _FLAGS + row.code + '.svg';
					var isDefault = {
						0: {'check': ''},
						1: {'check': 'd-none'}						
					};
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<div class="kt-user-card-v2__name"><img class="kt-margin-r-5" src="'+ imageAvatar +'" style="width: 18px;">'+ row.name +'</div>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status '+ isDefault[row.is_default].check +'" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			}, 
			{
				field: 'code',
				title: 'Code',
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
				width: 150,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.status(row.status);
					}
					return status;	
				},
			}]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });		   		   
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	status: adminPath + '/language/change-status',
			    }
		    });


			$(document).on('click', '.nh-is-default', function() {
				var _id = $(this).data('id');
				if(_id.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi_da_chon'));
			    	return false;
			    }
				swal.fire({
			        title: nhMain.getLabel('chon_ngon_ngu_mac_dinh'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_chon_ngon_ngu_nay_lam_mac_dinh'),
			        type: 'warning',
			        
			        confirmButtonText: nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		nhMain.callAjax({
							url: adminPath + '/language/is-default',
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
	nhListUser.listData();
});

