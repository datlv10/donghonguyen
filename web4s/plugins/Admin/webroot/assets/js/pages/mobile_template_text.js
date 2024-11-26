"use strict";

var formEl;
var initSubmit = function() {
	$(document).on('click', '.btn-save', function(e) {
		e.preventDefault();
		nhMain.initSubmitForm(formEl, $(this));
	});
}

var loadValueItem = function(item) {
	item.find("#wrap-text .wrap-item").each(function (index) {
		var code = $.trim($(this).find('[label-code]').html());
		var text = $.trim($(this).find('[label-text]').html());
		
		if (typeof(code) != _UNDEFINED && typeof(text) != _UNDEFINED) {
			$(this).find('input[input-value]').val(JSON.stringify({
				code: code,
				text: text
			}));
		}
		
	})
}

var textMobileTemplate = {
	idWrap: '#wrap-text',
	classItem: '.wrap-item',
	itemHtml: null,
	modal: null,
	init: function(options = {}){
		formEl = $('#main-form');	
		var self = this;
		self.itemHtml = $(self.idWrap + ' ' + self.classItem + ':first-child').length ? $(self.idWrap + ' ' + self.classItem + ':first-child')[0].outerHTML : '';
		self.modal = $('#text-info-modal');
		if(self.modal.length == 0) return;

		$(document).on('click', '#add-new-text', function(e) {
			self.showModal();
		});

		$(document).on('click', self.idWrap + ' [btn-delete]', function(e) {
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
		    	}
		    });
		});

		$(document).on('click', self.idWrap + ' ' + self.classItem +  ' [btn-edit]', function(e) {
			var item = $(this).closest(self.classItem);

			var index = item.index();
			var value = nhMain.utilities.parseJsonToObject(item.find('input[input-value]').val());
			var code =  typeof(value.code) != _UNDEFINED ? value.code : null;
			var text = typeof(value.text) != _UNDEFINED ? value.text : null;

			self.showModal(index, code, text);
		});


		$(document).on('click', '#btn-save-text', function(e) {
			var code = self.modal.find('input#code').val();
			var text = self.modal.find('input#text').val();
			var index = self.modal.find('input#index').val();

			if (typeof(code) == _UNDEFINED || code == '') {
				toastr.error(nhMain.getLabel('ma_nhan_khong_duoc_de_trong'));
    			return false;
    		}

    		if (typeof(text) == _UNDEFINED || text == '') {
				toastr.error(nhMain.getLabel('noi_dung_khong_duoc_de_trong'));
    			return false;
    		}

			var check = self.checkCodeTextExit(code, index);
			
			if (typeof(check) != _UNDEFINED && check == false) {
				toastr.error(nhMain.getLabel('ma_nhan_da_ton_tai_tren_he_thong'));
    			return false;
    		}

			var item = null;
			if(!index.length > 0){
				self.addNewItem();
				item = $(self.idWrap).find(self.classItem + ':last-child');
			}else{
				item = $(self.idWrap).find(self.classItem).eq(index);
			}

			self.setValueItem(item, code, text);
			self.modal.modal('hide');

		});
		loadValueItem(formEl);
		initSubmit();

	},
	addNewItem: function(){
		var self = this;
		
		$(self.idWrap).append(self.itemHtml);
		var item = $(self.idWrap).find(self.classItem + ':last-child');
		self.clearItem();
	},
	clearItem: function(item = null){
		var self = this;

		if(!nhMain.utilities.notEmpty(item) || item.length == 0) return;

		item.find('[label-code]').text('');
		item.find('[label-text]').text('');

		item.find('input[input-value]').val('');
	},
	setValueItem: function(item = null, code = null, text = null){
		var self = this;

		if(!nhMain.utilities.notEmpty(item) || item.length == 0) return;
		if(!nhMain.utilities.notEmpty(code) || !nhMain.utilities.notEmpty(text)) return;

		item.find('[label-code]').text(code);
		item.find('[label-text]').text(text);

		item.find('input[input-value]').val(JSON.stringify({
			code: code,
			text: text
		}));
		
	},
	showModal: function(index = null, code = null, text = null){
		var self = this;
		self.modal.modal('show');
		self.modal.find('input').val('');

		self.modal.find('input#index').val(index);
		if(nhMain.utilities.notEmpty(code)){
			self.modal.find('input#code').val(code);
		}

		if(nhMain.utilities.notEmpty(code)){
			self.modal.find('input#text').val(text);
		}		
	},

	checkCodeTextExit: function(code = null, index = null) {
		var check = true;
		$("#wrap-text .wrap-item [label-code]").each(function (i) {
			if(i == index) return;

			var code_new = $.trim($(this).html());    		
    		if (typeof(code) != _UNDEFINED && typeof(code_new) != _UNDEFINED && code == code_new) {
    			check = false;
    			return false;
    		}    		
    	});
    	
    	return check;
	}
}


$(document).ready(function() {
	textMobileTemplate.init();
});
