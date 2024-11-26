"use strict";

var nhGenealogy = {
	wrapTree: $('#kt_tree'),
	formEl: null,
	validator: null,
	modalGenealogy: null,
    init: function(){
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
        self.initLibrary();
        self.libraryTree();
    },
    event: function(){
        var self = this;
        
        $(document).on('click', '[add-genealogy]', function(e) {
			e.preventDefault();
			self.modalGenealogy.modal('show');
		});

		$(document).on('click', '[href="#genealogy_list"]', function(e) {
			e.preventDefault();

			self.listData();
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

			var labelInfo = $('[label-info]');
			if (labelInfo.length != 0) {
				switch(_val) {
				  	case 1:
				  		labelInfo.text('Thông tin vợ');
				    break;

				    case 2:
				  		labelInfo.text('Thông tin chồng');
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

		$(document).on('click', '[add-genealogy]', function(e) {
			e.preventDefault();
			self.modalGenealogy.modal('show');
		});
    },
    setDataDefaultForm: function(){
    	var self = this;

		self.modalGenealogy.find('#main-form').attr('action', adminPath + '/genealogy/save');

    	$('input[type=text], input[type=hidden]', self.modalGenealogy).each(function () {
			$(this).val('');
		});

		$('textarea.mce-editor-simple', self.modalGenealogy).each(function () {
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
    	$('[name=sex][value="other"]').prop( "checked", true );
    	$('[name=genealogical][value="1"]').prop( "checked", true );
    },
    initSubmitForm: function(formEl = null, btn_save = null){
		var self = this;
		// show loading
		KTApp.progress(btn_save);
		KTApp.blockPage(blockOptions);

		var _noteIdOld = $('[note-id-old]').val();
		var _noteIdNew = $('[note-id-old]').val();

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

				self.refreshNoteTree(1);
				return false;
            	if (typeof(_noteIdOld) != _UNDEFINED && _noteIdOld != null && _noteIdOld != '') {
            		self.refreshNoteTree(_noteIdOld);
				}

				if (typeof(_noteIdNew) != _UNDEFINED && _noteIdNew != null && _noteIdNew != '') {
					self.refreshNoteTree(_noteIdNew);
				}
            } else {
            	toastr.error(message);
            }
		});
	},
    initLibrary: function(){
    	var self = this;

    	$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		nhMain.location.init({
			idWrap: ['#main-form']
		});

		$('.kt-selectpicker').selectpicker();
		nhMain.selectMedia.single.init();
		nhMain.tinyMce.simple();
    },
    libraryTree: function(){
    	var self = this;

    	var wrapTree = $('#kt_tree');
    	if (wrapTree.length == 0) return false;

    	wrapTree.jstree({
	        "theme" : { "icons": false },
	        "plugins" : [ "contextmenu" ],
	        "contextmenu" : {items: self.customMenu}
	    });

	    wrapTree.on('ready.jstree', function() {
		    wrapTree.jstree("open_all");
		});
    },
    refreshNoteTree: function(noteId = null){
    	var self = this;

    	var wrapTree = $('#kt_tree');
    	if (wrapTree.length == 0 || typeof(noteId) == _UNDEFINED || noteId == null || noteId == '') return false;

    	var _dataTree = self.loadListGenealogy();
    	if (typeof(_dataTree) == _UNDEFINED || _dataTree == null || _dataTree == '') return false;

    	wrapTree.jstree({
	        "plugins" : [ "contextmenu" ],
	        "contextmenu" : {items: self.customMenu}
	    });

    	wrapTree.jstree().settings.core.data = _dataTree;
    	wrapTree.jstree().refresh(true, true);
    },
    customMenu: function(node) {
    	var self = this;

    	var _id = node.a_attr['data-id'];
    	var _noteId = node.id;

    	if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

	    // The default set of all items
	    var items = {
	        renameItem: { // The "rename" menu item
	            icon: "fa fa-edit",
	            label: "Thay đổi thông tin",
	            action: function (obj) {
	            	editGenealogy(_id, _noteId);
	            }
	        },
	        detailItem: { // The "rename" menu item
	            icon: "fa fa-eye",
	            label: "Chi tiết",
	            action: function (obj) {
	            	detailGenealogy(_id);
	            }
	        },
	        addItem: { // The "add" menu item
	            icon: "fa fa-plus",
	            label: "Thêm mới",
	            action: false,
	            submenu: {
                    Husband : {
                        seperator_before: false,
                        seperator_after: false,
                        label: "Chồng",
                        action: function (obj) {
                            addNewGenealogy(_id, 1);
                        }
                    },
                    Wife : {
                        seperator_before: false,
                        seperator_after: false,
                        label: "Vợ",
                        action: function (obj) {
                            addNewGenealogy(_id, 2);
                        }
                    },
                    Child : {
                        seperator_before: false,
                        seperator_after: false,
                        label: "Con",
                        action: function (obj) {
                            addNewGenealogy(_id, 3);
                        }
                    }
                }
	        },
	        deleteItem: { // The "delete" menu item
	            icon: "fa fa-trash-alt",
	            label: "Xóa",
	            action: function () {
	            	deleteGenealogy(_id);
	            }
	        }
	    };

	    return items;
	},
	listData: function() {	
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
					width: 110,
					autoHide: false,
					template: function(row) {
						var _html = '\
							<a href="javascript:;" genealogy-detail data-id="'+ row.id +'" class="btn btn-sm btn-brand btn-text-primary btn-icon btn-icon-md" title="Chi tiết">\
								<i class="la la-eye"></i>\
							</a>\
							<a href="javascript:;" genealogy-edit data-id="'+ row.id +'" class="btn btn-sm btn-danger btn-text-success btn-icon btn-icon-md" title="Chỉnh sửa">\
								<i class="la la-edit"></i>\
							</a>';

						return _html;
					},
				}]
		};	

		var datatable = $('.kt-datatable').KTDatatable(options);

	    $('#nh_status').on('change', function() {
	      	datatable.search($(this).val(), 'status');
	    });

	    $(document).on('click', '[genealogy-edit]', function(e) {
			e.preventDefault();

			var _id = $(this).attr('data-id');
			if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

			editGenealogy(_id);
		});

		$(document).on('click', '[genealogy-detail]', function(e) {
			e.preventDefault();

			var _id = $(this).attr('data-id');
			if (typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

			detailGenealogy(_id);
		});
	},
	loadListGenealogy: function(){
		var self = this;
		var result = {};

		nhMain.callAjax({
    		async: false,
			url: adminPath + '/genealogy/load-list-genealogy'
		}).done(function(response) {
			var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};

        	if (code == _SUCCESS && !$.isEmptyObject(data)) {
                result = data;                
            }
		});

		return result;
	}
}

