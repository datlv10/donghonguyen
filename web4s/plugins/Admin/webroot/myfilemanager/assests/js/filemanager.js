"use strict";

var fileManager = {
	wrapMainElement: $('#nh-filemanager'),
	wrapListFilesElement: $('[nh-wrap="list-files"]'),
	wrapBreadcrumbElement: $('[nh-wrap="breadcrumb"]'),
	wrapNavigationElement: $('[nh-wrap="navigation"]'),
	wrapSortColumn :$('[nh-wrap="sort-column"]'),
	wrapInfoElement :$('[nh-wrap="info"]'),
	basePath: '/media',
	showInfo: true,
	viewList: 'gird',
	copy: {
		files: [],
		cut: false
	},
	indexActiveItem: null,
	listFileParams: {
		page: 1,
		path: '/media',
		filter_type: '',
		filter_keyword: '',
		sort_type: '',
		sort_field: ''
	},
	listLogParams: {
		page: 1,
		limit: null,

		filter_action: '',
		filter_date_from: '',
		filter_date_to: '',
		filter_fullname: ''
	},	
	init: function(){
		var self = this;

		if(self.wrapMainElement.length == 0 || self.wrapListFilesElement.length == 0) return;		

		self.events();

		// website nhúng iframe cdn có truyền lọc theo loại tệp
		self.showFilterTypeFile();

		// hiển thị panel info khi load trang (ưu tiên đọc từ localStore)
		var showPanelInfo = self.myLocalStorage.getItem('showInfo');
		self.showInfo = false;
		if(showPanelInfo == null || parseInt(showPanelInfo) > 0) self.showInfo = true;

		self.showPanelInfo();

		// set kiểu hiển thị ds tệp
		self.viewList = self.myLocalStorage.getItem('viewList');
		self.showViewList();

		// load files
		var currentPath = self.myLocalStorage.getItem('currentPath');
		if(currentPath != null && typeof(currentPath) != _UNDEFINED && currentPath.length > 0){
			self.listFileParams.path = currentPath;
		}
		self.loadListFiles(true);

		// set chiều cao các panel
		self.setHeightMainWrap();

	},
	events: function(){
		var self = this;

		// lọc theo loại
		$(document).on('click', '[nh-btn="filter"]', function(e) {
			var type = $(this).data('type');
			if(typeof(type) == _UNDEFINED || $.inArray(type, [ _IMAGE, _VIDEO, _AUDIO, _DOCUMENT, _ARCHIVE]) == -1) return;

			self.listFileParams.page = 1;
			self.listFileParams.filter_type = type;

			$('[nh-btn="filter"]').removeClass('active');
			$(this).addClass('active');

			self.loadListFiles(true);
		});

		// lọc theo từ khoá
		var timeOutKeyword = null;
		$(document).on('keyup', '[nh-input="filter"]', function(e) {

			// delay gõ từ khoá 0.5s thì mới search
			clearTimeout(timeOutKeyword);
			timeOutKeyword = setTimeout(() => {
			    var keyword = $(this).val();

				self.listFileParams.page = 1;
				self.listFileParams.filter_keyword = keyword;

				self.loadListFiles(true);
			 }, 500);
	
		});

		// bỏ lọc
		$(document).on('click', '[nh-btn="clear-filter"]', function(e) {
			$('[nh-input="filter"]').val('');

			self.listFileParams.page = 1;
			self.listFileParams.filter_type = '';
			self.listFileParams.filter_keyword = '';

			$('[nh-btn="filter"]').removeClass('active');

			self.loadListFiles(true);
		});

		// sắp xếp
		$(document).on('click', '[nh-btn="sort"]', function(e) {
			var type = $(this).data('type');
			var sortType = $(this).attr('data-sort-type');
			
			if(typeof(sortType) == _UNDEFINED || sortType.length == 0 || $.inArray(sortType, [_ASC, _DESC]) == -1) sortType = _ASC;
			if(typeof(type) == _UNDEFINED || $.inArray(type, [_TIME, _SIZE, _EXTENSION, _NAME]) == -1) return;

			if(self.wrapSortColumn.length > 0){
				self.wrapSortColumn.find('svg').remove();
				self.wrapSortColumn.find('[data-sort-type]').attr('data-sort-type', '');
			}
			
			if(sortType == _ASC){
				$(this).attr('data-sort-type', _DESC);
				$(this).append(_ICON_ARROW_UP);
			}else{
				$(this).attr('data-sort-type', _ASC);
				$(this).append(_ICON_ARROW_DOWN);
			}

			self.listFileParams.page = 1;
			self.listFileParams.sort_field = type;
			self.listFileParams.sort_type = sortType;

			self.loadListFiles(true);
		});

		// đổi kiểu hiển thị grid hoặc list
		$(document).on('click', '[nh-btn="view-list"]', function(e) {
			var type = $(this).data('type');
			if(self.wrapListFilesElement.length == 0 || typeof(type) == _UNDEFINED || $.inArray(type, ['gird', 'list']) == -1) return;

			// active button
			$('[nh-btn="view-list"]').removeClass('active');
			$(this).addClass('active');

			// set value in localStore
			self.myLocalStorage.setItem('viewList', type);
			self.viewList = type;

			self.showViewList();
		});	

		// ẩn hiện panel xem chi tiết file
		$(document).on('click', '[nh-btn="view-detail"]', function(e) {

			if(self.wrapInfoElement.length == 0) return;
			var show = true;
			if(self.showInfo == true) show = false;

			// set value to localStore
			self.myLocalStorage.setItem('showInfo', show ? 1 : 0);
			self.showInfo = show;			

			self.showPanelInfo();
		});		

		// truy cập folder trên breacrumb và navigation
		self.wrapMainElement.on('click', '[nh-btn="redirect"]', function(e) {
			var path = $(this).data('path');
			if(typeof(path) == _UNDEFINED) return;

			// thêm class open vào icon arrow ở navigation
			if($(this).find('.arrow-child-folder').length > 0) $(this).find('.arrow-child-folder').addClass('open');

			// load ds tệp
			self.listFileParams.path = path;
			self.loadListFiles(true);
		});

		// tạo folder
		self.wrapMainElement.on('click', '[nh-btn="create-folder"]', function(e) {
			self.actions.createFolder();
		});

		// hiển thị thêm tệp khi scroll chuột
		$('.files-wrap').scroll(function() {
		    if(this.scrollHeight - this.scrollTop === this.clientHeight) {		    
		    	var page = self.wrapListFilesElement.attr('data-page');
		    	self.listFileParams.page = parseInt(page) + 1;
		    	
				self.loadListFiles();
		    }
		});

		// dblclick truy cập hoặc quay lại folder
		self.wrapListFilesElement.on('dblclick touchstart', 'figure[data-type="folder"]', function(e) {

			var path = $(this).data('path');
			if(typeof(path) == _UNDEFINED) return;
			self.listFileParams.path = path;
			
			self.loadListFiles(true);
		});

		// click vào file hoặc folder
		self.wrapListFilesElement.on('click', 'figure', function(e) {
			var indexCurrent = $(this).closest('li').index();

			// giữ ctrl hoặc cmd 
			if(e.ctrlKey || e.metaKey) {
				var thisActive = $(this).hasClass('active') ? true : false;
				$(this).toggleClass('active', !thisActive);
			}

			// giữ shift
			if(e.shiftKey){
				// nếu đã chọn 1 item khác thì sẽ active tất cả item từ item đã chọn trc đó đến item hiện tại
				if(self.indexActiveItem != null){
					var start = 0;
					var end = 0;

					if(indexCurrent > self.indexActiveItem){
						start = self.indexActiveItem;
						end = indexCurrent;
					}else{
						start = indexCurrent;
						end = self.indexActiveItem;
					}

					for(var i = start; i <= end; i++){
						self.wrapListFilesElement.find(`li:eq(${i})`).find('figure').addClass('active');
					}
				}
			}

			// click chuột bình thường
			if(!e.ctrlKey && !e.metaKey && !e.shiftKey){
				self.wrapListFilesElement.find('figure').removeClass('active');
				$(this).addClass('active');				
				// gửi đường dẫn ảnh sang website nhúng iframe cdn
				if(
					typeof($(this).data('type')) != _UNDEFINED && $(this).data('type') != _FOLDER &&
					typeof(cdnCrossDomain) != _UNDEFINED && parseInt(cdnCrossDomain) > 0 && 
					typeof(cdnMultiple) != _UNDEFINED && parseInt(cdnMultiple) == 0 &&
					typeof(cdnFieldId) != _UNDEFINED && cdnFieldId.length > 0
				){
					var url = typeof($(this).data('path')) != _UNDEFINED ? prefixUrlFile + $(this).data('path') : null;
					// gửi đường dẫn và field đã chọn sang website 
					parent.postMessage({
		                sender: 'myfilemanager',
		                url: [url],
		                field_id: cdnFieldId
		            }, "*");
				}
			}

			// set index active
			self.indexActiveItem = indexCurrent;

			// hiển thị thông tin chi tiết
			self.showInfoFile($(this));
		});

		// hủy active khi click ngoài item
		$(window).click(function(e) {
		  	var target = $(e.target);
		  	// nếu click ngoài figure , modal, context-menu-list thì clear active file
		  	if(
		  		target.closest('#nh-filemanager').length == 0 && 
		  		target.closest('.modal').length == 0 && 
		  		target.closest('.context-menu-list').length == 0
		  	) {
			    self.wrapListFilesElement.find('figure').removeClass('active');
			    self.indexActiveItem = null;
			}

			// đóng thẻ nh-menu-element 
			if(!target.is('[nh-dropdown]')){
				$('[nh-dropdown-element]').hide();
			}

			// nếu click trong wrap list file manager nhưng không vào figure item thì bỏ chọn item

			if(
				target.closest('[nh-wrap="list-files"]').length > 0 && 
				target.closest('figure').length == 0
			){
				self.wrapListFilesElement.find('figure').removeClass('active');
			}
		});

		// ngăn sự kiện click chuột phải trên toàn trang
		$(document).on('contextmenu', function(e) {
			self.wrapListFilesElement.find('figure').removeClass('active');
			self.indexActiveItem = null;

		  	return false;
		});

		// click chuột phải vào file
		self.wrapMainElement.contextMenu({
			selector: '[nh-wrap="list-files"], figure',
			autoHide: false,
			build: function (element, event) {
				event.preventDefault();

				var items = {}
	            
	            // click chuột phải vào figure file
	            if(element.prop('tagName') == 'FIGURE'){
					
					var thisActive = element.hasClass('active') ? true : false;

					if(!thisActive){
						self.wrapListFilesElement.find('figure').removeClass('active');
					}

	            	// active item
	            	element.addClass('active');
	            	self.indexActiveItem = element.closest('li').index();
	            	
	            	// hiển thị thông tin chi tiết
					self.showInfoFile(element);

	            	var type = element.data('type');

	            	if(type != _FOLDER){	            		

	            		items['download'] = {
							name: `
								<span class="item-custom">
									${nhMain.getLabel('tai_ve')} 
									<span class="shortcut">
										Ctrl + D
									</span>
								</span>`,
							isHtmlName: true,
							icon: 'download'
						}

						items['see_link'] = {
							name: `
								<span class="item-custom">
									${nhMain.getLabel('duong_dan')} 
									<span class="shortcut">
										Ctrl + L
									</span>
								</span>`,
							isHtmlName: true,								
							icon: 'see-link'
						}

						items['step1'] = '---------';

						items['copy'] = {
							name: `
								<span class="item-custom">
										${nhMain.getLabel('sao_chep')} 
										<span class="shortcut">
											Ctrl + C
										</span>
								</span>`,
							isHtmlName: true,
							icon: 'copy'

						}

						items['cut'] = {
							name: `
								<span class="item-custom">
										${nhMain.getLabel('cat')} 
										<span class="shortcut">
											Ctrl + X
										</span>
								</span>`,
							isHtmlName: true,
							icon: 'cut'
						}

						if(self.copy.files.length > 0){
							items['paste'] = {
								name: `
									<span class="item-custom">
											${nhMain.getLabel('dan')} 
											<span class="shortcut">
												Ctrl + V
											</span>
									</span>`,
								isHtmlName: true,
								icon: 'paste'
							}
						}

						items['step2'] = '---------';

						items['rename'] = {
							name: `
								<span class="item-custom">
										${nhMain.getLabel('doi_ten')} 
										<span class="shortcut">
											Ctrl + R
										</span>
								</span>`,
							isHtmlName: true,
							icon: 'rename'
						}
	            	}					
					
					items['delete'] = {
						name: `
							<span class="item-custom">
									${nhMain.getLabel('xoa')} 
									<span class="shortcut">
										Del / ←
									</span>
							</span>`,
						isHtmlName: true,
						icon: 'delete'
					}

					if(
						typeof(cdnCrossDomain) != _UNDEFINED && parseInt(cdnCrossDomain) > 0 && 
						typeof(cdnMultiple) != _UNDEFINED && parseInt(cdnMultiple) > 0 &&
						typeof(cdnFieldId) != _UNDEFINED && cdnFieldId.length > 0
					){
						items['step3'] = '---------';

            			items['select'] = {
							name: `
								<span class="item-custom">
									${nhMain.getLabel('chon')} 
								</span>`,
							isHtmlName: true,
							icon: 'select'
						}

						items['unselect'] = {
							name: `
								<span class="item-custom">
									${nhMain.getLabel('bo_chon')} 
								</span>`,
							isHtmlName: true,
							icon: 'unselect'
						}						
            		}
	            }

	            if($.isEmptyObject(items)){

	            	if(self.copy.files.length > 0){
		            	items['paste'] = {
		            		name: `
								<span class="item-custom">
										${nhMain.getLabel('dan')} 
										<span class="shortcut">
											Ctrl + V
										</span>
								</span>`,
							isHtmlName: true,
		            		icon: 'paste'
		            	};

		            	items['step1'] = '---------';
		            }	            	

	            	items['upload'] = {
	            		name: `
							<span class="item-custom">
									${nhMain.getLabel('tai_len')}
							</span>`,
						isHtmlName: true,
	            		icon: 'upload'
	            	};

	            	items['step2'] = '---------';

	            	items['create_folder'] = {
	            		name: `
							<span class="item-custom">
									${nhMain.getLabel('tao_thu_muc')} 
							</span>`,
						isHtmlName: true,
	            		icon: 'create-folder'
	            	};

	            	// nếu click chuột phải trong folder thì clear những item đang active
	            	self.wrapListFilesElement.find('figure').removeClass('active');
	            }

				return {
					items: items,
					callback: function(key, options){
						switch(key) {
                            case 'download':
                                self.actions.download();
                                break;

                            case 'copy':
                            	self.actions.copy();
                            	break;

                            case 'cut':
                            	var cut = true;
                            	self.actions.copy(cut);
                            	break;

                            case 'paste':
                            	self.actions.paste();
                            	break;

                            case 'rename':
                            	self.actions.rename();
                            	break;

                            case 'delete':
                            	self.actions.delete();
                            	break;

                            case 'upload':
                            	$('[nh-input="select-upload-file"]').trigger('click');
                            	break;

                            case 'create_folder':
                            	self.actions.createFolder();
                            	break;

                            case 'see_link':
                            	self.actions.seeLink();
                            	break;

                            case 'select':
                            	self.wrapListFilesElement.find(`figure.active:not([data-type="${_FOLDER}"]) input[nh-input="select-file"]`).prop('checked', true);
                            	self.wrapListFilesElement.find(`figure.active:not([data-type="${_FOLDER}"])`).addClass('checked');

								$('[nh-group="select"]').removeClass('d-none');

                            	break;

                            case 'unselect':
                            	self.wrapListFilesElement.find(`figure.active:not([data-type="${_FOLDER}"]) input[nh-input="select-file"]`).prop('checked', false);
                            	self.wrapListFilesElement.find(`figure.active:not([data-type="${_FOLDER}"])`).removeClass('checked');

                            	break;
                        }
					}
				};
			}

		});

		// click button upload
		$(document).on('click', '[nh-btn="upload"]', function(e) {
			$('[nh-input="select-upload-file"]').trigger('click');
		});
		
		// trigger input upload
		$(document).on('change', 'input[nh-input="select-upload-file"]', function(e) {
			self.actions.upload($(this)[0].files);
		});

		// dropdown
		$(document).on('click', '[nh-dropdown]', function(e) {
			var key = $(this).attr('nh-dropdown');
			var menuElement = $('[nh-dropdown-element="' + key + '"]');

			if(menuElement.length > 0) menuElement.toggle();
		});

		//click arrow-child-folder
		self.wrapNavigationElement.on('click', '.arrow-child-folder', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var liElement = $(this).closest('li');
			if(liElement.length == 0) return;

			var listChildElement = liElement.find('ul.navigation-folder');

			// click vào nếu child folder chưa được load thì load ds
			if(listChildElement.length == 0){
				var path = liElement.find('[nh-btn="redirect"]').data('path');
				if(typeof(path) == _UNDEFINED || path.length == 0) return;
				self.listFileParams.path = path;

				self.loadListFiles(true);
				$(this).toggleClass(`open`);

			}else{
				var hidden = listChildElement.hasClass('d-none') ? false : true;
				listChildElement.toggleClass('d-none', hidden);

				$(this).toggleClass('open');
			}			
		});

		// show log
		$(document).on('click', '[nh-btn="view-log"]', function(e) {
			self.showListLog();
		});

		// load more log
		$(document).on('click', '[nh-btn="log-load-more"]', function(e) {
			var page = typeof($(this).attr('data-page')) != _UNDEFINED ? parseInt($(this).attr('data-page')) : 1;

			self.listLogParams.page = page;

			var tableElement = $(this).closest('table[nh-table="logs"]');
			self.loadListLog(tableElement, true);
		});

		// filter log
		$(document).on('click', '[nh-btn="log-filter"]', function(e) {
			var dateFrom = $('[nh-input="log-date-from"]').val();
			var dateTo = $('[nh-input="log-date-to"]').val();

			var action = $('[nh-select="log-action"]').val();
			var fullname = $('[nh-input="log-fullname"]').val();

			self.listLogParams.page = 1;
			self.listLogParams.file = null;

			self.listLogParams.filter_date_from = typeof(dateFrom) != _UNDEFINED ? dateFrom : null;
			self.listLogParams.filter_date_to = typeof(dateTo) != _UNDEFINED ? dateTo : null;
			self.listLogParams.filter_action = typeof(action) != _UNDEFINED ? action : null;
			self.listLogParams.fullname = typeof(fullname) != _UNDEFINED ? fullname : null;

			var tableElement = $('table[nh-table="logs"]');
			self.loadListLog(tableElement);
		});	

		// clear filter log
		$(document).on('click', '[nh-btn="clear-log-filter"]', function(e) {
			$('[nh-input="log-date-from"]').val('');
			$('[nh-input="log-date-to"]').val('');
			$('[nh-select="log-action"]').val('');
			$('[nh-input="log-fullname"]').val('');

			self.clearLogParams();

			var tableElement = $('table[nh-table="logs"]');
			self.loadListLog(tableElement);
		});		

		// download file chi tiết
		$(document).on('click', '[nh-btn="download-detai"]', function(e) {
			self.actions.download();
		});	

		// event key up
		$(document).on('keydown', function(e) {
			// kiểm tra shortcut button của modal
			if($('[nh-modal]').length > 0 && $(`[shortcut="${e.keyCode}"]`).length > 0){
				e.preventDefault();

				if ($(`[shortcut="${e.keyCode}"]`).is('[type=text]')) {
		            $(`[shortcut="${e.keyCode}"]`).focus();
		        } else {
		            $(`[shortcut="${e.keyCode}"]`).trigger('click');
		        }
			}


			// nếu modal action đang bật thì ko kiểm tra các phím tắt của file item
			if($('[nh-modal]').is(':visible')) return;

			var preventDefault = false;

			// ctrl + d || cmd + d
		  	if ((e.ctrlKey || e.metaKey) && e.keyCode == 68 && !e.shiftKey){
		  		preventDefault = true;
		  		self.actions.download();
		  	}

		    // ctrl + L || cmd + L
		  	if ((e.ctrlKey || e.metaKey) && e.keyCode == 76 && !e.shiftKey){
		  		preventDefault = true;
		  		self.actions.seeLink();
		  	}

			// ctrl + v || cmd + v
		  	if ((e.ctrlKey || e.metaKey) && e.keyCode == 86 && !e.shiftKey){
		  		preventDefault = true;
		  		self.actions.paste();
		  	}
		    
		    // ctrl + c || cmd + c
		    if ((e.ctrlKey || e.metaKey) && e.keyCode == 67 && !e.shiftKey){
		    	preventDefault = true;
		    	self.actions.copy();
		    }

		    // ctrl + x || cmd + x
		    if ((e.ctrlKey || e.metaKey) && e.keyCode == 88 && !e.shiftKey){
		    	preventDefault = true;
		    	self.actions.copy(true);
		    }

		    // ctrl + R || cmd + R
		    if ((e.ctrlKey || e.metaKey) && e.keyCode == 82 && !e.shiftKey){
		    	preventDefault = true;
		    	self.actions.rename();
		    }

		    //delete
		    if (e.keyCode == 46 || e.keyCode == 8){
		    	preventDefault = true;
		    	self.actions.delete();
		    }

		    // ctrl + a || cmd + a
		    if ((e.ctrlKey || e.metaKey) && e.keyCode == 65 && !e.shiftKey) {
		    	preventDefault = true;
                self.wrapListFilesElement.find('figure').addClass('active');
		    }

		    if(preventDefault) e.preventDefault();

		});

		// resize window
		$( window ).on('resize', function() {
		  	self.setHeightMainWrap();
		});
		// show modal lưu ý
		$(document).on('click', '[modal-instruct]', function(e) {
			$("#modal-instruct").modal({
				showClose: true,
				closeExisting: false,
				escapeClose: false,
				clickClose: false
			});
		});	
		
		// chọn nhiều tệp khi cross domain
		if(
			typeof(cdnCrossDomain) != _UNDEFINED && parseInt(cdnCrossDomain) > 0 && 
			typeof(cdnMultiple) != _UNDEFINED && parseInt(cdnMultiple) > 0 &&
			typeof(cdnFieldId) != _UNDEFINED && cdnFieldId.length > 0
		){
			// check hoặc uncheck selectbox
			$(document).on('change', 'input[nh-input="select-file"]', function(e) {

				// ẩn hiển button select
				var showBtn = false;
				if(self.wrapListFilesElement.find('input[nh-input="select-file"]:checked').length > 0) showBtn = true;

				$('[nh-group="select"]').toggleClass('d-none', !showBtn);
			});

			$(document).on('click', '[nh-btn="selected"]', function(e) {
				var listUrl = [];

				$('input[nh-input="select-file"]:checked').each(function( index ) {
				  	var figureElement = $(this).closest('figure');
				  	if(figureElement.length == 0) return;

				  	var path = figureElement.data('path');
				  	var type = figureElement.data('type');

				  	if(typeof(path) == _UNDEFINED || path.length == 0) return;
				  	if(typeof(type) == _UNDEFINED || type.length == 0 || type == _FOLDER) return;

				  	listUrl.push(prefixUrlFile + path);
				});

				if(listUrl.length == 0) return;
				
				// gửi đường dẫn và field đã chọn sang website 
				parent.postMessage({
	                sender: 'myfilemanager',
	                url: listUrl,
	                field_id: cdnFieldId
	            }, "*");
			});

			$(document).on('click', '[nh-btn="select-all"]', function(e) {
				self.wrapListFilesElement.find('input[nh-input="select-file"]').prop('checked', true);
				self.wrapListFilesElement.find(`figure:not([data-type="${_FOLDER}"])`).addClass('checked');
			});

			$(document).on('click', '[nh-btn="unselect-all"]', function(e) {
				self.wrapListFilesElement.find('input[nh-input="select-file"]').prop('checked', false);

				self.wrapListFilesElement.find(`figure`).removeClass('checked');

				$('[nh-group="select"]').toggleClass('d-none', true);
			});
		}

		// kéo file vào wrapListFilesElement
		self.wrapListFilesElement.on('dragover', function(e) {
	        e.preventDefault();
	        e.stopPropagation();

	        $('.files-wrap').addClass('bg-dragenter');
	    });

	    self.wrapListFilesElement.on('dragleave', function(e) {
	    	e.preventDefault();
	        e.stopPropagation();

	        $('.files-wrap').removeClass('bg-dragenter');
	    });
		

		// thả file vào wrapListFilesElement
	    self.wrapListFilesElement.on('drop', function(e) { 
	    	e.preventDefault(); 
	    	e.stopPropagation(); 

	    	$('.files-wrap').removeClass('bg-dragenter');

	    	var files = e.originalEvent.dataTransfer.files;
	    	self.actions.upload(files);
	    });
	},
	actions: {
		download: function(){
			var self = fileManager;

			if(self.indexActiveItem == null) return;

			var figureElement = self.wrapListFilesElement.find(`li:eq(${self.indexActiveItem})`).find('figure');
			if(figureElement.length == 0) return;

			var file_url = figureElement.data('path');
			var filename = figureElement.data('name');

			if(file_url == null || typeof(file_url) == _UNDEFINED || file_url.length == 0) return;
			if(filename == null || typeof(filename) == _UNDEFINED || filename.length == 0) return;

		    const tmpLink = document.createElement('a');
		  	tmpLink.href = file_url;
		 	tmpLink.download = filename;
		  	document.body.appendChild(tmpLink);
		  	tmpLink.click();
		  	document.body.removeChild(tmpLink);
		},
		seeLink: function(){
			var self = fileManager;

			if(self.indexActiveItem == null) return;

			var figureElement = self.wrapListFilesElement.find(`li:eq(${self.indexActiveItem})`).find('figure');
			if(figureElement.length == 0) return;

			var file_url = figureElement.data('path');
			var filename = figureElement.data('name');

			if(file_url == null || typeof(file_url) == _UNDEFINED || file_url.length == 0) return;
			if(filename == null || typeof(filename) == _UNDEFINED || filename.length == 0) return;

		    // mở modal
			var typeModal = 'view-link';
			var options = {
				showClose: true,
				closeExisting: false,
  				escapeClose: false,
  				clickClose: false
			}
			self.modal.open(typeModal, {
				filename: filename,
				file_url: file_url
			}, options);

			var modalElement = $('body').find(`[nh-modal="${typeModal}"]`);
			if(modalElement.length == 0) return;

			
			modalElement.on('click', `[nh-btn="${typeModal}"]`, function(e) {
				modalElement.find(`input[nh-input="${typeModal}"]`).select();

				document.execCommand('copy');
				self.modal.close();
			});
		},
		copy: function(cut = false){
			var self = fileManager;
			
			if(self.wrapListFilesElement.find('figure.active').length == 0) return;

			var files = [];
			self.wrapListFilesElement.find('figure.active').each(function( index ) {
				files.push({
					filename: typeof($(this).data('name')) != _UNDEFINED ? $(this).data('name') : '',
					file_url: typeof($(this).data('path')) != _UNDEFINED ? $(this).data('path') : ''
				});
			});
			
			self.copy.files = files;
			self.copy.cut = Boolean(cut);
		},
		paste: function(){
			var self = fileManager;

			var currentPath = self.listFileParams.path;
			if(typeof(currentPath) == _UNDEFINED ||currentPath == null || currentPath.length == 0) return;
			if(typeof(self.copy.files) == _UNDEFINED || self.copy.files == null || self.copy.files.length == 0) return;
			
			var cut = typeof(self.copy.cut) != _UNDEFINED && Boolean((self.copy.cut)) == true ? 1 : 0;

			// kiểm tra nếu cut file vào cùng thư mục thì ngăn thực hiện
			if(cut){
				var firstFile = typeof(self.copy.files[0]) != _UNDEFINED ? self.copy.files[0] : {};
				var fileUrl = typeof(firstFile.file_url) != _UNDEFINED ? firstFile.file_url : '';
				var fileName = typeof(firstFile.filename) != _UNDEFINED ? firstFile.filename : '';

				var filePath = '';
				if(fileUrl.length > 0 && fileName.length > 0){
					filePath = fileUrl.replace('/' + fileName, '');
				}

				// ngăn thực hiện
				if(filePath == currentPath) return;
			}

			// mở modal
			var typeModal = 'paste';
			var options = {
				showClose: true,
				closeExisting: false,
  				escapeClose: false,
  				clickClose: false
			}
			var openModal = self.modal.open(typeModal, {
				files: self.copy.files
			}, options);

			var modalElement = $(`[nh-modal="${typeModal}"]`);
			if(modalElement.length == 0) return;
			
			// paste file
			var hasError = false;
			var numberFile = self.copy.files.length;
			var filePasted = 0;
			var hasError = false;
			$.each(self.copy.files, function(index, file) {
				var fileName = typeof(file.filename) != _UNDEFINED ? file.filename : '';
				var fileUrl = typeof(file.file_url) != _UNDEFINED ? file.file_url : '';


				nhMain.callAjax({
					async: true,
		            url: prefixCdnUrl + '/paste',
		            data: {
		            	file: fileUrl,
		            	path: currentPath,
		            	cut: cut
		            }
		        }).done(function(response) {
		            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		            var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		           	
		           	filePasted ++;

		           	// nếu response ko trả về json thì đang lỗi -> tiếp tục xử lý tệp tiếp theo
		            if(typeof(response) != 'object' || code != _SUCCESS) {
		            	// hiển thị log file bị lỗi
		            	if(modalElement.find('[nh-wrap-log]').length > 0){
		            		hasError = true;
		            		
		            		var logError = `<p class="text-error">${fileName}: ${message}</p>`;

		            		modalElement.find('[nh-wrap-log]').append(logError);
		            		modalElement.find('[nh-wrap-log]').removeClass('d-none');
		            	}
		            }else{ 	
		            	var fileInfo = typeof(data.file_info) != _UNDEFINED ? data.file_info : [];		            	
		            	var type = typeof(fileInfo.type) != _UNDEFINED ? fileInfo.type : null;
					  	var htmlFileItem = self.getHtmlFileItem(fileInfo, false);

					  	if(type == _FOLDER){
					  		var liElement = self.wrapListFilesElement.find(`li.${type}:last`);
					  	}else{
					  		var liElement = self.wrapListFilesElement.find(`li:last`);
					  	}

					  	if(liElement.length > 0){
					  		liElement.after(htmlFileItem);
					  	}else{
					  		self.wrapListFilesElement.append(htmlFileItem);
					  	}
					  	
					  	// cập nhật trên modal
				        if(modalElement.length > 0){
				        	// cập nhật số lượng file đã paste trên modal
				        	modalElement.find('span[nh-count-file]').text(filePasted);

				        	// hiển thị % progress bar trên modal
				        	if(modalElement.find('.progress-bar').length > 0){
				        		// nếu có lỗi xảy ra thì thêm class orange cho processbar
					        	if(hasError){
					        		modalElement.find('.progress-bar').addClass('orange');
					        	}

				        		var percent = (filePasted/numberFile) * 100;

				        		if(percent > 100 || percent < 0) percent = 100;
				        		modalElement.find('.progress-bar .progress-bar-value').css('width', `${percent}%`)
				        	}
				        }
		            }

		            // đóng modal nếu đã thực hiện hết và không có lỗi xảy ra
		            if(filePasted == numberFile){
		            	self.copy.files = [];
		            	if(!hasError){
		            		setTimeout(function(){
			        			self.modal.close();
			        		}, 500);
		            	}

		            }

		        });
			});
		},
		rename: function(){
			var self = fileManager;

			var figureElement = self.wrapListFilesElement.find('figure.active').first();
			if(figureElement.length == 0) return;

			var liElement = figureElement.closest('li');
		    if(liElement.length == 0) return;
			
			var file_url = figureElement.data('path');
			var filename = figureElement.data('name');
			var extension = figureElement.data('extension');
			var type = figureElement.data('type');

			if(file_url == null || typeof(file_url) == _UNDEFINED || file_url.length == 0) return;
			if(filename == null || typeof(filename) == _UNDEFINED || filename.length == 0) return;
			if(type == null || typeof(type) == _UNDEFINED || type.length == 0) return;

			var currentPath = self.listFileParams.path;
			
			// mở modal
			var typeModal = 'rename';
			var options = {
				showClose: true,
				closeExisting: false,
  				escapeClose: false,
  				clickClose: false
			}
			self.modal.open(typeModal, {
				filename: filename,
				type: type
			}, options);

			var modalElement = $('body').find(`[nh-modal="${typeModal}"]`);
			if(modalElement.length == 0) return;

			// thêm sự kiện click button save trên modal
			modalElement.on('click', `[nh-btn-save="${typeModal}"]`, function(e) {
				var inputElement = modalElement.find(`[nh-input="${typeModal}"]`);
				if(inputElement.length == 0) return;

				var nameChange = inputElement.val();
				// validate folder name
				if(nameChange.length == 0 || `${nameChange}.${extension}` == filename){
					inputElement.addClass('error');
					return;
				}

				if(type != _FOLDER) nameChange += '.' + filename.substr((filename.lastIndexOf('.') + 1));
				
				// đóng modal
				self.modal.close();

				nhMain.callAjax({
					async: true,
		            url: prefixCdnUrl + '/rename',
		            data: {
		            	name: nameChange,
		            	old_name: filename,
		            	path: currentPath,
		            	type: type == _FOLDER ? _FOLDER : _FILE
		            }
		        }).done(function(response) {
		            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		            var data = typeof(response.data) != _UNDEFINED ? response.data : {};

		            if(code == _SUCCESS){
		            	var fileInfo = {
		            		is_dir: type == _FOLDER ? true : false,
		            		type: type,
		            		path: typeof(data.path) != _UNDEFINED ? data.path : '',
		            		filename: typeof(data.name) != _UNDEFINED ? data.name : '',
		            		extension:  typeof(data.extension) != _UNDEFINED ? data.extension : '',
		            		size: typeof(data.size) != _UNDEFINED ? data.size : 0,
		            		time: typeof(data.time) != _UNDEFINED ? data.time : 0,
		            	}

		            	var htmlFileItem = self.getHtmlFileItem(fileInfo, false);		            	

		            	liElement.replaceWith(htmlFileItem);

		            	// clear select item
		            	self.wrapListFilesElement.find('figure').removeClass('active');
		            }else{
		            	$.toast({
						    text: message,
						    loader : false,
						    icon: 'error'
						});
		            }
		        });

			});

			modalElement.on('click', `[nh-input="${typeModal}"]`, function(e) {
				$(this).select();
			});
		},
		delete: function(){
			var self = fileManager;

			if(self.indexActiveItem == null) return;

			// lấy danh sách files đã chọn để xóa
			var files = [];
			self.wrapListFilesElement.find('figure.active').each(function( index ) {
				files.push({
					'filename': typeof($(this).data('name')) != _UNDEFINED ? $(this).data('name') : '',
					'file_url': typeof($(this).data('path')) != _UNDEFINED ? $(this).data('path') : ''
				});
			});

			if(typeof(files) == _UNDEFINED || files.length == 0) return;

			// mở modal
			var typeModal = 'delete-confirm';
			var options = {
				showClose: true,
				closeExisting: false,
  				escapeClose: false,
  				clickClose: false
			}
			self.modal.open(typeModal, {
				files: files
			}, options);

			var modalElement = $('body').find(`[nh-modal="${typeModal}"]`);
			if(modalElement.length == 0) return;

			// thêm sự kiện click button save trên modal			
			modalElement.on('click', `[nh-btn-confirm="${typeModal}"]`, function(e) {				

				// đóng modal xác nhận
				self.modal.close();

				var typeModalDelete = 'delete';
				var options = {
					showClose: true,
					closeExisting: false,
	  				escapeClose: false,
	  				clickClose: false
				}
				self.modal.open(typeModalDelete, {
					files: files
				}, options);

				var modalDeleteElement = $('body').find(`[nh-modal="${typeModalDelete}"]`);
				if(modalDeleteElement.length == 0) return;

				// xóa tệp
				var hasError = false;
				var numberFile = files.length;
				var fileDeleted = 0;
				var hasError = false;
				$.each(files, function(index, file) {
					var fileName = typeof(file.filename) != _UNDEFINED ? file.filename : '';
					var fileUrl = typeof(file.file_url) != _UNDEFINED ? file.file_url : '';					

					nhMain.callAjax({
						async: true,
			            url: prefixCdnUrl + '/delete',
			            data: {
			            	file: fileUrl
			            }
			        }).done(function(response) {
			            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
			            var data = typeof(response.data) != _UNDEFINED ? response.data : {};

			            fileDeleted ++;

			            // nếu response ko trả về json thì đang lỗi -> tiếp tục xử lý tệp tiếp theo
			            if(typeof response != 'object' || code != _SUCCESS) {
			            	hasError = true;

			            	// hiển thị log file bị lỗi			            	
			            	if(modalDeleteElement.find('[nh-wrap-log]').length > 0){
			            		hasError = true;
			            		var logError = `<p class="text-error">${fileName}: ${message}</p>`;

			            		modalDeleteElement.find('[nh-wrap-log]').append(logError);
			            		modalDeleteElement.find('[nh-wrap-log="delete"]').removeClass('d-none');
			            	}
			            }else{
			               	// xóa figure 
			            	var figureElement = self.wrapListFilesElement.find(`figure[data-path="${fileUrl}"]`);
			            	if(figureElement.length > 0){
			            		figureElement.closest('li').remove();
			            	}

			            	// cập nhật trên modal delete
					        if(modalDeleteElement.length > 0){
					        	// cập nhật số lượng file đã xóa trên modal
					        	modalDeleteElement.find('span[nh-count-file]').text(fileDeleted);

					        	// hiển thị % progress bar trên modal
					        	if(modalDeleteElement.find('.progress-bar').length > 0){
					        		var percent = (fileDeleted/numberFile) * 100;
					        		if(percent > 100 || percent < 0) percent = 100;
					        		modalDeleteElement.find('.progress-bar .progress-bar-value').css('width', `${percent}%`)
					        	}
					        }
			            }

			            // đóng modal nếu đã thực hiện hết và không có lỗi xảy ra
			        	if(fileDeleted == numberFile && !hasError) {
			        		setTimeout(function(){
			        			self.modal.close();
			        		}, 500);
			        	}
			        });
				});
			});
		},
		createFolder: function(){
			var self = fileManager;
			var currentPath = self.listFileParams.path;

			if(typeof(currentPath) == _UNDEFINED ||currentPath == null || currentPath.length == 0) currentPath = self.basePath;				

			// mở modal
			var typeModal = 'create-folder';
			var options = {
				showClose: true,
				closeExisting: false,
  				escapeClose: false,
  				clickClose: false
			}

			self.modal.open(typeModal, {}, options);

			var modalElement = $('body').find(`[nh-modal="${typeModal}"]`);
			if(modalElement.length == 0) return;
			
			// thêm sự kiện click button save trên modal
			modalElement.on('click', `[nh-btn-save="${typeModal}"]`, function(e) {
				var inputElement = modalElement.find(`[nh-input="${typeModal}"]`);

				if(inputElement.length == 0) return;

				var folderName = inputElement.val();
				// validate folder name
				if(folderName.length == 0){
					inputElement.addClass('error');
					return;
				}
				
				// đóng modal
				self.modal.close();	

				nhMain.callAjax({
					async: true,
		            url: prefixCdnUrl + '/create-folder',
		            data: {
		            	name: folderName,
		            	path: currentPath
		            }
		        }).done(function(response) {
		            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		            var data = typeof(response.data) != _UNDEFINED ? response.data : {};

		            if(code == _SUCCESS){
		            	var fileInfo = {
		            		is_dir: true,
		            		type: _FOLDER,
		            		path: typeof(data.path) != _UNDEFINED ? data.path : '',
		            		filename: typeof(data.name) != _UNDEFINED ? data.name : '',
		            		extension: _FOLDER,
		            		size: 0,
		            		time: typeof(data.time) != _UNDEFINED ? data.time : 0,
		            	}
		            	var htmlFileItem = self.getHtmlFileItem(fileInfo);

		            	// append new folder to list
		            	if(self.wrapListFilesElement.find('figure[data-type="folder"]').length == 0){
		            		self.wrapListFilesElement.prepend(htmlFileItem);
		            	}else{
		            		self.wrapListFilesElement.find('figure[data-type="folder"]:last').closest('li.folder').after(htmlFileItem);
		            	}

		            }else{
		            	$.toast({
						    text: message,
						    loader : false,
						    icon: 'error'
						});
		            }
		        });

			});			
		},
		upload: function(upload_files = []){
			var self = fileManager;

			var currentPath = self.listFileParams.path;
			if(typeof(currentPath) == _UNDEFINED ||currentPath == null || currentPath.length == 0) return;
			if($.isEmptyObject(upload_files)) return;

			var files = [];
			$.each(upload_files, function( index, file) {
			  	files.push({
			  		filename: typeof(file.name) != _UNDEFINED ? file.name : '',
					file_url: ''
			  	});
			});

			if(files.length == 0) return;

			var inputUploadElement = $('input[nh-input="select-upload-file"]');
			if(inputUploadElement.length == 0) return;

			var maxFileSize = inputUploadElement.data('max-file-size');
			var maxFileSizeLabel = nhMain.utilities.parseBytelabel(maxFileSize);
			
			// mở modal upload
			var typeModal = 'upload';
			var options = {
				showClose: true,
				closeExisting: false,
  				escapeClose: false,
  				clickClose: false
			}
			self.modal.open(typeModal, {
				files: files
			}, options);

			var modalElement = $(`[nh-modal="${typeModal}"]`);
			if(modalElement.length == 0) return;

			var hasError = false;
			var fileUploaded = 0;
			var numberFile = files.length;
			$.each(upload_files, function(index, file) {
				var filename = typeof(file.name) != _UNDEFINED ? file.name : '';
				var size = typeof(file.size) != _UNDEFINED ? parseInt(file.size) : 0;

				if(filename.length == 0 || size < 0) return;				

	        	// validate size file trước khi upload
	        	if(typeof(maxFileSize) != _UNDEFINED && size > parseInt(maxFileSize)){
	        		hasError = true;

	            	// hiển thị log file bị lỗi
	            	if(modalElement.find('[nh-wrap-log]').length > 0){
	            		var logError = `<p class="text-error">${filename}: ${nhMain.getLabel('dung_luong_tep_dang_tai_vuot_qua_gioi_han')}. ${maxFileSizeLabel}</p>`;

	            		modalElement.find('[nh-wrap-log]').append(logError);
	            		modalElement.find('[nh-wrap-log]').removeClass('d-none');
	            	}

	            	return;
	        	}
	        	
				var formData = new FormData();
				formData.append('file', file);
				formData.append('path', currentPath);

				nhMain.callAjax({
					async: true,
		            url: prefixCdnUrl + '/upload',
		            data: formData,
		            contentType: false,
					processData: false,
		        }).done(function(response) {
		            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		            var data = typeof(response.data) != _UNDEFINED ? response.data : {};

		            fileUploaded ++;		

		            // hiển thị tên file đã upload
					modalElement.find('[nh-file-upload]').text(filename);

					// cập nhật số lượng file đã upload trên modal
		        	modalElement.find('span[nh-count-file]').text(fileUploaded);

		        	// hiển thị % progress bar trên modal
		        	if(modalElement.find('.progress-bar').length > 0){
		        		// nếu có lỗi xảy ra thì thêm class orange cho processbar

			        	if(hasError){
			        		modalElement.find('.progress-bar').addClass('orange');
			        	}

		        		var percent = (fileUploaded/numberFile) * 100;

		        		if(percent > 100 || percent < 0) percent = 100;
		        		modalElement.find('.progress-bar .progress-bar-value').css('width', `${percent}%`)
		        	}

		            // nếu response ko trả về json thì đang lỗi -> tiếp tục xử lý tệp tiếp theo
		            if(nhMain.utilities.isJson(response) || code != _SUCCESS) {
		            	hasError = true;

		            	// hiển thị log file bị lỗi
		            	if(modalElement.find('[nh-wrap-log]').length > 0){
		            		var logError = `<p class="text-error"><b>${filename}</b>: ${message}</p>`;
		            		modalElement.find('[nh-wrap-log]').append(logError);
		            		modalElement.find('[nh-wrap-log]').removeClass('d-none');
		            	}
		            }else{
		            	var fileInfo = typeof(data.file_info) != _UNDEFINED ? data.file_info : [];
		            	var type = typeof(fileInfo.type) != _UNDEFINED ? fileInfo.type : null;
					  	var htmlFileItem = self.getHtmlFileItem(fileInfo, false);

					  	if(type == _FOLDER){
					  		var liElement = self.wrapListFilesElement.find(`li.${type}:last`);
					  	}else{
					  		var liElement = self.wrapListFilesElement.find(`li:last`);
					  	}

					  	if(liElement.length > 0){
					  		liElement.after(htmlFileItem);
					  	}else{
					  		self.wrapListFilesElement.append(htmlFileItem);
					  	}
		            }


		        	// đóng modal nếu đã thực hiện hết và không có lỗi xảy ra
		        	if(fileUploaded == numberFile && !hasError) {
		        		setTimeout(function(){
		        			self.modal.close();
		        		}, 500);
		        	}
		        });
			});
			
		}
	},
	myLocalStorage: {
		localStorageEnabled: null,
		checkEnabled: function(){
			var self = this;

			try {
				var keyTest = '__localStorage__';
		        localStorage.setItem(keyTest, true);
		        localStorage.removeItem(keyTest);

		        return true;
		    } catch(e) {
		        return false;
		    }
		},
		setItem: function(key = null, value = null){
			var self = this;
			if(!self.checkEnabled() || key == null || typeof(key) == _UNDEFINED || key.length == 0) return;

			localStorage.setItem(key, value);			

		},
		removeItem: function(key = null){
			var self = this;
			if(!self.checkEnabled() || key == null || typeof(key) == _UNDEFINED || key.length == 0) return;

			localStorage.removeItem(key);
		},
		getItem: function(key = null){
			var self = this;
			if(!self.checkEnabled() || key == null || typeof(key) == _UNDEFINED || key.length == 0) return '';

			return localStorage.getItem(key);
		},
		clear: function(){
			var self = this;
			if(!self.checkEnabled()) return;

			localStorage.clear();
		}
	},
	setHeightMainWrap: function(){
		var self = this;

		var screenHeight = $(window).height();
		var navbarHeight = $('.navbar').height() + 10; // + 10px margin-bottom

		// set chiều cao main container
		var height = screenHeight - navbarHeight;

		$('.navigation-container').height(height);
		$('.detail-container').height(height);
		$('.files-container').height(height); 
		
	},
	loadListFiles: function(init = false){
		var self = this;

		if(self.wrapListFilesElement.length == 0) return;

		if(init){
			self.wrapListFilesElement.attr('data-page', 0);
			self.wrapListFilesElement.attr('data-loading', 0);
			self.wrapListFilesElement.attr('data-next', 0);

			self.listFileParams.page = 1;
			self.wrapListFilesElement.html('');
		}

		var loadingData = self.wrapListFilesElement.attr('data-loading');
		var nextPage = self.wrapListFilesElement.attr('data-next');
		if(typeof(loadingData) == _UNDEFINED || parseInt(loadingData) > 0) return;
		if(typeof(loadingData) == _UNDEFINED || (parseInt(nextPage) == 0 && !init)) return;

		//show loading
		self.loading.show(self.wrapListFilesElement);

		// cập nhật thuộc tính loading của wrap
		self.wrapListFilesElement.attr('data-loading', 1);

		nhMain.callAjax({
			async: true,
            url: prefixCdnUrl + '/files',
            data: self.listFileParams
        }).done(function(response) {

            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
            var data = typeof(response.data) != _UNDEFINED ? response.data : {};

            var files = typeof(data.files) != _UNDEFINED ? data.files : {};
            var pagination = typeof(data.pagination) != _UNDEFINED ? data.pagination : {};
            var extend = typeof(data.extend) != _UNDEFINED ? data.extend : {};

            if (code == _SUCCESS) {
            	var page = typeof(pagination.page) != _UNDEFINED ? pagination.page : 1;
            	var next = typeof(pagination.next) != _UNDEFINED ? Boolean(pagination.next) : false;
            	var isRoot = typeof(extend.is_root) != _UNDEFINED ? Boolean(extend.is_root) : false;
            	var numberFile = typeof(extend.number_file) != _UNDEFINED ? parseInt(extend.number_file) : 0;
            	var numberFolder = typeof(extend.number_folder) != _UNDEFINED ? parseInt(extend.number_folder) : 0;
            	var folders = typeof(extend.folders) != _UNDEFINED ? extend.folders : [];

            	// set attribute pagination
            	self.wrapListFilesElement.attr('data-page', page);
            	self.wrapListFilesElement.attr('data-next', next ? 1 : 0);

            	// clear html panel list file ở trang 1
            	if(page == 1){            		
            		self.wrapListFilesElement.html('');            		
            	}

            	if(!$.isEmptyObject(files)){
            		// load ds files
	            	$.each(files, function( index, file) {
	            		var htmlItem = self.getHtmlFileItem(file);
			            self.wrapListFilesElement.append(htmlItem);
			        });

	            	// khởi tạo thư viện cho các item file
			        self.initLibForListItems();
            	}

            	if(page == 1){
            		// load breadcrum ở trang 1
            		self.loadBreadcrumb(numberFile, numberFolder);

            		// nếu ở trang 1 thì active item đầu tiên
            		var firstFigureElement = self.wrapListFilesElement.find('li:first-child figure');
            		firstFigureElement.addClass('active');
            		self.showInfoFile(firstFigureElement);
            	}

				// cập nhật thuộc tính loading của wrap
				self.wrapListFilesElement.attr('data-loading', 0);

				//close loading
				self.loading.hide(self.wrapListFilesElement);

				// set localStore path khi load thành công ds 
				self.myLocalStorage.setItem('currentPath', self.listFileParams.path);

				// load navigation
            	self.loadNavigation();
            }else{

            	// xóa local Storage
            	self.myLocalStorage.clear();

            	// hiển thị thông báo lỗi
            	$.toast({
				    text: message,
				    loader : false,
				    icon: 'error'
				});

            }

        });
	},
	getHtmlFileItem: function(file = {}, lazy = true){
		var self = this;

		if(typeof(file) != 'object' || $.isEmptyObject(file)) return '';

		var isDir = typeof(file.is_dir) != _UNDEFINED ? Boolean(file.is_dir) : false;
		var type = typeof(file.type) != _UNDEFINED && file.type != null ? file.type : '';
		var path = typeof(file.path) != _UNDEFINED && file.path != null ? file.path : '';
		var filename = typeof(file.filename) != _UNDEFINED && file.filename != null ? file.filename : '';
		var extension = typeof(file.extension) != _UNDEFINED && file.extension != null ? file.extension : '';
		var basename = filename.split('.').shift();
		var size = typeof(file.size) != _UNDEFINED ? parseInt(file.size): 0;
		var time = typeof(file.time) != _UNDEFINED ? parseInt(file.time) : '';
		
		if(filename.length == 0 || extension.length == 0) return '';

		// hiển thị icon theo loại tệp (nếu là ảnh thì hiển thị ảnh)
		var urlImgPreview = self.getIconFile(type, extension);
		var imgPreview = `<img src="${urlImgPreview}">`;
		
		if(type == _IMAGE && lazy) imgPreview = `<img nh-lazy="image" data-src="${prefixUrlFile}${nhMain.utilities.getThumbs(path, 150)}" src="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAoMBgDTD2qgAAAAASUVORK5CYII=">`;
		if(type == _IMAGE && !lazy) imgPreview = `<img src="${prefixUrlFile}${nhMain.utilities.getThumbs(path, 150)}">`;

		var classLi = type;
		var actionTemplate = '';

		var selectMultipleHtml = '';
		if(
			typeof(cdnCrossDomain) != _UNDEFINED && parseInt(cdnCrossDomain) > 0 && 
			typeof(cdnMultiple) != _UNDEFINED && parseInt(cdnMultiple) > 0 &&
			typeof(cdnFieldId) != _UNDEFINED && cdnFieldId.length > 0
		){
			selectMultipleHtml = `
				<label class="cont">
				    <input nh-input="select-file" type="checkbox" class="selection" />
				    <span class="checkmark"></span>
				</label>`;
		}		

		var badgeExtensionHtml = type == _FOLDER ? `<span class="extension">${extension}</span>` : `<span class="extension">.${extension}</span>`;

		var itemTemplate = `
			<li class="${classLi}">
				<figure data-name="${filename}" data-extension="${extension}" data-type="${type}" data-path="${path}" data-time="${time}" data-size="${size}">
					${selectMultipleHtml}
					<div class="img-title">
						<div class="img-precontainer">
							${imgPreview}
						</div>

						<div class="title">
							<h4>${basename}</h4>
						</div>
						${badgeExtensionHtml}
					</div>

					<div class="size-time">
						<div class="size">
							${nhMain.utilities.parseBytelabel(size)}
						</div>

						<div class="time">
							${nhMain.utilities.parseIntToDateTimeString(time)}
						</div>
					</div>
				</figure>
			</li>`;

		return itemTemplate;		
	},
	getIconFile: function(type = null, extension = null){
		var self = this;

		var prefixImageUrl = `${prefixCdnUrl}/assests/img`;
		var iconDefault = `${prefixImageUrl}/icons-files/unknown.svg`;
		if(type == null || extension == null || type.length == 0 || extension.length == 0) return iconDefault;		

		// danh sách extension có thể hiển thị icon
		var listExtension = [
			'7z', 'ae', 'avi', 'bin', 'cdr', 'css', 'dll', 'doc', 'docx', 'dw', 'dwg', 'eml', 'eps', 'epub', 
			'gif', 'html', 'ico', 'indd', 'iso', 'jar', 'jpeg', 'jpg', 'js', 'm3u', 'max', 'mkv', 'mov', 
			'mp3', 'mp4', 'mpeg', 'obj', 'odt', 'pdf', 'ppt', 'pr', 'psd', 'py', 'rar', 'raw', 'rtf', 
			'svg', 'swf', 'tiff', 'txt', 'wav', 'wmv', 'xls', 'xlsx', 'zip'
		];

		// trả về icon extension
		var icon = '';
		if($.inArray(extension, listExtension) != -1) icon = `${prefixImageUrl}/icons-extension/${extension}.svg`;
		if(icon.length > 0) return icon;

		// nếu không có icon theo extension thì hiển thị icon theo type
		icon = `${prefixImageUrl}/icons-type-files/${type}.svg`;
		return icon;
	},
	initLibForListItems: function () {
		var self = this;
		
		// lazy image
		$('.files-wrap').find('img[nh-lazy="image"][data-src]:not(.loaded)').lazy({
            scrollDirection: 'vertical',
            effect: 'fadeIn',
            visibleOnly: true,
            appendScroll: $('.files-wrap')
        });
	},
	loadBreadcrumb: function(number_file = 0, nuber_folder = 0){
		var self = this;

		if(self.wrapBreadcrumbElement.length == 0) return;

		var currentPath = self.listFileParams.path;
		if(typeof(currentPath) == _UNDEFINED ||currentPath == null || currentPath.length == 0) currentPath = self.basePath;

		var splitPath = currentPath.split('/');
		if(splitPath.length < 2) return	
		var items = splitPath.slice(2, splitPath.length);

		var itemsHtml = '';
		var path = self.basePath;
		$.each(items, function(index, path_item) {
			path += '/' + path_item;

			if(index != items.length - 1){
				itemsHtml += `
		    	<li>
		    		<span nh-btn="redirect" data-path="${path}">
		    			${path_item}
		    		</span>
		    	</li>`;
			}else{
				itemsHtml += `
		    	<li class="active">
		    		<span>
		    			${path_item}
		    		</span>
		    	</li>`;
			}

		});

		var itemCountHtml = `<li><small>(${nuber_folder} ${nhMain.getLabel('thu_muc')} - ${number_file} ${nhMain.getLabel('tep_tin')})</small></li>`;

		var breacrumbHtml = `
			<ul>
				<li>
					<span nh-btn="redirect" data-path="/media">
						${nhMain.getLabel('tep_cua_toi')}
					</span>
				</li>
				${itemsHtml}
				${itemCountHtml}
			</ul>`;
		self.wrapBreadcrumbElement.html(breacrumbHtml);
	},	
	loadNavigation: function(){
		var self = this;

		if(self.wrapNavigationElement.length == 0) return;

		var currentPath = self.listFileParams.path;
		if(typeof(currentPath) == _UNDEFINED || currentPath == null || currentPath.length == 0) currentPath = self.basePath;
		if(currentPath.indexOf(self.basePath) == -1) return;

		//show loading
		self.loading.show(self.wrapNavigationElement);
		
		// nếu trên navigation chưa có item của currentPath thì load lại tất cả
		var reLoad = false;
		if(self.wrapNavigationElement.find(`[nh-btn="redirect"][data-path="${currentPath}"]`).length == 0) reLoad = true;		

		if(reLoad){
			// cắt currentPath theo từng cấp để load navigation theo từng cấp thư mục
			var subPath = currentPath.substring(self.basePath.length, currentPath.length);
			var splitPath = subPath.split('/');
			splitPath = splitPath.filter(Boolean);
			splitPath.unshift(self.basePath.substring(1, self.basePath.length));
			
			var pathNavigation = '';
			$.each(splitPath, function(index, value) {
			  	pathNavigation += '/' + value;			  
			  	self.getDataNavigation(pathNavigation, function(data){
					self.appendNavigation(data, pathNavigation);					
				});
			});
		}else{
			self.getDataNavigation(currentPath, function(data){
				self.appendNavigation(data, currentPath);
			});
		}

		//close loading
		self.loading.hide(self.wrapNavigationElement);

		// active navigation currentPath
		self.wrapNavigationElement.find('.navigation-folder .title').removeClass('active');
		self.wrapNavigationElement.find(`[nh-btn="redirect"][data-path="${currentPath}"]`).addClass('active');			

	},
	getDataNavigation: function(path = null, callback = null){
		var self = this;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

		if(path == null || typeof(path) == _UNDEFINED || path.indexOf(self.basePath) == -1) {
			callback(data);
			return;
		}

		nhMain.callAjax({
			async: false,
            url: prefixCdnUrl + '/navigation',
            data: {
            	path: path
            }
        }).done(function(response) {
        	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        var data = typeof(response.data) != _UNDEFINED ? response.data : {};

	        if(code == _SUCCESS){
	        	callback(data);	        	
	        }
        });
	},
	appendNavigation: function(folders = [], path = null){
		var self = this;
		var currentPath = self.myLocalStorage.getItem('currentPath');
					
		if(typeof(path) == _UNDEFINED || path == null || path.length == 0) path = self.basePath;
		if(path.indexOf(self.basePath) == -1) return;

		var itemsHtml = '';
		$.each(folders, function(index, folder_item) {
			var path = typeof(folder_item.path) != _UNDEFINED ? folder_item.path : '';
			var folderName = typeof(folder_item.folder) != _UNDEFINED ? folder_item.folder : '';
			var hasChildFolder = typeof(folder_item.has_child_folder) != _UNDEFINED ? Boolean(folder_item.has_child_folder) : false;
			
			var arrowHtml = ``;
			var activeNavigation = ``;

			if(hasChildFolder){
				arrowHtml = `<i class="arrow arrow-child-folder"></i>`;
			}

			if(path == currentPath){
				activeNavigation = `active`;
			}

			itemsHtml += `
		    	<li>
		    		<span class="title ${activeNavigation}" nh-btn="redirect" data-path="${path}">
		    			${arrowHtml}
		    			<img src="${prefixCdnUrl}/assests/img/icons-type-files/folder.svg">
		    			<span>
		    				${folderName}
		    			</span>
		    		</span>
		    		
		    	</li>`;
		});

		itemsHtml += `</li>`;

		var isRoot = path == self.basePath ? true : false;
		var navigationHtml = ``;
		
		if(isRoot){
			self.wrapNavigationElement.html('');
			navigationHtml = `
				<span nh-btn="redirect" class="font-weight-bold" data-path="/media">
	    			<img src="${prefixCdnUrl}/assests/img/icons-type-files/folder.svg">
	    			${nhMain.getLabel('tep_cua_toi')}
	    		</span>
				<ul class="navigation-folder">
					${itemsHtml}
				</ul>`;

			self.wrapNavigationElement.html(navigationHtml);
		}else{
			navigationHtml = `
				<ul class="navigation-folder">
					${itemsHtml}
				</ul>`;

			var itemActive = self.wrapNavigationElement.find(`[nh-btn="redirect"][data-path="${path}"]`).closest('li');
			if(itemActive.length == 0) return;

			// xóa ds cũ và thêm ds mới
			itemActive.find('ul.navigation-folder').remove('ul');
			itemActive.append(navigationHtml);
			itemActive.addClass('child-open');
		}

	},
	showPanelInfo: function(){
		var self = this;

		if(self.wrapInfoElement.length == 0) return;
		
		$('.detail-container').toggleClass('d-none', !self.showInfo);
		self.wrapMainElement.find('.files-container').toggleClass('files-container-full', !self.showInfo);
	},
	showViewList: function(){
		var self = this;

		if(self.wrapListFilesElement.length == 0) return;

		var view = self.viewList;		
		if(view == null || typeof(view) == _UNDEFINED || !$.inArray(view, ['gird', 'list'])) view = 'gird';
		
		switch(view){
			case 'gird':
				self.wrapListFilesElement.addClass('list-view-0').removeClass('list-view-1');
				$('.files-wrap').addClass('view-gird').removeClass('view-list');
				self.wrapMainElement.find('[nh-wrap="sort-column"]').removeClass('d-block');
			break;

		case 'list':
				self.wrapListFilesElement.addClass('list-view-1').removeClass('list-view-0');
				$('.files-wrap').addClass('view-list').removeClass('view-gird');
				self.wrapMainElement.find('[nh-wrap="sort-column"]').addClass('d-block');
			break;
		}
	},
	showFilterTypeFile: function(){
		var self = this;

		// website nhúng iframe cdn có truyền lọc theo loại tệp
		if(typeof(cdnTypeFile) != _UNDEFINED && $.inArray(cdnTypeFile, [_IMAGE, _VIDEO, _AUDIO, _DOCUMENT, _ARCHIVE]) > -1){
			self.listFileParams.filter_type = cdnTypeFile;

			// ẩn các button lọc theo loại
			$('[nh-btn="filter"]').addClass('d-none');
			$('[nh-btn="clear-filter"]').addClass('d-none');
		}
	},
	showListLog: function(){
		var self = this;

		// mở modal
		var typeModal = 'logs';
		var options = {
			showClose: true,
			closeExisting: false,
			escapeClose: false,
			clickClose: false
		}
		var openModal = self.modal.open(typeModal, {}, options);

		var modalElement = $(`[nh-modal="${typeModal}"]`);
		if(modalElement.length == 0) return;

		var tableElement = modalElement.find('[nh-table="logs"]');
		if(tableElement.length == 0) return;

		// init lib
		var languageDatepicker = null;
		if(typeof(cdnLanguage) != _UNDEFINED && $.inArray(cdnLanguage, ['vi', 'ko', 'ja', 'zh'])) languageDatepicker = cdnLanguage;

		$('[nh-datepicker]').flatpickr({
			dateFormat: 'd/m/Y',
			locale: languageDatepicker
		});

		self.clearLogParams();
		self.loadListLog(tableElement);
	},
	loadListLog: function(tableElement = null, loadMore = false){
		var self = this;

		if(tableElement == null || typeof(tableElement) == _UNDEFINED || tableElement.length == 0) return;

		nhMain.callAjax({
			async: false,
            url: prefixCdnUrl + '/list-logs',
            data: self.listLogParams
        }).done(function(response) {

            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
            var data = typeof(response.data) != _UNDEFINED ? response.data : {};
 
            var meta = typeof(response.meta) != _UNDEFINED ? response.meta : {};
            var pagination = typeof(meta.pagination) != _UNDEFINED ? meta.pagination : {};
            var tbodyElement = tableElement.find('tbody');

            if(!loadMore){
				tbodyElement.html('');
            }

            if (code == _SUCCESS) {
            	var page = typeof(pagination.page) != _UNDEFINED ? pagination.page : 1;
            	var limit = typeof(pagination.limit) != _UNDEFINED ? pagination.limit : null;
      			var file = typeof(pagination.file) != _UNDEFINED ? pagination.file : null;

      			// xóa button load more
      			tbodyElement.find('.load-more').remove();

            	// load ds logs
            	$.each(data, function(index, log) {
            		var fullname = typeof(log.auth_fullname) != _UNDEFINED ? log.auth_fullname : '';
            		var time = typeof(log.time) != _UNDEFINED ? log.time : '';
            		var action = typeof(log.action) != _UNDEFINED ? log.action : '';
            		var pathFrom = typeof(log.path_from) != _UNDEFINED ? log.path_from : '';
            		var pathTo = typeof(log.path_to) != _UNDEFINED ? log.path_to : '';
            		var message = typeof(log.message) != _UNDEFINED ? log.message : '';

		    		var htmlAction = ``;
            		var color = ``;
            		switch(action){
            			case  'RENAME':
            				color = `blue`;
            				htmlAction = `<span class="${color} font-weight-bold">${nhMain.getLabel('sua_ten')}</span>`;
            			break;

            			case  'UPLOAD':
            				color = `green`;
            				htmlAction = `<span class="${color} font-weight-bold">${nhMain.getLabel('tai_len')}</span>`;
            			break;

            		case  'CUT':
            				color = `red`;
            				htmlAction = `<span class="${color} font-weight-bold">${nhMain.getLabel('cat')}</span>`;
            			break;

            			case  'DELETE':
            				color = `red`;
            				htmlAction = `<span class="${color} font-weight-bold">${nhMain.getLabel('xoa')}</span>`;
            			break;

            			case  'CREATE_FOLDER':
            				color = `green`;
            				htmlAction = `<span class="${color} font-weight-bold">${nhMain.getLabel('tao_thu_muc_moi')}</span>`;
            			break;

            			case  'COPY':
            				color = `blue`;
            				htmlAction = `<span class="${color} font-weight-bold">${nhMain.getLabel('sao_chep')}</span>`;
            			break;
            		}
            		
            		// thêm span vào cuối path
					var splitPathFrom = pathFrom.split('/');
					splitPathFrom[splitPathFrom.length - 1] = `<span class="${color}">${splitPathFrom.at(-1)}</span>`;
					
					pathFrom = splitPathFrom.join('/');

					// thêm span vào cuối path
					if(pathTo){
						var splitPathTo = pathTo.split('/');
						splitPathTo[splitPathTo.length - 1] = `<span class="${color}">${splitPathTo.at(-1)}</span>`;
						
						pathTo = splitPathTo.join('/');
					}

            		var htmlMessage = message;

            		htmlMessage = htmlMessage.replace('_INFLUENCER_', `<span class="user font-weight-bold">${fullname}</span>`);
            		htmlMessage = htmlMessage.replace('_PATH_FROM_', `<span class="log-message from">${pathFrom}</span>`);
            		htmlMessage = htmlMessage.replace('_PATH_TO_', `<span class="log-message to">${pathTo}</span>`);
            		var htmlLog = `
            			<tr>
            				<td>
								${htmlAction}
            				</td>
            				<td class="font-italic">
            					${nhMain.utilities.parseIntToDateTimeString(time)}
            				</td>
            				<td>${htmlMessage}</td>
            			</tr>`;
            			tbodyElement.append(htmlLog);
		        });

		        // thêm button load more khi dữ liệu trang load đủ
		        if(limit != null && parseInt(limit) == data.length){
		        	var page = typeof(self.listLogParams.page) != _UNDEFINED ? parseInt(self.listLogParams.page) + 1 : 1;
			        var htmlBtnLoadMore = `
				        <tr class="load-more">
		    				<td colspan="4">
		    					<span class="btn btn-primary d-inline-block" nh-btn="log-load-more" data-page="${page}" data-file="${file}">${nhMain.getLabel('xem_them')}</span>
		    				</td>
		    			</tr>`;
		            tbodyElement.append(htmlBtnLoadMore);
		        }
		        
            }
        });
	},
	clearLogParams: function(){
		var self = this;

		self.listLogParams = {
			page: 1,
			limit: null,

			filter_action: '',
			filter_date_from: '',
			filter_date_to: '',
			filter_fullname: ''
		}
	},
	showInfoFile: function(figureElement = null){
		var self = this;

		if(figureElement == null || figureElement.length == 0 || self.wrapInfoElement.length == 0) {
			self.showInfoFileEmpty();
			return;
		}

		if(typeof(self.showInfo) == _UNDEFINED || !Boolean(self.showInfo)) {
			self.showInfoFileEmpty();
			return;
		}
	
		var name = figureElement.data('name');
		var type = figureElement.data('type');
		var path = figureElement.data('path');
		var time = figureElement.data('time');
		var size = figureElement.data('size');
		var extension = figureElement.data('extension');
		
		if(typeof(type) == _UNDEFINED || typeof(path) == _UNDEFINED || typeof(time) == _UNDEFINED || typeof(size) == _UNDEFINED || typeof(extension) == _UNDEFINED) {
			self.showInfoFileEmpty();
			return;
		}
	
		// lấy icon theo loại tệp (nếu là ảnh thì hiển thị ảnh)
		var urlImgPreview = self.getIconFile(type, extension);

		var imgPreview = `<img src="${urlImgPreview}">`;
		if(type == _IMAGE) imgPreview = `<img src="${prefixUrlFile}${urlImgPreview}">`;
		

		// nếu ảnh trên 2Mb thì show ảnh thumb thay vì ảnh gốc
		var srcImage = path;
		if(size > 1048576) srcImage = nhMain.utilities.getThumbs(path, 500);
		if(type == _IMAGE) imgPreview = `<img src="${prefixUrlFile}${srcImage}">`;

		var typeLabel = `${nhMain.getLabel('tep_tin')}`;
		switch(type){
			case _FOLDER:
				typeLabel = `${nhMain.getLabel('thu_muc')}`;
			break;

			case _IMAGE:
				typeLabel = `${nhMain.getLabel('hinh_anh')}`;
			break;				

			case _AUDIO:
				typeLabel = `${nhMain.getLabel('am_thanh')}`;
			break;

			case _ARCHIVE:
				typeLabel = `${nhMain.getLabel('tep_nen')}`;
			break;

			case _VIDEO:
				typeLabel = 'Video';
			break;
		}

		// html panel
		var htmlButtonImage = ``;
		
		if(type == _IMAGE){
			htmlButtonImage = `
			<a href="${path}" target="_blank" class="btn btn-main btn-nav-top btn-view-img" nh-btn="view-image-detai" data-tooltip="${nhMain.getLabel('xem_chi_tiet')}">
				<img src="${prefixCdnUrl}/assests/img/icon-navbar/Redo.svg"/>
			</a>`;
		}

		var htmlButtonDownload = ``;
		if(type != _FOLDER){
			htmlButtonDownload = `
				<span class="btn btn-main btn-nav-top" nh-btn="download-detai" data-tooltip="${nhMain.getLabel('tai_xuong')}">
					<img src="${prefixCdnUrl}/assests/img/icon-navbar/Download.svg"/ >
				</span>`;
		}

		var htmlInfo = `
			<div class="title-detail-filemanager">
				<span class="name font-weight-bold">${name}</span>
				<span class="info-download-image">				
					${htmlButtonImage}
					${htmlButtonDownload}
				</span>
			</div>

			<div class="detail-info-content">
				<div class="img-detail-filemanager">
					${imgPreview}
				</div>

					<div class="info-detail-filemanager">
						<p>
							<span>${nhMain.getLabel('ten_tep')}</span>: ${name}
						</p>
						<p>
							<span>${nhMain.getLabel('loai')}</span>: ${typeLabel}
						</p>

						<p>
							<span>${nhMain.getLabel('kich_thuoc')}</span>: ${nhMain.utilities.parseBytelabel(size)}
						</p>

					<p>
						<span>${nhMain.getLabel('ngay_cap_nhat')}</span>: ${nhMain.utilities.parseIntToDateTimeString(time)}
					</p>
				</div>
			</div>`;

		self.wrapInfoElement.html(htmlInfo);

	},
	showInfoFileEmpty: function(){
		var self = this;

		if(self.wrapInfoElement.length == 0) return;

		var htmlInfo = `
			<div class="title-detail-filemanager">
				<span class="name font-weight-bold">...</span>
			</div>

			<div class="detail-info-content">
				<div class="img-detail-filemanager"></div>

					<div class="info-detail-filemanager">
						<p>
							<span>${nhMain.getLabel('ten_tep')}</span>: ...
						</p>
						<p>
							<span>${nhMain.getLabel('loai')}</span>: ...
						</p>

						<p>
							<span>${nhMain.getLabel('kich_thuoc')}</span>: ...
						</p>

					<p>
						<span>${nhMain.getLabel('ngay_cap_nhat')}</span>: ...
					</p>
				</div>
			</div>`;

		self.wrapInfoElement.html(htmlInfo);

		return;

	},
	loading: {		
		show: function(wrapElement = null){
			var self = this;

			if(wrapElement == null || typeof(wrapElement) == _UNDEFINED || wrapElement.length == 0) return;
			var htmlLoading = `
				<div nh-loading class="loading-page">
		    		<span class="loader"></span>
		    	</div>`;
			wrapElement.append(htmlLoading);			
		},		
		hide: function(wrapElement = null){
			var self = this;

			if(wrapElement == null || typeof(wrapElement) == _UNDEFINED || wrapElement.length == 0) wrapElement = $('body');
			wrapElement.find('[nh-loading]').remove();
		}
	},
	modal: {
		open: function(type_modal = null, params = {}, options = {}){
			var self = this;

			$('body').find('.modal-action').remove();
			$('body').append(self._getHtmlModal(type_modal, params));

			$(`[nh-modal="${type_modal}"]`).modal(options);
		},
		close: function(){
			var self = this;

			// đóng và xóa modal
			$.modal.close();
			$('body').find('.modal-action').remove();
		},
		_getHtmlModal: function(type_modal = null, params = {}){
			var self = this;

			var listType = [
				'create-folder',
				'rename',
				'delete',
				'delete-confirm',
				'upload',
				'paste',
				'logs',
				'view-link'
			];

			if(type_modal == null || typeof(type_modal) == _UNDEFINED || type_modal.length == 0 || $.inArray(type_modal, listType) == -1) return '';

			var filename = typeof(params.filename) != _UNDEFINED ? params.filename : '';
			var file_url = typeof(params.file_url) != _UNDEFINED ? params.file_url : '';
			var type = typeof(params.type) != _UNDEFINED ? params.type : '';
			var files = typeof(params.files) != _UNDEFINED ? params.files : [];

			var bodyHtml = '';

			var classModal = '';
			switch(type_modal){
				case 'create-folder':
					var title = nhMain.getLabel('tao_thu_muc_moi');
					bodyHtml = `
						<div class="info-modal">
							<div class="info-name-action">
								<div class="name">
									<input nh-input="${type_modal}" value="" placeholder="${nhMain.getLabel('ten_thu_muc')}" maxlength="200" />
								</div>
								<div class="btn-action">
									<span nh-btn-save="${type_modal}" shortcut="13" class="btn btn-primary ml-10">
										${nhMain.getLabel('them')}
									</span>
								</div>
							</div>
							<div class="note">
								<i class="text-danger">
									<span>
										${nhMain.getLabel('luu_y')}:
									</span>
									${nhMain.getLabel('ten_thu_muc_se_duoc_dinh_dang_lai_loai_bo_dau_va_ky_tu_dac_biet')}
								</i>
							</div>
						</div>
						`;
				break;

				case 'rename':
					var title = nhMain.getLabel('nhap_ten_moi');

					var extension = filename.length > 0 ? filename.substr((filename.lastIndexOf('.') + 1)) : '';
					var basename = filename.length > 0 ? filename.substr(0, filename.lastIndexOf('.')) : '';
					var inputHtml = '';
					if(type == _FOLDER){
						inputHtml = `<input nh-input="${type_modal}" value="${filename}" placeholder="${nhMain.getLabel('ten_thu_muc')}" />`;						
					}else{
						inputHtml = `
							<div class="form-group">
								<input nh-input="${type_modal}" value="${basename}" placeholder="${nhMain.getLabel('ten_tep')}" />
								<div class="input-group-append">
		                            ${extension}
		                        </div>
							</div>`;
					}

					bodyHtml = `
						<div class="info-modal">
							<div class="info-name-action">
								<div class="form">
									${inputHtml}
								</div>
								<div class="btn-action">
									<span nh-btn-save="${type_modal}" shortcut="13" class="btn btn-primary ml-10">
										${nhMain.getLabel('doi_ten')}
									</span>
								</div>
							</div>
						
						
							<div class="note">
								<i class="text-danger">
									${nhMain.getLabel('luu_y')}:
									${nhMain.getLabel('ten_tep_hoac_thu_muc_se_duoc_dinh_dang_lai_loai_bo_dau_va_ky_tu_dac_biet')}
								</i>
							</div>
						</div>`;
				break;

				case 'delete-confirm':
					var title = nhMain.getLabel('xoa_tep_tin');

					var itemsDeleteHtml = '';
					// hiển thị 5 tệp chọn để xóa
					$.each(files, function(index, file) {
						if(index > 4) return;
					  	itemsDeleteHtml += `<li>${file.filename}</li>`;
					});
					if(files.length > 5){
						var moreFile = files.length - 5;
						itemsDeleteHtml += `+ ${moreFile} ${nhMain.getLabel('tep_khac')}`;
					}

					bodyHtml = `
						<div class="info-delete-confirm">
							<div class="note">
								${nhMain.getLabel('ban_co_chac_chan_muon_xoa_thu_muc_nay')}
							</div>
							<div class="name">
								<ul>
									${itemsDeleteHtml}
								</ul>
							</div>
							<div class="btn-action">
								<a href="javascript:;" rel="modal:close" class="btn btn-cancel">
									${nhMain.getLabel('huy')}
								</a>

								<span nh-btn-confirm="${type_modal}" shortcut="13" class="btn btn-primary ml-10">
									${nhMain.getLabel('dong_y')}
								<span>
							</div>
						</div>`;
				break;

				case 'delete':
					var title = nhMain.getLabel('dang_xoa') + '...';
					
					var numberFile = files.length;
					var percentProgress = 0;

					bodyHtml = `
						<div class="info-delete">
							<div class="title-file-popup">
								<span class="title">
									${nhMain.getLabel('dang_xoa')}
								</span>
								<span nh-count-file>0</span> /
								<span>${numberFile} ${nhMain.getLabel('tep')}</span>
							</div>

							<div class="progress-all">
								<div class="progress-bar hover animate">
									<span class="progress-bar-value" style="width: 0%"><span></span></span>
								</div>
							</div>

							<div class="log-modal">
								<div nh-wrap-log="delete" class="log-result d-none">
								</div>
							</div>
						</div>`;
				break

				case 'paste':
					var title = nhMain.getLabel('dang_sao_chep') + '...';
					
					var numberFile = files.length;
					var percentProgress = 0;

					bodyHtml = `
						<div class="info-paste">
							<div class="title-file-popup">
								<span class="title">
									${nhMain.getLabel('dang_sao_chep')}
								</span>
								<span nh-count-file>0</span> /
								<span>${numberFile} ${nhMain.getLabel('tep')}</span>
							</div>
							
							<div class="progress-all">
								<div class="progress-bar animate">
									<span class="progress-bar-value" style="width: 0%"><span></span></span>
								</div>
							</div>
							<div class="log-modal">
								<div nh-wrap-log="paste" class="log-result d-none"></div>
							</div>

							<div class="btn-action">
								<a href="javascript:;" rel="modal:close" class="btn btn-cancel d-inline-block px-25">
									${nhMain.getLabel('dong')}
								</a>
							</div>
						</div>`;
				break

				case 'upload':
					var title = nhMain.getLabel('dang_tai_len') + '...';
					var classModal = 'modal-lg';
					var numberFile = files.length;
					var percentProgress = 0;

					bodyHtml = `
						<div class="info-upload">
							<div class="title-file-popup">
								<span class="title">
									${nhMain.getLabel('tai_len')}
								</span>
								<span nh-count-file>0</span> /
								<span>${numberFile} ${nhMain.getLabel('tep')}</span>
							</div>

							<div class="file-upload">
								<span nh-file-upload></span>
							</div>

							<div class="progress-all">
								<div class="progress-bar animate">
									<span class="progress-bar-value" style="width: 0%"><span></span></span>
								</div>
							</div>

							<div class="log-modal">
								<div nh-wrap-log="paste" class="log-result d-none">

								</div>
							</div>

							<div class="btn-bottom d-none">
								<a class="btn btn-cancel d-inline-block px-25" rel="modal:close">${nhMain.getLabel('dong')}</a>
							</div>
						</div>`;
				break

				case 'logs':
					var title = nhMain.getLabel('lich_su_cap_nhat');
					var classModal = 'modal-xl';

					bodyHtml = `
						<div class="item-log-top">
							<div class="form-group">
								<input nh-input="log-date-from" nh-datepicker placeholder="${nhMain.getLabel('tu_ngay')}" value="" />
							</div>
							<div class="form-group">
								<input nh-input="log-date-to" nh-datepicker placeholder="${nhMain.getLabel('den_ngay')}" value="" />
							</div>
							<div class="form-group">
								<select nh-select="log-action">
									<option value="">
								  		-- ${nhMain.getLabel('hanh_dong')} --
								  	</option>
								  	<option value="UPLOAD">
								  		${nhMain.getLabel('tai_len')}
								  	</option>
								  	<option value="DELETE">
								  		${nhMain.getLabel('xoa')}
								  	</option>
								  	<option value="RENAME">
								  		${nhMain.getLabel('doi_ten')}
								  	</option>
								  	<option value="COPY">
								  		${nhMain.getLabel('dan')}
								  	</option>
								  	<option value="CUT">
								  		${nhMain.getLabel('cat')}
								  	</option>
								</select>
							</div>

							<div class="form-group">
								<input nh-input="log-fullname" placeholder="${nhMain.getLabel('nguoi_thuc_hien')}" value="" />
							</div>

							<div class="form-group group-btn">
								<div class="btn-log">
									<span nh-btn="log-filter" class="btn btn-color-hover">
										${nhMain.getLabel('tim_kiem')}
									</span>
								</div>
								<div class="btn-log">
									<span nh-btn="clear-log-filter" class="btn btn-primary">
										${nhMain.getLabel('lam_moi')}
									</span>
								</div>
							</div>
						</div>

						<div class="table-list-log">
							<table nh-table="logs">
							  	<thead>
							    	<tr>
							      		<th>
							      			${nhMain.getLabel('loai')}
							      		</th>
							      		<th>
							      			${nhMain.getLabel('thoi_gian')}
							      		</th>
							      		<th>
							      			${nhMain.getLabel('hanh_dong')}
							      		</th>
							    	</tr>
							  	</thead>
						  		<tbody>

							  	</tbody>
							</table>
						</div>`;
				break

				case 'view-link':
					var title = nhMain.getLabel('duong_dan_tep');

					bodyHtml = `
						<div class="info-modal">
							<div class="info-name-action">
								<div class="form">
									<input nh-input="${type_modal}" value="${location.origin}${file_url}" readonly />
								</div>
								<div class="btn-action">
									<span nh-btn="${type_modal}" shortcut="13" class="btn btn-primary ml-10">
										${nhMain.getLabel('sao_chep')}
									</span>
								</div>
							</div>
						</div>`;
				break;
			}

			var htmlModal = `
				<div nh-modal="${type_modal}" class="modal modal-action ${classModal}">
					<div class="header-modal">
						<div class="title-modal">
							${title}
						</div>
					</div>
					<div class="body-modal">
						${bodyHtml}
					</div>
				</div>`;

			return htmlModal;
		}
	}
}

$(document).ready(function() {
    fileManager.init();
});

