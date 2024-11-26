"use strict";

var nhConfigByOrder = {
	validate: false,
	htmlRow: '',
	table: $('table#table-config-by-order'),
	data: {},
	init: function(){
		var self = this;
		if(self.table.length == 0) return;
		self.htmlRow = self.table.find('tbody tr:first-child')[0].outerHTML;
		if(self.htmlRow == _UNDEFINED || self.htmlRow.length == 0) return;

		self.event();

		nhMain.tinyMce.simple();

		self.table.find('tbody tr').each(function(index, tr) { 
		   self.initInputRow($(tr));
		});
	},
	event: function(){
		var self = this;

		$(document).on('click', '[btn-action="add-item"]', function(e) {
            e.preventDefault();            
            self.table.find('tbody').append(self.htmlRow);
            var rowElement = self.table.find('tbody tr:last-child');

            self.clearRow(rowElement);
            self.initInputRow(rowElement);
        });

        $(document).on('click', '[btn-action="remove-item"]', function(e) {
            e.preventDefault();

            $(this).closest('tr').remove();
        });
	},
	clearRow: function(rowElement = null){
		var self = this;
		if(rowElement == null || rowElement.length == 0) return;

		rowElement.find('input').val('');
	},
	initInputRow: function (rowElement = null){
		var self = this;
		if(rowElement == null || rowElement.length == 0) return;

		rowElement.find('select.select2-multile-select').select2();

		rowElement.find('input.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});
	},
	getDataConfig: function(){
		var self = this;

		self.data = {};
		self.table.find('tbody tr').each(function(index, tr) { 
			self.data[index] = {
				order_from: $(this).find('input[name="order_from"]').val(),
				order_to: $(this).find('input[name="order_to"]').val(),
				order_location: $(this).find('select[name="order_location[]"]').val(),
				order_shipping_fee: $(this).find('input[name="order_shipping_fee"]').val()
			};
		});
	}
}

var nhShippingMethod = {
	formEl: $('form#main-form'),
	init: function(){
		var self = this;

		if(self.formEl.length == 0) return;

		var validator = self.formEl.validate({
			ignore: ":hidden",
			rules: {
				name: {
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
			},
		});

		nhMain.input.inputMask.init($('input[name="general_shipping_fee"]'), 'number');
		nhMain.tinyMce.simple();

		$(document).on('click', '#btn-save', function(e) {
			e.preventDefault();
			if (validator.form()) {
				nhConfigByOrder.getDataConfig();
				self.formEl.find('input[name="custom_config"]').val(JSON.stringify(nhConfigByOrder.data));

				$('.mce-editor-simple').each(function(index, item) {
					var inputId = $(this).attr('id');
					$('#' + inputId).val(tinymce.get(inputId).getContent());  	
				});
				
				nhMain.initSubmitForm(self.formEl, $(this));
			}
		});
	}
}

$(document).ready(function() {
	nhConfigByOrder.init();
    nhShippingMethod.init();
});