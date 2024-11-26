"use strict";

var nhTemplateModify = function () {
	var ktTree = {
		ext: null,
		path: null,
		load_folder: $("#load-folder"),
		init: function() {
			var self = this;
			self.readFolder();

			$(document).on('click', '.jstree-clicked', function() {
				self.path = $(this).attr('data_file_path');
				self.ext = $(this).attr('data_file_ext');
				if(typeof(self.path) != _UNDEFINED && self.ext.length > 0){
					self.loadFile(self.path, self.ext);
				}
			});

			$(document).on('click', '.btn-save-file', function(e) {
				e.preventDefault();
				self.saveFile();
			});

			// rename file jstree
	        self.load_folder.on(
		        "rename_node.jstree", function(evt, data){
		        	if(data.node.original.a_attr.data_file_path){
		        		var path = data.node.original.a_attr.data_file_path;
		        	} else {
		        		var path = data.node.original.a_attr.data_folder_path;
		        	}
		            nhMain.callAjax({
			            url: adminPath + '/template/modify/rename-file',
			            type: 'POST',
			            data: {
			            	new_name: data.text,
			            	old_name: data.old,
			            	path: path,
			            }
			        }).done(function(response) {
			            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
			            var data = typeof(response.data) != _UNDEFINED ? response.data : '';

			            if (code == _SUCCESS) {
			            	self.refreshFolder();
			            	toastr.info(message);
			            } else {
		                    toastr.error(message);
		                }         
			        })
		        }
			);

			// delete file jstree
			self.load_folder.on(
		        'delete_node.jstree', function(evt, data){
		        	var type = data.node.original.a_attr.type;
		        	var path = null;
		        	if(data.node.original.a_attr.data_file_path){
		        		path = data.node.original.a_attr.data_file_path;
		        	} else {
		        		path = data.node.original.a_attr.data_folder_path;
		        	}

		        	return false;
		        	swal.fire({
				        title: nhMain.getLabel('xoa_tep_tin'),
				        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_tep_nay'),
				        type: 'warning',
				        
				        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
				        confirmButtonClass: 'btn btn-sm btn-danger',

				        showCancelButton: true,
				        cancelButtonText: nhMain.getLabel('huy_bo'),
				        cancelButtonClass: 'btn btn-sm btn-default'
				    }).then(function(result) {
				    	if(typeof(result.value) != _UNDEFINED && result.value){
				    		nhMain.callAjax({
					            url: adminPath + '/template/modify/delete-file',
					            type: 'POST',
					            data: {
					            	path: path,
					            	type: type
					            }
					        }).done(function(response) {
					            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
					            var data = typeof(response.data) != _UNDEFINED ? response.data : '';

					            if (code == _SUCCESS) {
					            	toastr.info(message);
					            } else {
				                    toastr.error(message);
				                }
					        });
				    	}    	
				    });
		        }
			);

			// show full screen
			$(document).on('click', '[nh-btn="full-screen-editor"]', function(e) {
				if(!$('#editor-template').hasClass('ace_editor')) return;
				$('#editor-template').addClass('full-screen-editor');
			});

			// remove full screen
			$(document).on('keydown', function(e) {
				if($('#editor-template').hasClass('full-screen-editor') && e.key == 'Escape'){
					$('#editor-template').removeClass('full-screen-editor');
				};
			});
		},
		readFolder: function() {
			var self = this;
			KTApp.blockPage(blockOptions);
			nhMain.callAjax({
                url: adminPath + '/template/modify/read-folder',
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';

                toastr.clear();
                if (code == _SUCCESS) {
                	KTApp.unblockPage();
			        self.load_folder.jstree({
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
			            		var tree = $('#load-folder').jstree(true);
			            		if ($node.a_attr.type === 'file') return self.fileContextMenu($node, tree);
                            	else return self.folderContextMenu($node, tree);
			            	}
			            	
			            },
			        });
                } else {
                    toastr.error(message);
                }   
            });
		},
		refreshFolder: function() {
			var self = this;
			nhMain.callAjax({
                url: adminPath + '/template/modify/read-folder',
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';

                toastr.clear();
                if (code == _SUCCESS) {
                	self.load_folder.jstree(true).settings.core.data = data;
					self.load_folder.jstree(true).refresh();
                } else {
                    toastr.error(message);
                }   
            });
		},
		fileContextMenu: function($node, tree){
			return {
				'upload': {
		            label: nhMain.getLabel('tai_len'),
		            action: function (obj) {
		            	$('#upload-modal').modal('show');
		            	$('#path').val($node.a_attr.data_file_path);
                    }
		        },
		        'download': {
		            label: nhMain.getLabel('tai_xuong'),
		            action: function (obj) {
		            	window.location.href = adminPath + '/template/modify/download-file?path=' + $node.original.a_attr.data_file_path;
                    }
		        },
		        'rename': {
		            label: nhMain.getLabel('doi_ten'),
		            action: function (obj) {
                        tree.edit($node);
                    }
		        },
		        'remove': {
                    label: nhMain.getLabel('xoa'),
                    action: function (obj) {
                    	var reference = obj.reference || null;
                    	if(reference == null) return;

			        	var path = reference.attr('data_file_path');
			        	var type = reference.attr('type');

			        	if(typeof(path) == _UNDEFINED || typeof(type) == _UNDEFINED) return;

			        	swal.fire({
					        title: nhMain.getLabel('xoa_tep_tin'),
					        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_tep_nay'),
					        type: 'warning',
					        
					        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
					        confirmButtonClass: 'btn btn-sm btn-danger',

					        showCancelButton: true,
					        cancelButtonText: nhMain.getLabel('huy_bo'),
					        cancelButtonClass: 'btn btn-sm btn-default'
					    }).then(function(result) {
					    	if(typeof(result.value) != _UNDEFINED && result.value){
					    		nhMain.callAjax({
						            url: adminPath + '/template/modify/delete-file',
						            type: 'POST',
						            data: {
						            	path: path,
						            	type: type
						            }
						        }).done(function(response) {
						            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
						            var data = typeof(response.data) != _UNDEFINED ? response.data : '';

						            if (code == _SUCCESS) {
						            	toastr.info(message);

						            	tree.delete_node($node);
						            } else {
					                    toastr.error(message);
					                }
						        });
					    	}    	
					    });
                    	
                    }
                }
			}
		},
		folderContextMenu: function($node, tree){
			return {
				'upload': {
		            label: nhMain.getLabel('tai_len'),
		            action: function (obj) {
		            	$('#upload-modal').modal('show');
		            	$('#path').val($node.a_attr.data_folder_path);
                    }
		        }		       
			}
		},
		loadFile: function(_path, _ext) {
			KTApp.blockPage(blockOptions);
			var fileName = _path.split('\\')[_path.split('\\').length - 1];
			var editor = ace.edit('editor-template');
			editor.setTheme('ace/theme/monokai');
			if(typeof _ext != _UNDEFINED && _ext.length > 0) {
				switch(_ext) {
					case 'css':
					case 'po':
						var mode = ace.require('ace/mode/css').Mode;
						break;

					case 'js':
						var mode = ace.require('ace/mode/javascript').Mode;
                        break;

                    case 'php':
                    	var mode = ace.require('ace/mode/php').Mode;
                        break;
                    case 'tpl':
                    	var mode = ace.require('ace/mode/smarty').Mode;
                        break;
                    default:
                    	var mode = ace.require('ace/mode/html').Mode;
                        break;
				}
			}

			editor.session.setMode(new mode());
			editor.setShowPrintMargin(false);

			ace.require('ace/ext/language_tools');
			editor.setOptions({
		        enableBasicAutocompletion: true,
		        enableSnippets: true,
		        enableLiveAutocompletion: true,
	        	fontSize: "14px",
	        	minLines: 40,
	        	maxLines: 40
		    });

		    nhMain.callAjax({
	            url: adminPath + '/template/modify/load-file',
	            type: 'POST',
	            data: {
	            	path: _path
	            }
	        }).done(function(response) {
				KTApp.unblockPage();
	            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	            var data = typeof(response.data) != _UNDEFINED ? response.data : '';

	            if (code == _SUCCESS) {
	            	$('#path-file').val(_path);
	            	if(data.length > 0){
	                	editor.setValue(data);
	                } else{
	                	editor.setValue("");
	                }

	                if(typeof(nhViewLogFile) != _UNDEFINED && typeof(nhViewLogFile.btnViewElement) != _UNDEFINED){
	            		nhViewLogFile.btnViewElement.attr('data-path', _path);
	            	}

	            } else {
	            	editor.destroy();
                    toastr.error(message);
                }         
	        })
		},
		saveFile: function() {			
			KTApp.blockPage(blockOptions);

			var editor = ace.edit('editor-template');
			nhMain.callAjax({
                url: adminPath + '/template/modify/save-file',
                data: {
                	content_file: editor.getValue(),
                	path: $('#path-file').val()
                }
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';
                toastr.clear();

                KTApp.unblockPage();

                if (code == _SUCCESS) {
                    toastr.info(message);
                } else {
                    toastr.error(message);
                }            
            })
		}
    }

	return {
		init: function() {		 
			ktTree.init();
			nhMain.selectMedia.dropzoneUpload({
				id: 'uploadFile',
                url: adminPath + '/template/modify/upload-file',
                ext: '.css, .js, .tpl, .po, .json, .jpeg, .jpg, .png, .gif, .ttf, .eot, .woff, .svg, .woff2',
				maxFile: 5,
			}, function(){
				ktTree.refreshFolder();
			}, function(){});
		}
	};
}();

$(document).ready(function() {
	nhTemplateModify.init();
});