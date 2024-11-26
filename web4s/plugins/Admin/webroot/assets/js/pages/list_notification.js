"use strict";

var nhListNotification = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/notification/list/json',
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
				field: 'title',
				title: nhMain.getLabel('tieu_de'),
				autoHide: false,
				width: 300,
				template: function(row) {
					var title = KTUtil.isset(row, 'title') && row.title != null ? row.title : '';
					return title;
				}
			},
			{
				field: 'number_sent',
				title: nhMain.getLabel('gui_thong_bao'),
				width: 120,
				sortable: false,
				template: function(row) {
					var sent = '<span class="text-danger">'+ nhMain.getLabel('chua_gui') +'</span>';
					if(KTUtil.isset(row, 'sent') && row.sent != null && row.sent > 0){
						sent = '<span class="text-success">'+ nhMain.getLabel('da_gui') +'</span>';
					}

					if(KTUtil.isset(row, 'count_sent') && row.count_sent != null && row.count_sent > 0){
						sent += '<p class="pb-0">'+ row.count_sent + ' ' + nhMain.getLabel('lan') +'</p>';
					}

					return sent;
				}
			},
			{
				field: 'created_by',
				title: nhMain.getLabel('nguoi_tao'),
				width: 120,
				sortable: false,
				template: function(row) {
					return nhList.template.createdBy(row);
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
						status = nhList.template.statusProduct(row.status);
					}
					return status;

				},
			},
			{
				field: 'action',
				title: '',
				width: 30,
				autoHide: false,
				sortable: false,
				template: function(row){
					return '\
					<div class="dropdown dropdown-inline">\
						<button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">\
							<i class="flaticon-more"></i>\
						</button>\
						<div class="dropdown-menu dropdown-menu-right pt-5 pb-5">\
							<a class="dropdown-item nh-sent-notification" href="javascript:;" data-id="'+ row.id +'" data-title="'+ row.title +'">\
								<span class="text-primary"><i class="fa fa-bell fs-14 mr-10"></i>'
									+ nhMain.getLabel('gui_thong_bao') +
								'</span>\
							</a>\
							<div class="dropdown-divider m-0"></div>\
							<a class="dropdown-item" href="' + adminPath + '/notification/update/' + row.id + '">\
								<span class="text-primary"><i class="fa fa-edit fs-14 mr-10"></i>'
									+ nhMain.getLabel('cap_nhat') +
								'</span>\
							</a>\
							<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="1">\
								<span class="text-success"><i class="fas fa-check-circle fs-14 mr-10"></i>'
									+ nhMain.getLabel('hoat_dong') +
								'</span>\
							</a>\
							<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="0">\
								<span class="text-warning"><i class="fas fa-times-circle fs-14 mr-10"></i>'
									+ nhMain.getLabel('ngung_hoat_dong') +
								'</span>\
							</a>\
							<div class="dropdown-divider m-0"></div>\
							<a class="dropdown-item nh-delete" href="javascript:;" data-id="'+ row.id +'">\
								<span class="text-danger"><i class="fas fa-trash-alt fs-14 mr-10"></i>'
									+ nhMain.getLabel('xoa') +
								'</span>\
							</a>\
						</div>\
					</div>';
				}
			}
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		  
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
		    		status: adminPath + '/notification/change-status',
			    	delete: adminPath + '/notification/delete'
			    }
		    });
		    sendNotification.init();
		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

var sendNotification = {
	modal: null,
	init: function(){
		var self = this;
		self.modal = $('#sent-notification-modal');

		if(self.modal.length == 0) return;
		self.event();

	},
	showModal: function(notification = {}){
		var self = this;
		if(self.modal.length == 0) return;
		if(typeof(notification.id) == _UNDEFINED || typeof(notification.title) == _UNDEFINED) return;

		self.clearInfoModal();
		self.loadInfoModal(notification);

		self.modal.modal('show');

	},
	clearInfoModal: function(){
		var self = this;
		if(self.modal.length == 0) return;

		self.modal.find('input#notification-id').val('');
		self.modal.find('input#token').val('');
		self.modal.find('select#platform').val('all').selectpicker('refresh');
		self.modal.find('#label-title').text('');
		self.modal.find('#wrap-token').addClass('d-none');
	},
	loadInfoModal: function(notification = {}){
		var self = this;

		if(self.modal.length == 0) return;
		if(typeof(notification.id) == _UNDEFINED || typeof(notification.title) == _UNDEFINED) return;

		self.modal.find('input#notification-id').val(notification.id);
		self.modal.find('#label-title').text(notification.title);
	},
	event: function(){
		var self = this;

		$(document).on('click', '.nh-sent-notification', function() {
		  	var _id = $(this).data('id');
		  	var title = $(this).data('title');
			if(typeof(_id) == _UNDEFINED || _id.length == 0){
		    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
		    	return false;
		    }

		    self.showModal({id: _id, title: title});
		});

		$(document).on('click', '#btn-send-notification', function() {
		  	var _id = self.modal.find('input#notification-id').val();
		  	var platform = self.modal.find('select#platform').val();
		  	var token = self.modal.find('input#token').val();
			if(typeof(_id) == _UNDEFINED || _id.length == 0){
		    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
		    	return false;
		    }

		    var btnSend = $(this);

		    KTApp.blockPage(blockOptions);
		    KTApp.progress(btnSend);

		  	nhMain.callAjax({
				url: adminPath + '/notification/send',
				data:{
					notification_id: _id,
					platform: platform,
					token: token
				}
			}).done(function(response) {
				KTApp.unprogress(btnSend);
				KTApp.unblockPage();
				
				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

			    self.modal.modal('hide');
			    if (code == _SUCCESS) {
	            	toastr.info(message);
	            	$('.kt-datatable').KTDatatable('reload');
	            } else {
	            	toastr.error(message);
	            }
			});
		});

		$(document).on('change', '#platform', function() {
			var platform = $(this).val();
			$('#wrap-token').toggleClass('d-none', platform != 'token' ? true : false);
		});
	}
}

jQuery(document).ready(function() {
	nhListNotification.listData();
});

