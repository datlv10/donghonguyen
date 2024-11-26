"use strict";

var nhGenealogy = function () {
	var ktTree = {
		ext: null,
		path: null,
		formEl: null,
		modalGenealogy: null,
		validator: null,
		load_genealogy: $("#kt_tree"),
		init: function() {
			var self = this;

			self.formEl = $('#main-form');
	        self.modalGenealogy = $('#modal-add-genealogy');
	        if (self.formEl.length == 0 || self.modalGenealogy.length == 0) return false;

			self.validator = self.formEl.validate({
				ignore: ":hidden",
				rules: {
					full_name: {
						required: true,
						maxlength: 255
					}
				},
				messages: {
					name: {
	                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
	                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
	                }
	            },

	            errorPlacement: function(error, element) {
	            	var messageRequired = element.attr('message-required');
	            	if(typeof(messageRequired) != _UNDEFINED && messageRequired.length > 0){
	            		error.text(messageRequired);
	            	}

	            	error.addClass('invalid-feedback')

	                var group = element.closest('.input-group');
	                if (group.length) {
	                    group.after(error);
	                }else if(element.hasClass('select2-hidden-accessible')){
	            		element.closest('.form-group').append(error);
	                }else{
	                	element.after(error);
	                }
	            },

				invalidHandler: function(event, validator) {
					KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
				}
			});

			self.event();
			self.library();
			self.readGenealogy();
		},
		event: function(){
			var self = this;

			$(document).on('click', '[add-genealogy]', function(e) {
				e.preventDefault();

				self.setDataDefaultForm();
				self.modalGenealogy.modal('show');
			});

			$(document).on('click', '[nh-tab-genealogy]', function(e) {
				e.preventDefault();

				if ($(this).attr('href') == '#genealogy_list') {
					self.listData();

					if ($('[group-excel]').length != 0) {
						$('[group-excel]').removeClass('d-none');
					}

					return false;
				}
				
				if ($('[group-excel]').length != 0) {
					$('[group-excel]').addClass('d-none');
				}
			});

	        $(document).on('click', '#save-genealogy', function(e) {
				e.preventDefault();

				if (self.validator.form()) {
					$('#content').val(tinymce.get('content').getContent());

					self.initSubmitForm(self.formEl, $(this));
				}
			});

			$(document).on('change', '[name="relationship"]', function(e) {
				var _val = parseInt($(this).val());
				var wrap_relationship = $('[wrap-relationship]');

				if (typeof(_val) == _UNDEFINED || wrap_relationship.length == 0) return false;

				if (_val === 0) {
					wrap_relationship.addClass('d-none');

					$('input', wrap_relationship).each(function () {
						$(this).val('');
					});

					return true;
				}

				var _title = $(this).attr('data-title');
				wrap_relationship.removeClass('d-none');

				if (typeof(_title) == _UNDEFINED || _title == null || _title == '') return true;
				
				if ($('[label-self-name]').length !=0 ) {
	    			$('[label-self-name]').text('Tên tự');
	    		}

				var labelInfo = $('[label-info]');
				if (labelInfo.length != 0) {
					switch(_val) {
					  	case 1:
					  		labelInfo.text('Thông tin vợ');
					    break;

					    case 2:
					  		labelInfo.text('Thông tin chồng');

					  		if ($('[label-self-name]').length !=0 ) {
				    			$('[label-self-name]').text('Tên hiệu');
				    		}
					    break;

					    case 3:
					  		labelInfo.text('Thông tin bố/mẹ');
					    break;
					}
				}

				var labelPosition = $('[label-position]');
				if (labelPosition.length != 0) {
					labelPosition.text(_title + ' thứ');
				}
			});

			$(document).on('change', '[name="sex"]', function(e) {
				var _val = $(this).val();

				if (typeof(_val) == _UNDEFINED) return false;

				if (_val === 'female') {
					$('[name=genealogical][value="0"]').prop( "checked", true );
					return true;
				}

				$('[name=genealogical][value="1"]').prop( "checked", true );
			});
		},
		library: function(){
			var self = this;

	    	$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			nhMain.location.init({
				idWrap: ['#main-form']
			});

			$('.datepicker').each(function() {
				$(this).datepicker({
		            format: 'dd/mm/yyyy',
		            showMeridian: true,
		            todayHighlight: true,
		            autoclose: true
		        });
			});

			$('.kt-selectpicker').selectpicker();
			nhMain.selectMedia.single.init();
			nhMain.tinyMce.simple();
		},
		initSubmitForm: function(formEl = null, btn_save = null){
			var self = this;
			// show loading
			KTApp.progress(btn_save);
			KTApp.blockPage(blockOptions);

			nhMain.attributeInput.setValueBeforeSubmit(formEl);
			var formData = formEl.serialize();
			
			nhMain.callAjax({
				url: formEl.attr('action'),
				data: formData
			}).done(function(response) {
				// hide loading
				KTApp.unprogress(btn_save);
				KTApp.unblockPage();

				//show message and redirect page
			   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	toastr.clear();

	            if (code == _SUCCESS) {
	            	toastr.info(message);
	            	self.modalGenealogy.modal('hide');

					self.refreshGenealogy();
					self.refreshRelationship();

					if ($('#genealogy_list').length != 0 && $('#genealogy_list').hasClass('active')) {
						$('.kt-datatable').KTDatatable('reload');
					}
	            } else {
	            	toastr.error(message);
	            }
			});
		},
		setDataDefaultForm: function(params = {}){
	    	var self = this;

	    	var relationship = typeof(params.relationship) != _UNDEFINED ? params.relationship : null;

			self.modalGenealogy.find('#main-form').attr('action', adminPath + '/genealogy/save');

	    	self.modalGenealogy.find('input[type=text], input[type=hidden]').each(function () {
				$(this).val('');
			});

			self.modalGenealogy.find('textarea.mce-editor-simple').each(function () {
				$(this).val('');
				$(this).html('');
			});

			if ($('.kt-avatar__holder').length != 0) {
				$('.kt-avatar__holder').css('background-image', '').attr('href', '');
			}

			$('#education_level').val('').change();
			$('#city_id').val('').change();
			$('#district_id').val('').change();
	    	$('[name=status][value="1"]').prop( "checked", true );
	    	$('[name=sex][value="male"]').prop( "checked", true );
	    	$('[name=genealogical][value="1"]').prop( "checked", true );

	    	if (typeof(relationship) != _UNDEFINED && relationship != null && relationship != '' && relationship == 2) {
	    		$('[name=sex][value="female"]').prop( "checked", true );
	    		$('[name=genealogical][value="0"]').prop( "checked", true );

	    		if ($('[label-self-name]').length !=0 ) {
	    			$('[label-self-name]').text('Tên hiệu');
	    		}
	    	}
	    },
		readGenealogy: function() {
			var self = this;
			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
                url: adminPath + '/genealogy/load-list-genealogy',
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';

                toastr.clear();
                if (code == _SUCCESS) {
                	KTApp.unblockPage();
			        self.load_genealogy.jstree({
			            core : {
			                'themes' : {
			                    'responsive': false
			                }, 
			                'multiple': false,
			                'check_callback': true,
			                'data': data
			            },
			            types : {
			                'default' : {
			                    'icon' : 'fa fa-folder kt-font-success'
			                },
			                'file' : {
			                    'icon' : 'fa fa-file  kt-font-success'
			                }
			            },
			            state : { 'key' : 'demo2' },
			            plugins : [ 'contextmenu', 'state', 'types' ],
			            contextmenu : { 
			            	items: function ($node) {
			            		var tree = $('#kt_tree').jstree(true);
			            		return self.contextMenu($node, tree);
			            	}
			            	
			            },
			        });
                } else {
                    toastr.error(message);
                }   
            });
		},
		refreshGenealogy: function() {
			var self = this;

			nhMain.callAjax({
                url: adminPath + '/genealogy/load-list-genealogy',
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';

                toastr.clear();
                if (code == _SUCCESS) {
                	self.load_genealogy.jstree(true).settings.core.data = data;
					self.load_genealogy.jstree(true).refresh();
                } else {
                    toastr.error(message);
                }   
            });
		},
		refreshRelationship: function(){
			var self = this;

			// clear select
			var relationshipSelect = self.modalGenealogy.find('#relationship_info');
			relationshipSelect.find('option:not([value=""])').remove();
			relationshipSelect.selectpicker('refresh');

			nhMain.callAjax({
                url: adminPath + '/genealogy/load-relationship',
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';

                toastr.clear();
                if (code == _SUCCESS) {
                	if (!$.isEmptyObject(data)) {
                    	var listOption = '';
				        $.each(data, function (id, name) {
				            listOption += '<option value="' + id + '">' + name + '</option>';
				        });

				        relationshipSelect.append(listOption);
				        relationshipSelect.selectpicker('refresh');
                    }		
                } else {
                    toastr.error(message);
                }   
            });
		},
		contextMenu: function($node, tree){
			var self = this;
			var _id = $node.id;
			if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

			return {
				'detail': {
		        	icon: "fa fa-eye",
		            label: "Chi tiết",
		            action: function (obj) {
		            	self.detailGenealogy(_id);
		            }
		        },
		        'rename': {
		            icon: "fa fa-edit",
	            	label: "Thay đổi thông tin",
		            action: function (obj) {
		            	self.editGenealogy(_id);
                    }
		        },
		        'add': {
		            icon: "fa fa-plus",
		            label: "Thêm mới",
		            action: false,
		            submenu: {
	                    'Husband' : {
	                        seperator_before: false,
	                        seperator_after: false,
	                        label: "Chồng",
	                        action: function (obj) {
	                            self.addNewGenealogy(_id, 1);
	                        }
	                    },
	                    'Wife' : {
	                        seperator_before: false,
	                        seperator_after: false,
	                        label: "Vợ",
	                        action: function (obj) {
	                            self.addNewGenealogy(_id, 2);
	                        }
	                    },
	                    'Child' : {
	                        seperator_before: false,
	                        seperator_after: false,
	                        label: "Con",
	                        action: function (obj) {
	                            self.addNewGenealogy(_id, 3);
	                        }
	                    }
	                }
		        },
		        'remove': {
                    icon: "fa fa-trash-alt",
	            	label: "Xóa",
                    action: function (obj) {
                    	self.deleteGenealogy(_id, $node, tree);
                    }
                }
			}
		},
		detailGenealogy: function(_id = null){
			var self = this;

			var modalGenealogyDetail = $('#modal-detail-genealogy');
		    if (modalGenealogyDetail.length == 0 || typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

			nhMain.callAjax({
				async: false,
				dataType: 'html',
				url: adminPath + '/genealogy/detail/' + _id,
			}).done(function(response) {
				modalGenealogyDetail.find('.modal-content').html(response);
				modalGenealogyDetail.modal('show');
			});
		},
		addNewGenealogy: function(relationship_info = null, relationship = null){
			var self = this;
		    if (typeof(relationship_info) == _UNDEFINED || relationship_info == null || relationship_info == '') return false;

		    var tinyMceId = self.modalGenealogy.find('textarea.mce-editor-simple').attr('id');
			tinymce.get(tinyMceId).remove();

			self.library();

		    self.setDataDefaultForm({relationship: relationship});

		    $('[wrap-relationship]').removeClass('d-none')
		    $('#relationship_info').val(relationship_info).change();
		    $('[name=relationship][value='+relationship+']').prop( "checked", true );

		    self.modalGenealogy.modal('show');
		},
		editGenealogy: function(_id = null){
			var self = this;

		    if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

			nhMain.callAjax({
				async: false,
				dataType: 'html',
				url: adminPath + '/genealogy/update/' + _id,
			}).done(function(response) {
				self.modalGenealogy.find('#main-form').html(response);
				self.modalGenealogy.find('#main-form').attr('action', adminPath + '/genealogy/save/' + _id);
				self.modalGenealogy.modal('show');

				var tinyMceId = self.modalGenealogy.find('textarea.mce-editor-simple').attr('id');
				tinymce.get(tinyMceId).remove();

				self.library();
			});
		},
		deleteGenealogy: function(_id = null, $node = null, tree = null){
			var self = this;
			if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

			swal.fire({
		        title: nhMain.getLabel('xoa_ban_ghi'),
		        text: 'Bạn có chắc chắn xóa thông tin này!',
		        type: 'warning',
		        
		        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){

		    		KTApp.blockPage(blockOptions);
					nhMain.callAjax({
						async: false,
						url: adminPath + '/genealogy/delete',
						data: {
							id: _id
						}
					}).done(function(response) {
						KTApp.unblockPage();

						var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
					    if (code == _SUCCESS) {
					    	toastr.info(message);

					    	if ($('#genealogy_list').length != 0 && $('#genealogy_list').hasClass('active')) {
								$('.kt-datatable').KTDatatable('reload');
								return false;
							}

			            	tree.delete_node($node);
			            	
			            } else {
			            	toastr.error(message);
			            }
					});
		    	}    	
		    });
		},
		listData: function() {	
			var self = this;

			var options = {
				data: {
					type: 'remote',
					source: {
						read: {
							url: adminPath + '/genealogy/list/json',
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
						field: 'image_avatar',
						title: '<div><i class="fa fa-image fa-lg"></i></div>',
						sortable: false,
						width: 155,
						template: function(row) {
							var imageAvatar = nhMain.utilities.notEmpty(row.image_avatar) ? row.image_avatar : '';
							var imageAvatarThumb = nhMain.utilities.notEmpty(imageAvatar) ? cdnUrl + nhMain.utilities.getThumbs(imageAvatar, 150) : '/admin/assets/media/users/default.jpg';

							var _html = '\
							<a class="symbol" href="'+ cdnUrl + imageAvatar +'">\
								<img src="'+ imageAvatarThumb +'">\
							</a>';

							return _html;
						}
					},
					{
						field: 'full_name',
						title: 'Họ và tên',
						autoHide: false,
						width: 200
					},
					{
						field: 'sex_name',
						title: 'Giới tính',
						sortable: true,
						textAlign: 'center',
						width: 100,
					},
					{
						field: 'education_level_name',
						title: 'Trình độ học vấn',
						sortable: true,
						textAlign: 'center',
						width: 200,
					},
					{
						field: 'generation',
						title: 'Đời thứ',
						sortable: true,
						textAlign: 'center',
						width: 100,
					},
					{
						field: 'status_name',
						title: 'Tình trạng',
						width: 110,
						autoHide: false
					},
					{
						field: 'action',
						title: 'Hành động',
						textAlign: 'center',
						width: 150,
						autoHide: false,
						template: function(row) {
							var _html = '\
								<a href="javascript:;" genealogy-detail data-id="'+ row.id +'" class="btn btn-sm btn-brand btn-text-primary btn-icon btn-icon-md" title="Chi tiết">\
									<i class="la la-eye"></i>\
								</a>\
								<div class="dropdown dropdown-inline">\
									<button type="button" class="btn btn-clean btn-icon btn-success bg-success btn-sm btn-icon-md" data-toggle="dropdown">\
										<i class="la la-plus"></i>\
									</button>\
									<div class="dropdown-menu dropdown-menu-right pt-5 pb-5">\
										<a class="dropdown-item" href="javascript:;" genealogy-add data-id="'+ row.id +'" relationship="1">\
											<span class="">Chồng</span>\
										</a>\
										<a class="dropdown-item" href="javascript:;" genealogy-add data-id="'+ row.id +'" relationship="2">\
											<span class="">Vợ</span>\
										</a>\
										<a class="dropdown-item" href="javascript:;" genealogy-add data-id="'+ row.id +'" relationship="3">\
											<span class="">Con</span>\
										</a>\
									</div>\
								</div>\
								<a href="javascript:;" genealogy-edit data-id="'+ row.id +'" class="btn btn-sm btn-brand btn-text-success btn-icon btn-icon-md" title="Chỉnh sửa">\
									<i class="la la-edit"></i>\
								</a>\
								<a href="javascript:;" genealogy-delete data-id="'+ row.id +'" class="btn btn-sm btn-danger btn-text-success btn-icon btn-icon-md" title="Chỉnh sửa">\
									<i class="la la-trash"></i>\
								</a>';

							return _html;
						},
					}]
			};	

			var datatable = $('.kt-datatable').KTDatatable(options);

		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#genealogical').on('change', function() {
		      	datatable.search($(this).val(), 'genealogical');
		    });

		    $('#generation').on('change', function() {
		      	datatable.search($(this).val(), 'generation');
		    });

		    $('#sex').on('change', function() {
		      	datatable.search($(this).val(), 'sex');
		    });

		    $('#birthday').on('change', function() {
		      	datatable.search($(this).val(), 'birthday');
		    });

		    $('#city_id').on('change', function() {
		      	datatable.search($(this).val(), 'city_id');
		    });

		    $('#district_id').on('change', function() {
		      	datatable.search($(this).val(), 'district_id');
		    });

		    $(document).on('click', '[genealogy-edit]', function(e) {
				e.preventDefault();

				var _id = $(this).attr('data-id');
				if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

				self.editGenealogy(_id);
			});

			$(document).on('click', '[genealogy-add]', function(e) {
				e.preventDefault();

				var _id = $(this).attr('data-id');
				var _relationship = $(this).attr('relationship');
				if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

				self.addNewGenealogy(_id, _relationship);
			});

			$(document).on('click', '[genealogy-detail]', function(e) {
				e.preventDefault();

				var _id = $(this).attr('data-id');
				if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

				self.detailGenealogy(_id);
			});

			$(document).on('click', '[genealogy-delete]', function(e) {
				e.preventDefault();

				var _id = $(this).attr('data-id');
				if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

				self.deleteGenealogy(_id);
			});

			$('#btn-refresh-search').on('click', function (e) {
				KTApp.blockPage(blockOptions);
		    	$('.nh-search-advanced input').val('');
		    	$('.nh-search-advanced .kt-selectpicker').val('');
		    	$('.nh-search-advanced .kt-selectpicker').selectpicker('refresh');
				datatable.setDataSourceParam('query','');
		    	$('.kt-datatable').KTDatatable('load');
		    	KTApp.unblockPage();
			});

			$('[nh-export]').on('click', function (e) {
                e.preventDefault();
                KTApp.blockPage(blockOptions);
                var nhExport = typeof($(this).attr('nh-export')) != _UNDEFINED ? $(this).attr('nh-export') : '';
                var page = typeof(datatable.getCurrentPage()) != _UNDEFINED ? datatable.getCurrentPage() : 1;

                var data_filter = {
					keyword: $('#nh-keyword').val(),
					status: $('[name=status]').val(),
					genealogical: $('#genealogical').val(),
					generation: $('#generation').val(),
					sex: $('#sex').val(),
					birthday: $('#birthday').val(),
					city_id: $('#city_id').val(),
					district_id: $('#district_id').val()
				}

                nhMain.callAjax({
                    url: adminPath + '/genealogy/list/json',
					data: {
						'data_filter': data_filter,
						'pagination': {page: page},
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
		}
    }

	return {
		init: function() {		 
			ktTree.init();
		}
	};
}();

$(document).ready(function() {
	nhGenealogy.init();
});