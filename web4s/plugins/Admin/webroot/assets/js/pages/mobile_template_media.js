"use strict";

var copyMedia = false;
var previewMedia = false;

var selectMedia = {
	type : null,
	copy: false,
	preview: false,
	init: function(params = {}){
		var self = this;

		if($('[btn-select-media-block]').length == 0) return false;

		if(typeof(params.copy) != _UNDEFINED && params.copy){
			self.copy = true;
		}

		if(typeof(params.preview) != _UNDEFINED && params.preview){
			self.preview = true;
		}

		$('[btn-select-media-block]').fancybox({
		   	closeExisting: true,
		   	iframe : {
		   		preload : false
		   	}
		});

		$(document).on('click', '[btn-select-media-block]', function(e) {
			self.resetOption();

			self.type = $(this).attr('btn-select-media-block');
			if($(this).attr('action') != _UNDEFINED && $(this).attr('action') == 'copy'){
				self.copy = true;
				copyMedia = true;
			}

			if($(this).attr('action') != _UNDEFINED && $(this).attr('action') == 'preview'){
				self.preview = true;
				previewMedia = true;
			}

			$(window).on('message', self.onSelectImage);
	    });

	    $(document).on('click', '.btn-clear-image', function(e) {
	    	var wrap = $(this).closest('.kt-avatar');
	    	wrap.find('.kt-avatar__holder').css('background-image', '');
	    	wrap.removeClass('kt-avatar--changed');
	    	wrap.find('input[type="hidden"]').val('');
	    });  
	},
	resetOption: function(){
		var self = this;

		self.copy = false;
		self.preview = false;

		copyMedia = false;
		previewMedia = false;
	},
	onSelectImage: function(e){
		var self = selectMedia;
		var event = e.originalEvent;
	   	if(event.data.sender === 'myfilemanager'){
	      	if(event.data.field_id){
	      		var field_id = event.data.field_id;
	      		var inputImage = $('#' + field_id);
	      		var inputSource = $('[block-image-source="' + field_id + '"]');

	      		var imageUrl = typeof(event.data.url) != _UNDEFINED ? event.data.url : null;			      		   	

				if(self.preview){
					self.previewImage(imageUrl, field_id);
				}
				
				// replace url image before set value for input
				if(isArray(imageUrl)){
					imageUrl = imageUrl[0];
				}
				imageUrl = imageUrl.replace(cdnUrl, '');


				// set value for input					
	      		if(inputImage.length > 0){
	      			inputImage.val(imageUrl);
	      		}

	      		if(inputSource.length > 0){
	      			inputSource.val('cdn');
	      		}

	      		if(self.copy){
	      			// imageUrl = '{CDN_URL}' + imageUrl;
					self.copyImage(imageUrl);
				}
				
				$.fancybox.close();
				$(window).off('message', self.onSelectImage);
	      	}
	   	}
	},
	copyImage: function(imageUrl = null, type = null){
		var self = this;

		var inputTmp = $('<input>');
		$('body').append(inputTmp);
		inputTmp.val(imageUrl).select();
		document.execCommand('copy');
		inputTmp.remove();

		toastr.success(nhMain.getLabel('da_copy_duong_dan_anh'));
	},
	previewImage: function(imageUrl = null, field_id = null){
		$('[block-preview-image="'+ field_id +'"]').find('.kt-avatar__holder').css('background-image', 'url("' + imageUrl + '")');
		$('[block-preview-image="'+ field_id +'"]').addClass('kt-avatar--changed');
	}
}


var formEl;
var initSubmit = function() {
	$(document).on('click', '.btn-save', function(e) {
		e.preventDefault();
		nhMain.initSubmitForm(formEl, $(this));
	});
}