function addNewGenealogy(relationship_info = null, relationship = null) {
	var modalGenealogy = $('#modal-add-genealogy');
    if (modalGenealogy.length == 0 || typeof(relationship_info) == _UNDEFINED || relationship_info == null || relationship_info == '') return false;

    nhGenealogy.setDataDefaultForm();

    $('[wrap-relationship]').removeClass('d-none')
    $('#relationship_info').val(relationship_info).change();
    $('[name=relationship][value='+relationship+']').prop( "checked", true );

    modalGenealogy.modal('show');
}

function editGenealogy(_id = null, _noteId = null) {
	var modalGenealogy = $('#modal-add-genealogy');
    if (modalGenealogy.length == 0 || typeof(_id) == _UNDEFINED || _id == null || _id == '') return false;

	nhMain.callAjax({
		async: false,
		dataType: 'html',
		url: adminPath + '/genealogy/update/' + _id,
	}).done(function(response) {
		modalGenealogy.find('#main-form').html(response);
		modalGenealogy.find('#main-form').attr('action', adminPath + '/genealogy/save/' + _id);
		modalGenealogy.modal('show');

		var tinyMceId = modalGenealogy.find('textarea.mce-editor-simple').attr('id');
		tinymce.get(tinyMceId).remove();

		nhGenealogy.initLibrary();

		if (typeof(_noteId) != _UNDEFINED && _noteId != null && _noteId != '' && $('[note-id-old]').length != 0) {
			$('[note-id-old]').val(_noteId);
		}
	});
}

function detailGenealogy(_id = null) {
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
}

function deleteGenealogy(_id = null) {
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
	            	nhGenealogy.refreshNoteTree(1);

	            	toastr.info(message);
	            	callback(response);
	            } else {
	            	toastr.error(message);
	            }
			});
    	}    	
    });
	return false;
}

$(document).ready(function() {
    nhGenealogy.init();
});