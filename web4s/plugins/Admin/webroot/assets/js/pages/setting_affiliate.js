"use strict";

var nhAffiliate = {
	form: $('#main-form'),
	wrapElement: '#list-level',
	classItem: '.wrap-item',
	itemHtml: null,
	init: function(params = {}){
		var self = this;

		if(self.form.length == 0) return false;
		if(self.wrapElement.length == 0) return false;

		self.itemHtml = $(self.wrapElement + ' ' + self.classItem + ':first-child').length ? $(self.wrapElement + ' ' + self.classItem + ':first-child')[0].outerHTML : '';

		$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});		

		self.event();
	},
	event: function(){
		var self = this;

		$(self.wrapElement).find(self.classItem).each(function(index) {
		  	self.initInputItem($(this));
		});

		$(document).on('click', '#add-new-level', function(e) {
			self.addNewItem();
		});

		$(document).on('click', '.btn-delete-item', function(e) {
			var itemElement = $(this).closest('.wrap-item');
			var tinyMceId = itemElement.find('textarea.mce-editor-simple').attr('id');

			tinymce.get(tinyMceId).remove();

			itemElement.remove();
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			$('textarea.mce-editor-simple').each(function (index) {
				$('#description_' + index).val(tinymce.get('description_'+index).getContent());
			});

			nhMain.initSubmitForm($(this).closest('form'), $(this));
		});
	},
	addNewItem: function(){
		var self = this;

		$(self.wrapElement).append(self.itemHtml);
		var item = $(self.wrapElement).find(self.classItem + ':last-child');
		nhMain.tinyMce.simple();

		self.clearDataItem(item);
		self.initInputItem(item);
	},
	replaceNameInput: function(item){
		var self = this;
		var indexItem = $(self.classItem).index(item[0]);

		$('input, textarea.mce-editor-simple', item).each(function () {
			if (typeof($(this).attr('data-name')) == _UNDEFINED) return;
			var name = 'commissions[' + indexItem + ']['+ $(this).attr('data-name') +']';
			$(this).attr('name', name);
		});

		$('textarea.mce-editor-simple', item).each(function () {
			$(this).attr('id', 'description_'+ indexItem);
		});

		$('#key_level', item).val(indexItem);

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
	initInputItem: function(item = null){
		var self = this;
		if(item == null || item.length == 0) return;

		// replace name input
		self.replaceNameInput(item);
		selectMedia.init();

		$('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		nhMain.tinyMce.simple();
	},
	clearDataItem: function(item = null){
		if(item == null || item.length == 0) return;
		var self = this;
		item.find('.kt-avatar__holder').css('background-image', '');

		$('input', item).each(function () {
			$(this).val('');
		});

		$('textarea.mce-editor-simple', item).each(function () {
			$(this).val('');
			$(this).html('');
		});
	}
};

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

$(document).ready(function() {
	nhAffiliate.init();
});