var nhMobileMedia = {
	idWrap: '#wrap-item-config',
	classItem: '.wrap-item',
	itemHtml: null,
	init: function(options = {}){
		formEl = $('#main-form');	

		var self = this;
		self.itemHtml = $(self.idWrap + ' ' + self.classItem + ':first-child').length ? $(self.idWrap + ' ' + self.classItem + ':first-child')[0].outerHTML : '';

		$(self.idWrap).find(self.classItem).each(function(index) {
		  	self.initInputItem($(this));
		});

		self.clearAllErrorItem();

		$(document).on('click', '#add-item', function(e) {
			self.addNewItem();
		});

		$(document).on('click', self.idWrap + ' .btn-delete-item', function(e) {
			var item = $(this).closest(self.classItem);
			swal.fire({
		        title: nhMain.getLabel('xoa_ban_ghi'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_ban_ghi_nay'),
		        type: 'warning',
		        
		        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
		    		item.remove();
		    		$('.wrap-item:last-child .kt-portlet__head-group').html('<span class="btn btn-sm btn-icon btn-danger btn-icon-md m-0 btn-delete-item"><i class="la la-trash-o"></i></span>'); 
		    	}
		    });
		});

		$(document).on('keyup', self.idWrap + ' ' + self.classItem +  ' input.item-name', function(e) {
			var name = $(this).val();
			if(name.length == 0){
				name = 'New Item';
			}

			var item = $(this).closest(self.classItem);
			item.find('.kt-portlet__head-title').text(name);
		});		

		$(document).on('click', '.btn-clear-image', function(e) {
	    	var wrap = $(this).closest('.kt-avatar');
	    	wrap.find('.kt-avatar__holder').css('background-image', '');
	    	wrap.removeClass('kt-avatar--changed');
	    	wrap.find('input[type="hidden"]').val('');
	    }); 

	    $('.wrap-item:not(:last-child) .btn-delete-item').remove(); 

		initSubmit();       
	},
	initInputItem: function(item = null){
		var self = this;
		if(item == null || item.length == 0) return;

		var indexItem = $(self.classItem).index(item[0]);

		// replace name input
		self.replaceNameInput(item);

	    selectMedia.init();
	},
	replaceNameInput: function(item){
		var self = this;
		var indexItem = $(self.classItem).index(item[0]);
		var number = indexItem + 1;
		$('input, select, textarea', item).each(function () {
			if (typeof($(this).attr('data-name')) == _UNDEFINED) return;
			var name = indexItem + '['+ $(this).attr('data-name') +']';
			$(this).attr('name', name);
		});

		// repace title item
		var imageCode = item.find('input[data-name="code"]').val();
		if(imageCode.length == 0){
			imageCode = 'image_' + number;
		}
		item.find('.header-item').text(imageCode);
		item.find('input[data-name="code"]').val(imageCode);

		// replace data-url and id of input select image
		item.find('[btn-select-media-block]').each(function( index ) {
			var dataSrc = $(this).data('src');
			$(this).data('src', dataSrc + '_' + indexItem);
		});

		var inputImage = item.find('.input-select-image');			
		var _id = inputImage.attr('id');
		
		inputImage.attr('id', _id + '_' + indexItem);
		item.find('[block-preview-image]').attr('block-preview-image', _id + '_' + indexItem);
		item.find('[block-image-source]').attr('block-image-source', _id + '_' + indexItem);
	},
	addNewItem: function(){
		var self = this;

		$(self.idWrap).append(self.itemHtml);
		var item = $(self.idWrap).find(self.classItem + ':last-child');

		self.clearDataItem(item);
		self.initInputItem(item);
	},
	clearDataItem: function(item = null){
		if(item == null || item.length == 0) return;
		var self = this;

		item.find('.kt-portlet__head-title').text('New Item');

		$('input, select, textarea', item).each(function () {
			var typeInput = $(this).attr('type');
			if(typeInput == 'checkbox'){
				$(this).prop('checked', false);
			}else{
				$(this).val('');
			}
		});

		var wrapImage = item.find('.kt-avatar');
		wrapImage.removeClass('kt-avatar--changed');
		wrapImage.find('.kt-avatar__holder').css('background-image', '');
	},
	showErrorItem: function(item = null){
		var self = this;
		if(item == null || item == _UNDEFINED || item.length == 0) return;
		item.addClass('item-error');
		KTUtil.scrollTo(item[0], nhMain.validation.offsetScroll);
	},
	clearAllErrorItem: function(){
		var self = this;
		$(self.idWrap).find(self.classItem).removeClass('item-error');
	}
}


$(document).ready(function() {
	nhMobileMedia.init();
});
